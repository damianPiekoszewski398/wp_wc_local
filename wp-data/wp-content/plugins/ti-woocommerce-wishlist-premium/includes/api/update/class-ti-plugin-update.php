<?php
/**
 * TemplateInvaders Updater - Single Updater Class
 *
 * @package Update TemplateInvaders/Update Handler
 * @since             1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * TemplateInvaders Updater - Single Updater Class
 */
class TI_Api_Manager_Update_API_Check {

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
	 * Dictonary name
	 *
	 * @var string
	 */
	public $dictionary;

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
	 * Software renew license url
	 *
	 * @var string
	 */
	public $renew_license_url;

	/**
	 * Software Title
	 *
	 * @var string
	 */
	private $product_id;

	/**
	 * Software Name
	 *
	 * @var string
	 */
	private $product_name;

	/**
	 * Software Nice Name
	 *
	 * @var string
	 */
	private $product_nice_name;

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
	 * Activation email
	 *
	 * @var string
	 */
	private $activation_email;

	/**
	 * Activation api key
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Is sets license?
	 *
	 * @var boolean
	 */
	private $license;

	/**
	 * Array for custom message errors
	 *
	 * @var array
	 */
	private $errors;

	/**
	 * Array for custom function errors
	 *
	 * @var array
	 */
	private $errors_func;

	/**
	 * Constructor
	 *
	 * @param string $software_name Software name.
	 * @param string $version Software version.
	 * @param string $dictionary Dictonary name.
	 */
	public function __construct( $software_name, $version, $dictionary = 'default' ) {
		$this->post();
		$this->_name       = $software_name;
		$this->_version    = $version;
		$this->dictionary  = $dictionary;
		$this->product     = false;
		$this->license     = false;
		$this->errors_func = array();
		$this->errors      = array();
	}

	/**
	 * Set product
	 *
	 * @param string $plugin_or_theme Valiable 'plugin' or 'theme'.
	 * @param string $product_name Plugin transient name.
	 * @param string $product_id Software Title.
	 * @param string $upgrade_url Software update url.
	 * @param string $renew_license_url Software renew license url.
	 * @param string $instance Unique for each activation or install.
	 *
	 * @return \TI_Api_Manager_Update_API_Check
	 */
	public function product( $plugin_or_theme, $product_name, $product_id, $upgrade_url, $renew_license_url, $instance ) {
		$this->plugin_or_theme   = $plugin_or_theme;
		$this->upgrade_url       = $upgrade_url;
		$this->renew_license_url = $renew_license_url;
		$this->product_name      = $product_name;
		$this->product_nice_name = 'TI WooCommerce Wishlist Premium';
		$this->product_id        = $product_id;
		$this->instance          = $instance;
		$this->product           = true;

		// Slug should be the same as the plugin/theme directory name.
		if ( 0 !== strpos( $this->product_name, '.php' ) ) {
			$this->slug = dirname( $this->product_name );
		} else {
			$this->slug = $this->product_name;
		}

		return $this;
	}

	/**
	 * Set license
	 *
	 * @param string $activation_email Activation email.
	 * @param string $api_key Activation API key.
	 *
	 * @return \TI_Api_Manager_Update_API_Check
	 */
	public function license( $activation_email, $api_key ) {
		$this->activation_email = $activation_email;
		$this->api_key          = $api_key;
		$this->license          = true;

		return $this;
	}

	/**
	 * Run updater
	 *
	 * @return \TI_Api_Manager_Update_API_Check
	 */
	public function run() {
		if ( $this->product && $this->license ) {
			if ( 'plugin' === $this->plugin_or_theme ) { // Check For Plugin Updates.
				add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_check' ) );
				add_filter( 'plugins_api', array( $this, 'request_check' ), 10, 3 );
			} elseif ( 'theme' === $this->plugin_or_theme ) { // Check For Theme Updates.
				add_filter( 'pre_set_site_transient_update_themes', array( $this, 'update_check' ) );
				add_filter( 'themes_api', array( $this, 'request_check' ), 10, 3 );
			}
		}

		return $this;
	}

	/**
	 * Set custom error message
	 *
	 * @param mixed $name Type error name.
	 * @param mixed $message Message error.
	 *
	 * @return \TI_Api_Manager_Update_API_Check
	 */
	public function set_error( $name = '', $message = '' ) {
		if ( empty( $name ) ) {
			$this->errors = (array) $message;
		} else {
			$this->errors[ $name ] = $message;
		}

		return $this;
	}

