<?php
/**
 * File that define P24_Config_Checker class.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class that check config.
 */
class P24_Config_Checker {

	const ERROR_NONE_ID         = 0;
	const ERROR_X_ID            = 16;
	const ERROR_AUTH_ID         = 32;
	const ERROR_REGISTRATION_ID = 64;

	const ERROR_AUTH_TXT         = 'Incorrect authentication';
	const ERROR_REGISTRATION_TXT = 'given merchant has unfinished registration process';

	/**
	 * Core plugin instance.
	 *
	 * @var P24_Core
	 */
	private $core;

	/**
	 * Normal constructor.
	 *
	 * @param P24_Core $core Plugin core.
	 */
	public function __construct( P24_Core $core ) {
		$this->core = $core;
	}

	/**
	 * Bind events.
	 *
	 * @return void
	 */
	public function bind_events() {
		add_action( 'rest_api_init', array( $this, 'on_rest_api_init' ) );
	}

	/**
	 * Generate temporary config.
	 *
	 * @param array $params Array of params.
	 * @return P24_Config_Accessor
	 */
	private function generate_temporary_config( $params ) {
		$holder   = new P24_Config_Holder();
		$accessor = new P24_Config_Accessor( 'PLN', $holder );
		$accessor
			->set_p24_operation_mode( $params['mode'] ?? 'sandbox' )
			->set_merchant_id( $params['merchant_id'] ?? '0' )
			->set_shop_id( $params['shop_id'] ?? '0' )
			->set_salt( $params[ 'crc_key' ?? '0' ] )
			->set_p24_api( $params['api_key'] ?? '0' )
			->access_mode_to_strict();

		return $accessor;
	}

	/**
	 * Generate fake payload.
	 *
	 * @param P24_Config_Accessor $config Temporary config to use.
	 * @return array
	 * @throws Exception Not expected, the provided data should be valid.
	 */
	private function generate_fake_payload( P24_Config_Accessor $config ) {
		return array(
			'sessionId'   => 'fake_session_' . bin2hex( random_bytes( 10 ) ),
			'posId'       => (int) $config->get_shop_id(),
			'merchantId'  => (int) $config->get_merchant_id(),
			'amount'      => 100,
			'currency'    => $config->get_currency(),
			'email'       => 'p24@example.com',
			'urlReturn'   => 'http://example.com',
			'description' => 'Config Test',
			'country'     => 'Polska',
		);
	}

	/**
	 * Extract error.
	 *
	 * @param string $error_text Error text.
	 * @return int
	 */
	private function extract_error( $error_text ) {
		switch ( $error_text ) {
			case self::ERROR_AUTH_TXT:
				return self::ERROR_AUTH_ID;
			case self::ERROR_REGISTRATION_TXT:
				return self::ERROR_REGISTRATION_ID;
			default:
				return self::ERROR_X_ID;
		}
	}

	/**
	 * Do a standard check.
	 *
	 * @param P24_Config_Accessor $config Temporary config.
	 * @return int
	 */
	private function do_request_check( P24_Config_Accessor $config ) {
		$rest      = new P24_Rest_Common( $config );
		$res       = $rest->test_access();
		$res_error = $res['error'] ?? '';
		if ( $res_error ) {
			return $this->extract_error( $res_error );
		} else {
			return self::ERROR_NONE_ID;
		}
	}

	/**
	 * Check if registration is possible.
	 *
	 * @param P24_Config_Accessor $config Temporary config.
	 * @param string              $token Reference to pass token.
	 * @return int
	 * @throws Exception Not expected for provided data.
	 */
	private function do_request_register( P24_Config_Accessor $config, &$token ) {
		$rest      = new P24_Rest_Transaction( $config );
		$payload   = $this->generate_fake_payload( $config );
		$res       = $rest->register( $payload );
		$res_error = $res['error'] ?? '';
		if ( $res_error ) {
			return $this->extract_error( $res_error );
		}
		$token = $res['data']['token'] ?? '';
		if ( ! $token ) {
			return self::ERROR_X_ID;
		} else {
			return self::ERROR_NONE_ID;
		}
	}

	/**
	 * Check if getting token is possible.
	 *
	 * @param P24_Config_Accessor $config Temporary config.
	 * @param string              $token Provided token.
	 * @return int
	 */
	private function do_request_token( P24_Config_Accessor $config, $token ) {
		$test_mode = $config->is_p24_operation_mode( 'sandbox' );
		$url       = Przelewy24Class::get_trn_request_url_static( $token, $test_mode );

		$params = array(
			'method'      => 'POST',
			'redirection' => 0,
		);

		$response = wp_remote_request( $url, $params );
		if ( ! is_array( $response ) ) {
			return self::ERROR_X_ID;
		}

		/* This is an array like object. */
		$location = $response['headers']['location'];
		if ( $location ) {
			$query = (string) wp_parse_url( $location, PHP_URL_QUERY );
			parse_str( $query, $params );
			$message = (string) ( $params['errorMessage'] ?? '' );
			if ( preg_match( '/^unfinished_registration/', $message ) ) {
				return self::ERROR_REGISTRATION_ID;
			}
			$error = $params['error'] ?? null;
			if ( $error ) {
				return self::ERROR_X_ID;
			}
		}

		return self::ERROR_NONE_ID;
	}

	/**
	 * Extract most important error.
	 *
	 * @param int $error Error mask.
	 * @return int
	 */
	private function extract_most_important_error( int $error ) {
		if ( self::ERROR_NONE_ID === $error ) {
			return $error;
		}
		$errors_hierarchy = array(
			self::ERROR_AUTH_ID,
			self::ERROR_REGISTRATION_ID,
			self::ERROR_X_ID,
		);

		foreach ( $errors_hierarchy as $one ) {
			if ( $one & $error ) {
				return $one;
			}
		}

		/* Not expected. */
		return $error;
	}

	/**
	 * Support API request.
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response
	 */
	public function route_check_config( WP_REST_Request $request ) {
		$body = $request->get_body();
		parse_str( $body, $params );

		$config = $this->generate_temporary_config( $params );

		$error_check    = $this->do_request_check( $config );
		$error_register = $this->do_request_register( $config, $token );
		$error_url      = self::ERROR_NONE_ID;
		if ( $token ) {
			$error_url = $this->do_request_token( $config, $token );
		}

		$error_combined  = $error_check | $error_register | $error_url;
		$error_to_report = $this->extract_most_important_error( $error_combined );

		return new WP_REST_Response( $error_to_report, 200 );
	}

	/**
	 * Register API route.
	 *
	 * @return void
	 */
	public function on_rest_api_init() {
		register_rest_route(
			'p24',
			'check-config',
			array(
				'callback'            => array( $this, 'route_check_config' ),
				'methods'             => array( 'POST', 'GET' ),
				'permission_callback' => '__return_true',
			)
		);
	}
}
