<?php
/**
 * TemplateInvaders Updater - Class
 *
 * @package Update TemplateInvaders
 * @since             1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * TemplateInvaders Updater - Class
 */
abstract class TI_Api_Manager_Update {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	public $_name;

	/**
	 * Plugin version
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
	 * API Updater
	 *
	 * @var \TI_Api_Manager_Update_API_Check
	 */
	private $class_updater;

	/**
	 * API Key
	 *
	 * @var \TI_Api_Manager_Key
	 */
	private $class_key;

	/**
	 * API Path Librarry
	 *
	 * @var type
	 */
	private $api_path;

	/**
	 * Software update url
	 *
	 * @var type
	 */
	protected $upgrade_url;

	/**
	 * Software Title
	 *
	 * @var type
	 */
	protected $product_id;

	/**
	 * Software Name
	 *
	 * @var type
	 */
	protected $product_name;

	/**
	 * Software renew license url
	 *
	 * @var type
	 */
	protected $renew_license_url;

	/**
	 *  Valiable 'plugin' or 'theme'
	 *
	 * @var type
	 */
	protected $plugin_or_theme;

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
	}

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 * @param string $dictionary Dictonary name.
	 */
	public function __construct( $plugin_name, $version, $dictionary = 'default' ) {
		if ( is_admin() ) {

			$this->_name      = $plugin_name;
			$this->_version   = $version;
			$this->dictionary = $dictionary;
			// API Path for children library.
			$this->api_path = trailingslashit( dirname( __FILE__ ) );

			$instance = $this->get_option( 'instance', '', '' );
			if ( empty( $instance ) ) {
				$this->set_option( 'instance', '', wp_generate_password( 12, false ) );
			}
			$license = $this->get_option( 'license' );
			if ( empty( $license['licence_key'] ) || empty( $license['email'] ) ) {
				$this->set_option( 'license', '', array(
					'licence_key' => '',
					'email'       => '',
				) );
			}

			if ( empty( $license['licence_key'] ) ) {
				add_action( 'admin_notices', array( $this, 'activation_license_notice' ) );

			}

			$this->load_function();

//		if ( ! empty( $license['licence_key'] ) && ! empty( $license['email'] ) ) {
			$this->updater();
//		}

		}
	}


	/**
	 * Print notice with products to activate
	 *
	 * @since 2.3.0
	 */
	public function activation_license_notice() { ?>
		<div class="notice notice-error">
			<div style="float: left;padding: 25px 20px 10px 10px;"><img
					src="<?php echo TINVWL_URL; ?>assets/img/logo_heart.png" width="54px"
					height="54px" alt=""
					style="border: 0; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;">
			</div>
			<div>
				<p>
					<strong><?php _e( 'Welcome to TI WooCommerce Wishlist Premium!', 'ti-woocommerce-wishlist-premium' ) ?></strong>

				</p>
				<p><?php _e( 'Please activate your license to get feature updates and premium support.', 'ti-woocommerce-wishlist-premium' ); ?></p>
				<p>
					<?php printf( '<a class="button button-primary" href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=tinvwl-license' ) ), esc_html_x( 'Activate your license', 'Button label', 'ti-woocommerce-wishlist-premium' ) ); ?>
				</p>
			</div>
		</div>
		<?php
	}


	/**
	 * Load function
	 */
	abstract function load_function();

	/**
	 * Add settings page error
	 *
	 * @param mixed $code Error code, used only for type error.
	 * @param string $message Error message.
	 * @param string $type Type error.
	 *
	 * @return boolean
	 */
	abstract function add_settings_error( $code, $message, $type = 'error' );

	/**
	 * Get status license
	 *
	 * @param bolean $forse Forse check status.
	 *
	 * @return boolean
	 */
	function status( $forse = false ) {
		if ( $this->get_option( 'license_activity', 'date', 0 ) > time() - 1 * DAY_IN_SECONDS && ! $forse ) {
			return $this->get_option( 'license_activity', 'status' );
		}
		$status = $this->license_key_status();
		$status = ( ! empty( $status['status_check'] ) && 'active' == $status['status_check'] );  // WPCS: loose comparison ok.

		$this->set_option( 'license_activity', 'date', time() );

		return $this->set_option( 'license_activity', 'status', $status );
	}

	/**
	 * Get license status
	 *
	 * @return mixed
	 */
	public function license_key_status() {
		$license = $this->get_option( 'license' );
		if ( ! empty( $license['licence_key'] ) && ! empty( $license['email'] ) ) {
			return json_decode( $this->key()->status( $license ), true );
		}

		return false;
	}

	/**
	 * Deactivate API
	 *
	 * @return boolean
	 */
	function deactivate() {
		if ( ! $this->status() ) {
			return false;
		}

		$results = json_decode( $this->key()->deactivate( $this->get_option( 'license' ) ), true );

		if ( empty( $results ) ) {
			return $this->add_settings_error( 0, __( 'Connection failed to the License Key API server. Try again later.', 'ti-woocommerce-wishlist-premium' ) ); // @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
		}

		if ( array_key_exists( 'deactivated', (array) $results ) && true === $results['deactivated'] ) {
			$this->set_option( 'license', '', array(
				'licence_key' => '',
				'email'       => '',
			) );

			$message = __( 'Plugin license deactivated.', 'ti-woocommerce-wishlist-premium' ); // @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
			if ( array_key_exists( 'activations_remaining', (array) $results ) ) {
				$message .= " {$results[ 'activations_remaining' ]}.";
			}
			$this->add_settings_error( 'deactivate_msg', $message, 'updated' );

			return true;
		}

		if ( array_key_exists( 'code', (array) $results ) ) {
			$message = '';
			if ( array_key_exists( 'error', (array) $results ) ) {
				$message .= " {$results[ 'error' ]}.";
			}
			if ( array_key_exists( 'additional info', (array) $results ) ) {
				$message .= " {$results[ 'additional info' ]}.";
			}
			$this->add_settings_error( $results['code'], $message, 'error' );
		}
	}

	/**
	 * Deactivation API Key.
	 *
	 * @return boolean
	 */
	function deactivate_license_key() {
		$results = $this->key()->deactivate( $this->get_option( 'license' ) );
		if ( empty( $results ) ) {
			return $this->add_settings_error( 0, __( 'Connection failed to the License Key API server. Try again later.', 'ti-woocommerce-wishlist-premium' ) ); // @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
		}
		if ( array_key_exists( 'deactivated', (array) $results ) && true === $results['deactivated'] ) {
			$this->set_option( 'license', '', array(
				'licence_key' => '',
				'email'       => '',
			) );

			return true;
		}

		return $this->add_settings_error( 0, __( 'The license could not be deactivated. Use the License Deactivation button on the https://templateinvaders.com/my-account/downloads/ to manually deactivate the license before activating a new license.', 'ti-woocommerce-wishlist-premium' ), 'updated' ); // @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
	}

	/**
	 * Validate API data
	 *
	 * @param string $api_key API Key.
	 * @param string $email API Email.
	 *
	 * @return boolean
	 */
	function validate( $api_key, $email ) {
		$licence_key = $this->get_option( 'license', 'licence_key' );
		if ( ! empty( $licence_key ) && $licence_key != $api_key ) { // WPCS: loose comparison ok.
			$this->deactivate_license_key();
		}
		if ( $this->status() ) {
			return false;
		}

		$attr = array(
			'email'       => $email,
			'licence_key' => $api_key,
		);

		$results = json_decode( $this->key()->activate( $attr ), true );

		if ( empty( $results ) ) {
			return $this->add_settings_error( 0, __( 'Connection failed to the License Key API server. Try again later.', 'ti-woocommerce-wishlist-premium' ) ); // @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
		}
		if ( array_key_exists( 'activated', (array) $results ) && true === $results['activated'] ) {
			$this->set_option( 'license', 'licence_key', $api_key );
			$this->set_option( 'license', 'email', $email );

			$message = __( 'Plugin activated.', 'ti-woocommerce-wishlist-premium' ); // @codingStandardsIgnoreLine WordPress.WP.I18n.NonSingularStringLiteralDomain
			if ( array_key_exists( 'message', (array) $results ) ) {
				$message .= " {$results[ 'message' ]}.";
			}
			$this->add_settings_error( 'activate_msg', $message, 'updated' );

			return true;
		}

		if ( array_key_exists( 'code', (array) $results ) ) {
			$message = '';
			if ( array_key_exists( 'error', (array) $results ) ) {
				$message .= " {$results[ 'error' ]}.";
			}
			if ( array_key_exists( 'additional info', (array) $results ) ) {
				$message .= " {$results[ 'additional info' ]}.";
			}
			$this->add_settings_error( $results['code'], $message, 'error' );
		}
	}

	/**
	 * Updater class
	 *
	 * @return \TI_Api_Manager_Update_API_Check
	 */
	public function updater() {
		if ( is_null( $this->class_updater ) ) {
			if ( ! class_exists( 'TI_Api_Manager_Update_API_Check' ) ) {
				require_once $this->api_path . 'class-ti-plugin-update.php';
			}
			$this->class_updater = new TI_Api_Manager_Update_API_Check( $this->_name, $this->_version, $this->dictionary );
			$this->class_updater->product( $this->plugin_or_theme, $this->product_name, $this->product_id, $this->upgrade_url, $this->renew_license_url, $this->get_option( 'instance' ) )->license( $this->get_option( 'license', 'email' ), $this->get_option( 'license', 'licence_key' ) )->run();
		}

		return $this->class_updater;
	}

	/**
	 * Get version in remote server
	 *
	 * @param bolean $forse Forse check version.
	 *
	 * @return string
	 */
	public function get_inapi_version( $forse = false ) {
		if ( $forse ) {
			$new_ver = $this->updater()->check_version();
			set_transient( $this->_name . '_ver_update', $new_ver, 12 * HOUR_IN_SECONDS );

			return $new_ver;
		}
		$new_ver = get_transient( $this->_name . '_ver_update' );
		if ( empty( $new_ver ) ) {
			return $this->_version;
		}

		return $new_ver;
	}

	/**
	 * API Key class
	 *
	 * @return \TI_Api_Manager_Key
	 */
	public function key() {
		if ( is_null( $this->class_key ) ) {
			if ( ! class_exists( 'TI_Api_Manager_Key' ) ) {
				require_once $this->api_path . 'class-ti-key-api.php';
			}
			$this->class_key = new TI_Api_Manager_Key( $this->_name, $this->_version );
			$this->class_key->product( $this->product_id, $this->upgrade_url, $this->get_option( 'instance' ) );
		}

		return $this->class_key;
	}

	/**
	 * Get options
	 *
	 * @param string $name Name setting.
	 * @param string $key Key setting.
	 * @param mixed $default Default value.
	 *
	 * @return mixed
	 */
	protected function get_option( $name, $key = '', $default = false ) {
		$value = get_option( sprintf( '%s-%s', $this->_name, $name ), null );
		if ( empty( $key ) ) {
			return is_null( $value ) ? $default : $value;
		} else {
			if ( is_null( $value ) ) {
				return $default;
			} else {
				return array_key_exists( $key, $value ) ? $value[ $key ] : $default;
			}
		}

		return false;
	}

	/**
	 * Set options
	 *
	 * @param string $name Name setting.
	 * @param string $key Key setting.
	 * @param mixed $value Value.
	 *
	 * @return type
	 */
	private function set_option( $name, $key = '', $value = false ) {
		$db = get_option( sprintf( '%s-%s', $this->_name, $name ) );

		$_value = ( $db ) ? $db : array();
		$_value = ( empty( $key ) ) ? $value : $_value;
		if ( ! empty( $key ) && is_array( $_value ) ) {
			$_value[ $key ] = $value;
		}

		if ( false === $db ) {
			add_option( sprintf( '%s-%s', $this->_name, $name ), $_value );
		} else {
			update_option( sprintf( '%s-%s', $this->_name, $name ), $_value );
		}

		return $value;
	}
}