	/**
	 * Set custom error function
	 *
	 * @param mixed $name Type error name.
	 * @param mixed $function Function name error.
	 *
	 * @return \TI_Api_Manager_Update_API_Check
	 */
	public function set_error_function( $name = '', $function = null ) {
		if ( empty( $name ) ) {
			$this->errors_func = (array) $function;
		} elseif ( ! empty( $function ) ) {
			$this->errors_func[ $name ] = $function;
		}

		return $this;
	}

	/**
	 * Set method post
	 *
	 * @return \TI_Api_Manager_Update_API_Check
	 */
	public function post() {
		$this->method = 'POST';

		return $this;
	}

	/**
	 * Set method get
	 *
	 * @return \TI_Api_Manager_Update_API_Check
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
		$api_url = add_query_arg( 'wc-api', 'upgrade-api', $this->upgrade_url );
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
		$response = @unserialize( $response ); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
		if ( is_object( $response ) ) {
			return $response;
		} else {
			return null;
		}
	}

	/**
	 * Get API Version
	 *
	 * @return string
	 */
	function check_version() {
		$attr     = array(
			'request'          => 'pluginupdatecheck',
			'slug'             => $this->slug,
			'plugin_name'      => $this->product_name,
			'api_key'          => $this->api_key,
			'activation_email' => $this->activation_email,
			'version'          => $this->_version,
			'software_version' => $this->_version,
			'source'           => TINVWL_SOURCE,
		);
		$response = $this->request( $attr ); // Check for a plugin update.

		if ( ! empty( $response ) && is_object( $response ) ) {
			if ( isset( $response->errors ) ) {
				$this->check_response_for_errors( $response->errors );
			}

			// New plugin version from the API.
			$new_ver = (string) @$response->new_version; // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged

			return $new_ver;
		}

		return $this->_version;
	}

	/**
	 * Check for updates against the remote server.
	 *
	 * @param object $transient Transient updater.
	 *
	 * @return object
	 */
	public function update_check( $transient ) {
		// First transient before the real check.
		if ( ! isset( $transient->response ) ) {
			return $transient;
		}

		$transient_name = $this->_name . '_update_check';

		$response = get_transient( $transient_name );

		if ( false === $response ) {
			$attr     = array(
				'request'          => 'pluginupdatecheck',
				'slug'             => $this->slug,
				'plugin_name'      => $this->product_name,
				'api_key'          => $this->api_key,
				'activation_email' => $this->activation_email,
				'version'          => $this->_version,
				'software_version' => $this->_version,
				'source'           => TINVWL_SOURCE,
			);
			$response = $this->request( $attr ); // Check for a plugin update.
			set_transient( $transient_name, $response, MINUTE_IN_SECONDS );
		}

		if ( ! empty( $response ) && is_object( $response ) ) {
			if ( isset( $response->errors ) ) {
				$this->check_response_for_errors( $response->errors );
			}

			// New plugin version from the API.
			$new_ver = (string) @$response->new_version; // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
			set_transient( $this->_name . '_ver_update', $new_ver, 12 * HOUR_IN_SECONDS );
			// Current installed plugin version.
			$curr_ver = (string) $this->_version;

			if ( version_compare( $new_ver, $curr_ver, 'gt' ) ) {
				if ( 'plugin' === $this->plugin_or_theme ) {
					$transient->response[ $this->product_name ] = $response;
				} elseif ( 'theme' === $this->plugin_or_theme ) {
					$transient->response[ $this->product_name ]['new_version'] = $response->new_version;
					$transient->response[ $this->product_name ]['url']         = $response->url;
					$transient->response[ $this->product_name ]['package']     = $response->package;
				}
			} else {
				// If new version is not available then set to `no_update`
				$transient->no_update[ $this->product_name ] = $response;
			}
			//Delete license from DB in case it's not valid
			if ( isset( $response->package ) && ! $response->package ) {
//				delete_option( sprintf( '%s-%s', $this->_name, 'license' ) );
			}

			$transient->last_checked                   = time();
			$transient->checked[ $this->product_name ] = $this->_version;
		}

		return $transient;
	}

