<?php
/**
 * File that define P24_Update class.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class that support plugin update.
 */
class P24_Update {

	/* URL of the file with information. */
	const URL = 'https://www.przelewy24.pl/storage/app/media/ecommerce/P24_WooCommerce_8.ini';

	/* Key used for cache. */
	const CACHE_KEY = 'p24-wc8-gateway-plugin-info';

	/**
	 * Slug.
	 */
	const SLUG = 'p24-wc-gateway';

	/**
	 * Cache for parsed meta.
	 *
	 * @var array|null
	 */
	private $new_meta = null;

	/**
	 * Return downloaded data. The respone can be cached.
	 *
	 * @return string|null
	 */
	private function download() {
		$body = get_transient( self::CACHE_KEY );

		if ( ! $body ) {
			$r = wp_safe_remote_get( self::URL );

			/* If there is an error, ignore request. */
			if ( is_array( $r ) && 200 === $r['response']['code'] ) {
				$body = $r['body'];
				set_transient( self::CACHE_KEY, $body, 3600 );
			}
		}

		if ( $body ) {
			return $body;
		} else {
			return null;
		}
	}

	/**
	 * Parse input.
	 *
	 * @param string $input Input.
	 * @return array
	 */
	public function parse( $input ) {
		/* This is a PHP native function that generate warnings. */
		$array = @parse_ini_string( $input ); // phpcs:ignore
		if ( ! $array ) {
			return array();
		}

		$array = array_map( 'trim', $array );

		return $array;
	}

	/**
	 * Get parsed information of plugin update.
	 *
	 * @return array
	 */
	private function get_meta() {
		if ( ! $this->new_meta ) {

			$info = $this->download();
			if ( $info ) {

				$parsed = $this->parse( $info );

				$defaults = array(
					'name'         => 'WooCommerce Przelewy24 Payment Gateway',
					'author'       => 'Prelewy24',
					'new_version'  => null,
					'last_updated' => null,
					'homepage'     => 'https://www.przelewy24.pl',
					'package'      => null,
					'description'  => null,
					'changelog'    => null,
				);

				$this->new_meta = $parsed + $defaults;
			}
		}

		return $this->new_meta;
	}

	/**
	 * Check and fill information for plugin auto update.
	 *
	 * @param object $data Data about other plugins to update.
	 * @param string $name Name of the action.
	 * @return object
	 */
	public function fill_auto_update( $data, $name ) {
		if ( 'update_plugins' !== $name ) {
			return $data;
		}

		if ( ! $data ) {
			return $data;
		}

		if ( ! P24_Check_Sums::check_files() ) {
			return $data;
		}

		$meta = $this->get_meta();

		if ( ! $meta ) {
			return $data;
		}

		if ( ! $meta['new_version'] ) {
			return $data;
		}

		if ( version_compare( $meta['new_version'], P24_Core::INTERNAL_VERSION, '<=' ) ) {
			return $data;
		}

		$key                    = 'przelewy24/woocommerce-gateway-przelewy24.php';
		$data->response[ $key ] = (object) array(
			'id'          => 'przelewy24.pl/wc-gateway',
			'slug'        => self::SLUG,
			'plugin'      => $key,
			'package'     => $meta['package'],
			'new_version' => $meta['new_version'],
		);

		return $data;
	}

	/**
	 * Fill plugin information.
	 *
	 * @param object $original Original information.
	 * @param string $action Action.
	 * @param object $args Additional parameters.
	 * @return object
	 */
	public function fill_plugin_info( $original, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $original;
		}

		if ( self::SLUG !== $args->slug ) {
			return $original;
		}

		$meta = $this->get_meta();

		if ( ! $meta ) {
			return $original;
		}

		$payload = (object) array(
			'name'          => $meta['name'],
			'slug'          => self::SLUG,
			'sections'      => array(
				'description' => $meta['description'],
				'changelog'   => nl2br( $meta['changelog'] ),
			),
			'author'        => $meta['author'],
			'version'       => $meta['new_version'],
			'homepage'      => $meta['homepage'],
			'last_updated'  => $meta['last_updated'],

			'download_link' => $meta['package'],
		);

		return $payload;
	}

	/**
	 * Bind events.
	 *
	 * @return void
	 */
	public function bind_events() {
		add_filter( 'site_transient_update_plugins', array( $this, 'fill_auto_update' ), 50, 2 );
		add_filter( 'plugins_api', array( $this, 'fill_plugin_info' ), 10, 3 );
	}
}
