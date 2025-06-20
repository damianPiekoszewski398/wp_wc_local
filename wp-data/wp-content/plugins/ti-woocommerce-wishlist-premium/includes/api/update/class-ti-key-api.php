<?php
/**
 * TemplateInvaders API Manager API Key Class
 *
 * @package Update TemplateInvaders/Key Handler
 * @since             1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * TemplateInvaders API Manager API Key Class
 */
class TI_Api_Manager_Key {

	/**
	 * Software name
	 *
	 * @var string
	 */
	public $_name;

	/**
	 * Software version
	 *
	 * @var string
	 */
	public $_version;

	/**
	 * Method request.
	 * Variation:
	 * * POST
	 * * GET
	 *
	 * @var string
	 */
	private $method;

	/**
	 * Software update url
	 *
	 * @var string
	 */
	public $upgrade_url;

	/**
	 * Software Title
	 *
	 * @var string
	 */
	private $product_id;

	/**
	 * Instance ID (unique to each blog activation)
	 *
	 * @var string
	 */
	private $instance;

	/**
	 * Is sets product?
	 *
	 * @var boolean
	 */
	private $product;

	/**
	 * Constructor
	 *
	 * @param string $software_name Software name.
	 * @param string $version Software version.
	 */
	public function __construct( $software_name, $version ) {
		$this->post();
		$this->_name    = $software_name;
		$this->_version = $version;
		$this->product  = false;
	}

	/**
	 * Set product
	 *
	 * @param string $product_id Software Title.
	 * @param string $upgrade_url Software update url.
	 * @param string $instance Unique for each activation or install.
	 *
	 * @return \TI_Api_Manager_Key
	 */
	public function product( $product_id, $upgrade_url, $instance ) {
		$this->upgrade_url = $upgrade_url;
		$this->product_id  = $product_id;
		$this->instance    = $instance;
		$this->product     = true;

		return $this;
	}

	/**
	 * Set method post
	 *
	 * @return \TI_Api_Manager_Key
	 */
	public function post() {
		$this->method = 'POST';

		return $this;
	}

	/**
	 * Set method get
	 *
	 * @return \TI_Api_Manager_Key
	 */
	public function get() {
		$this->method = 'GET';

		return $this;
	}

	/**
	 * Get method
	 *
	 * @return string
	 */
	private function method() {
		return strtolower( $this->method );
	}

	/**
	 * Create api url
	 *
	 * @param array $attr URL attributes.
	 *
	 * @return string
	 */
	function create_api_url( $attr = array() ) {
		$api_url = add_query_arg( 'wc-api', 'am-software-api', $this->upgrade_url );
		if ( empty( $attr ) || ! is_array( $attr ) ) {
			return $api_url;
		}

		return $api_url . '&' . http_build_query( $attr );
	}

	/**
	 * Get domain blog a activated plugin
	 *
	 * @return string
	 */
	function get_current_domain() {
		return str_ireplace( array( 'http://', 'https://' ), '', home_url() );
	}

	/**
	 * API request
	 *
	 * @param array $attr Add request attribute.
	 *
	 * @return mixed
	 */
	private function request( $attr ) {
		if ( ! $this->product ) {
			return null;
		}
		$defaults = array(
			'product_id' => $this->product_id,
			'instance'   => $this->instance,
			'platform'   => $this->get_current_domain(),
		);

		$attr    = wp_parse_args( $defaults, $attr );
		$request = null;
		switch ( $this->method() ) {
			case 'post':
				$request = wp_remote_post( $this->create_api_url(), array( 'body' => $attr, 'timeout' => 45 ) );
				break;
			case 'get':
			default:
				$request = wp_safe_remote_get( esc_url_raw( $this->create_api_url( $attr ) ) );
				break;
		}
		if ( is_wp_error( $request ) || 200 !== wp_remote_retrieve_response_code( $request ) ) {
			return null; // Request failed.
		}
		$response = wp_remote_retrieve_body( $request );

		return $response;
	}

	/**
	 * Activated software
	 *
	 * @param array $attr Add request attribute.
	 *
	 * @return mixed
	 */
	public function activate( $attr ) {
		$attr = wp_parse_args( array(
			'request'          => 'activation',
			'software_version' => $this->_version,
		), $attr );

		return $this->request( $attr );
	}

	/**
	 * Checks if the software is activated or deactivated
	 *
	 * @param array $attr Add request attribute.
	 *
	 * @return mixed
	 */
	public function status( $attr ) {
		$attr = wp_parse_args( array(
			'request' => 'status',
		), $attr );

		return $this->request( $attr );
	}

	/**
	 * Deactivated software
	 *
	 * @param array $attr Add request attribute.
	 *
	 * @return mixed
	 */
	public function deactivate( $attr ) {
		$attr = wp_parse_args( array(
			'request' => 'deactivation',
		), $attr );

		return $this->request( $attr );
	}
}