	/**
	 * Generic request helper.
	 *
	 * @param object $false Return object.
	 * @param string $action Not used.
	 * @param object $attr Attr object.
	 *
	 * @return object
	 */
	public function request_check( $false, $action, $attr ) {
		if ( 'plugin' === $this->plugin_or_theme ) {
			$version = get_site_transient( 'update_plugins' );
		} elseif ( 'theme' === $this->plugin_or_theme ) {
			$version = get_site_transient( 'update_themes' );
		}

		// Check if this plugins API is about this plugin.
		if ( isset( $attr->slug ) ) {
			if ( $attr->slug !== $this->slug ) {
				return $false;
			}
		} else {
			return $false;
		}

		$attr     = array(
			'request'          => 'plugininformation',
			'plugin_name'      => $this->product_name,
			'api_key'          => $this->api_key,
			'activation_email' => $this->activation_email,
			'version'          => $this->_version,
			'software_version' => $this->_version,
		);
		$response = $this->request( $attr );

		if ( ! empty( $response ) && is_object( $response ) ) {
			return $response;
		}
	}

	/**
	 * Apply error message.
	 *
	 * @param array $errors Respons API errors.
	 *
	 * @return boolean
	 */
	public function check_response_for_errors( $errors ) {
		$errors       = (array) $errors;
		$errors_flags = array(
			'exp_license'            => 'exp_license',
			'hold_subscription'      => 'hold_subscription',
			'cancelled_subscription' => 'cancelled_subscription',
			'exp_subscription'       => 'exp_subscription',
			'suspended_subscription' => 'suspended_subscription',
			'pending_subscription'   => 'pending_subscription',
			'trash_subscription'     => 'trash_subscription',
			'no_subscription'        => 'no_subscription',
			'no_activation'          => 'no_activation',
			'no_key'                 => 'no_key',
			'download_revoked'       => 'download_revoked',
			'switched_subscription'  => 'switched_subscription',
		);
		if (
			array_key_exists( 'no_key', $errors ) && $errors_flags['no_key'] == $errors['no_key'] && array_key_exists( 'no_subscription', $errors ) && $errors_flags['no_subscription'] == $errors['no_subscription'] // WPCS: loose comparison ok.
		) {
			add_action( 'admin_notices', array( $this, 'error_notice_no_key' ) );
			add_action( 'admin_notices', array( $this, 'error_notice_no_subscription' ) );

			return true;
		}

		foreach ( $errors_flags as $flag => $value ) {
			if ( array_key_exists( $flag, $errors ) && $value == $errors[ $flag ] ) { // WPCS: loose comparison ok.
				add_action( 'admin_notices', array( $this, 'error_notice_' . $flag ) );

				return true;
			}
		}
	}

