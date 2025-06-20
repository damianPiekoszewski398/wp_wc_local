<?php
/**
 * File that define P24_Rest_Heavy class.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;


/**
 * Class that cache few REST requests.
 */
class P24_Rest_Heavy {


	/* Key used for cache. */
	const CACHE_KEY_PREFIX = 'p24-wc8-gateway-plugin-methods-';

	/**
	 * @var P24_Rest_Common
	 */
	private $rest_common;

	/**
	 * P24_Rest_Heavy constructor.
	 *
	 * @param P24_Config_Accessor $cf
	 */
	public function __construct( P24_Config_Accessor $cf ) {
		$this->rest_common = new P24_Rest_Common( $cf );
	}

	/**
	 * Get payment methods, use cache.
	 *
	 * @param string $lang One of supported languages (only 'pl' and 'en' for now).
	 *
	 * @return array
	 *
	 * @throws LogicException When wrong language is provided.
	 */
	public function payment_methods( $lang ) {
		if ( ! in_array( $lang, array( 'pl', 'en' ), true ) ) {
			throw new LogicException( 'The lang ' . $lang . ' is not supported.' );
		}
		$key = self::CACHE_KEY_PREFIX . $lang;
		$ret = get_transient( $key );
		if ( ! $ret ) {
			$ret = $this->rest_common->payment_methods( $lang );
			set_transient( $key, $ret, 180 );
		}

		return $ret;
	}

    /**
     * Get payment methods and reset cache.
     *
     * @param string $lang One of supported languages (only 'pl' and 'en' for now).
     *
     * @return array
     *
     * @throws LogicException When wrong language is provided.
     */
	public function payment_methods_uncached( $lang ) {
		if ( ! in_array( $lang, array( 'pl', 'en' ), true ) ) {
			throw new LogicException( 'The lang ' . $lang . ' is not supported.' );
		}
		$key = self::CACHE_KEY_PREFIX . $lang;
		$ret = $this->rest_common->payment_methods( $lang );
		set_transient( $key, $ret, 180 );

		return $ret;
	}
}