	/**
	 * Apply error message
	 *
	 * @param string $type Error type.
	 */
	private function error_notice( $type ) {
		$errors = array(
			'exp_license'            => sprintf( __( 'The license key for %1$s has expired. You can reactivate or purchase a license key from your account <a href="%2$s" target="_blank">dashboard</a>.', 'ti-woocommerce-wishlist-premium' ), esc_html( $this->product_nice_name ), esc_url( $this->renew_license_url ) ),
			// @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
			'hold_subscription'      => sprintf( __( 'The subscription for %1$s is on-hold. You can reactivate the subscription from your account <a href="%2$s" target="_blank">dashboard</a>.', 'ti-woocommerce-wishlist-premium' ), esc_html( $this->product_nice_name ), esc_url( $this->renew_license_url ) ),
			// @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
			'cancelled_subscription' => sprintf( __( 'The subscription for %1$s has been cancelled. You can renew the subscription from your account <a href="%2$s" target="_blank">dashboard</a>. A new license key will be emailed to you after your order has been completed.', 'ti-woocommerce-wishlist-premium' ), esc_html( $this->product_nice_name ), esc_url( $this->renew_license_url ) ),
			// @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
			'exp_subscription'       => sprintf( __( 'The subscription for %1$s has expired. You can reactivate the subscription from your account <a href="%2$s" target="_blank">dashboard</a>.', 'ti-woocommerce-wishlist-premium' ), esc_html( $this->product_nice_name ), esc_url( $this->renew_license_url ) ),
			// @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
			'suspended_subscription' => sprintf( __( 'The subscription for %1$s has been suspended. You can reactivate the subscription from your account <a href="%2$s" target="_blank">dashboard</a>.', 'ti-woocommerce-wishlist-premium' ), esc_html( $this->product_nice_name ), esc_url( $this->renew_license_url ) ),
			// @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
			'pending_subscription'   => sprintf( __( 'The subscription for %1$s is still pending. You can check on the status of the subscription from your account <a href="%2$s" target="_blank">dashboard</a>.', 'ti-woocommerce-wishlist-premium' ), esc_html( $this->product_nice_name ), esc_url( $this->renew_license_url ) ),
			// @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
			'trash_subscription'     => sprintf( __( 'The subscription for %1$s has been placed in the trash and will be deleted soon. You can purchase a new subscription from your account <a href="%2$s" target="_blank">dashboard</a>.', 'ti-woocommerce-wishlist-premium' ), esc_html( $this->product_nice_name ), esc_url( $this->renew_license_url ) ),
			// @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
			'no_subscription'        => sprintf( __( 'A subscription for %1$s could not be found. You can purchase a subscription from your account <a href="%2$s" target="_blank">dashboard</a>.', 'ti-woocommerce-wishlist-premium' ), esc_html( $this->product_nice_name ), esc_url( $this->renew_license_url ) ),
			// @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
			'no_key'                 => sprintf( __( 'A license key for %1$s could not be found. Maybe you forgot to enter a license key when setting up %1$s, or the key was deactivated in your account. You can reactivate or purchase a license key from your account <a href="%2$s" target="_blank">dashboard</a>.', 'ti-woocommerce-wishlist-premium' ), esc_html( $this->product_nice_name ), esc_url( $this->renew_license_url ) ),
			// @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
			'download_revoked'       => sprintf( __( 'Download permission for %1$s has been revoked possibly due to a license key or subscription expiring. You can reactivate or purchase a license key from your account <a href="%2$s" target="_blank">dashboard</a>.', 'ti-woocommerce-wishlist-premium' ), esc_html( $this->product_nice_name ), esc_url( $this->renew_license_url ) ),
			// @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
			'no_activation'          => sprintf( __( '%1$s has not been activated. Go to the settings page and enter the license key and license email to activate %1$s.', 'ti-woocommerce-wishlist-premium' ), esc_html( $this->product_nice_name ) ),
			// @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
			'switched_subscription'  => sprintf( __( 'You changed the subscription for %1$s, so you will need to enter your new API License Key in the settings page. The License Key should have arrived in your email inbox, if not you can get it by logging into your account <a href="%2$s" target="_blank">dashboard</a>.', 'ti-woocommerce-wishlist-premium' ), esc_html( $this->product_nice_name ), esc_url( $this->renew_license_url ) ),
			// @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
		);

		if ( array_key_exists( $type, $errors ) ) {
			$message = array_key_exists( $type, $this->errors ) ? $this->errors[ $type ] : $errors[ $type ];
			if ( array_key_exists( $type, $this->errors_func ) ) {
				$callback = $this->errors_func[ $type ];
				call_user_func( $callback, $message );
			} elseif ( ! empty( $message ) ) {
				printf( '<div id="message" class="error"><p>%s</p></div>', $message ); // WPCS: xss ok.
			}
		}
	}

	/**
	 * Apply error message 'exp_license'
	 */
	public function error_notice_exp_license() {
		$this->error_notice( 'exp_license' );
	}

	/**
	 * Apply error message 'hold_subscription'
	 */
	public function error_notice_hold_subscription() {
		$this->error_notice( 'hold_subscription' );
	}

	/**
	 * Apply error message 'cancelled_subscription'
	 */
	public function error_notice_cancelled_subscription() {
		$this->error_notice( 'cancelled_subscription' );
	}

	/**
	 * Apply error message 'exp_subscription'
	 */
	public function error_notice_exp_subscription() {
		$this->error_notice( 'exp_subscription' );
	}

	/**
	 * Apply error message 'suspended_subscription'
	 */
	public function error_notice_suspended_subscription() {
		$this->error_notice( 'suspended_subscription' );
	}

	/**
	 * Apply error message 'pending_subscription'
	 */
	public function error_notice_pending_subscription() {
		$this->error_notice( 'pending_subscription' );
	}

	/**
	 * Apply error message 'trash_subscription'
	 */
	public function error_notice_trash_subscription() {
		$this->error_notice( 'trash_subscription' );
	}

	/**
	 * Apply error message 'no_subscription'
	 */
	public function error_notice_no_subscription() {
		$this->error_notice( 'no_subscription' );
	}

	/**
	 * Apply error message 'no_activation'
	 */
	public function error_notice_no_activation() {
		$this->error_notice( 'no_activation' );
	}

	/**
	 * Apply error message 'no_key'
	 */
	public function error_notice_no_key() {
		$this->error_notice( 'no_key' );
	}

	/**
	 * Apply error message 'download_revoked'
	 */
	public function error_notice_download_revoked() {
		$this->error_notice( 'download_revoked' );
	}

	/**
	 * Apply error message 'switched_subscription'
	 */
	public function error_notice_switched_subscription() {
		$this->error_notice( 'switched_subscription' );
	}
}
