<?php
/**
 * Wishlist Notification a change price product create email
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 * @subpackage          Email
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Wishlist Notification a change price product create email
 */
class TInvWL_Public_Email_Subscribe extends WC_Email {

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
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	function __construct( $plugin_name, $version ) {
		$this->_name    = $plugin_name;
		$this->_version = $version;
		$this->load_data();
		$this->set_templates();
		parent::__construct();
		add_filter( 'woocommerce_email_get_option', array( $this, 'get_option_tinvwl' ), 10, 4 );
		add_filter( 'woocommerce_email_enabled_' . $this->id, array( $this, 'enabled_tinvwl' ), 10, 1 );
	}

	/**
	 * Set template email
	 */
	function set_templates() {
		$emailtemplate = tinv_template_email( get_class( $this ) );
		$this->set_template( $emailtemplate );
	}

	/**
	 * Set template email
	 *
	 * @param string $emailtemplate Email template name.
	 */
	function set_template( $emailtemplate = '' ) {
		$this->template_html  = $this->loadtemplates($this->template_name, $emailtemplate, false);
		$this->template_plain = $this->loadtemplates($this->template_name, $emailtemplate, true);
	}

	/**
	 * Get current template path
	 *
	 * @param string $template Name template.
	 * @param string $emailtemplate Name skin template.
	 * @param boolean $plain Plain or HTML template?.
	 *
	 * @return string
	 */
	function loadtemplates($template, $emailtemplate, $plain = false) {
		$curtemplate   = tinv_template();
		$template_name = 'emails' . DIRECTORY_SEPARATOR . ( $plain ? 'plain' . DIRECTORY_SEPARATOR : '' ) . $template . $emailtemplate . '.php';
		if ( ! empty( $curtemplate ) ) {
			if ( file_exists( TINVWL_PATH . implode( DIRECTORY_SEPARATOR, array(
					'templates',
					$curtemplate,
					$template_name,
				) ) ) ) {
				return $template_name;
			}
		}
		if ( file_exists( TINVWL_PATH . implode( DIRECTORY_SEPARATOR, array( 'templates', $template_name ) ) ) ) {
			return $template_name;
		}

		return 'emails' . DIRECTORY_SEPARATOR . ( $plain ? 'plain' . DIRECTORY_SEPARATOR : '' ) . $template . '.php';
	}

	/**
	 * Update status email
	 *
	 * @param boolean $value Woocommerce status.
	 *
	 * @return boolean
	 */
	function enabled_tinvwl( $value ) {
		$option_name = str_replace( $this->_name . '_', '', $this->id );
		$_value      = tinv_get_option( $option_name, 'enabled' );
		if ( is_null( $_value ) ) {
			return $value;
		}

		return $_value;
	}

	/**
	 * It replaces the value to the value of the plugin
	 *
	 * @param mixed $value Set value.
	 * @param object $_this Object for validation id.
	 * @param mixed $_value New Value.
	 * @param string $key key field.
	 *
	 * @return mixed
	 */
	function get_option_tinvwl( $value, $_this, $_value, $key ) {
		if ( $this->id === $_this->id ) {
			$option_name = str_replace( $this->_name . '_', '', $this->id );
			$_value      = tinv_get_option( $option_name, $key );
			if ( is_null( $_value ) ) {
				return $value;
			}
			if ( is_bool( $_value ) ) {
				$_value = $_value ? 'yes' : 'no';
			}

			return $_value;
		}

		return $value;
	}

	/**
	 * Set email defaults
	 */
	function load_data() {
		$this->id          = $this->_name . '_subscribe_email';
		$this->title       = __( 'Wishlist Notification of changes in subscriptions', 'ti-woocommerce-wishlist-premium' );
		$this->description = __( 'This notification will be sent to customers that follow someone Wishlist and there were some changes e.g. product added, removed, purchased, became in stock or out of stock, changed products quantity.', 'ti-woocommerce-wishlist-premium' );

		// These are the default heading and subject lines that can be overridden using the settings.
		$this->heading = __( 'The Wishlist you follow has been changed!', 'ti-woocommerce-wishlist-premium' );
		$this->subject = __( 'The Wishlist you follow has been changed', 'ti-woocommerce-wishlist-premium' );

		// These define the locations of the templates that this email should use, we'll just use the new order template since this email is similar.
		$this->template_name = 'ti-subscribes';

		$this->events = TInvWL_Subscribers::event_lists();

		// Trigger on new paid orders.
		add_action( 'tinvwl_send_subscribe', array( $this, 'trigger' ), 10, 3 );

		// This sets the recipient to the settings defined below in init_form_fields().
		$this->customer_email = true;
	}

	/**
	 * Get list templates
	 *
	 * @return array
	 */
	public static function gettemplates() {
		return self::templates( 'ti-subscribes' );
	}

	/**
	 * Save value to plugin
	 */
	function process_admin_options() {
		parent::process_admin_options();
		$option_name = str_replace( $this->_name . '_', '', $this->id );
		$enabled     = tinv_get_option( $option_name, 'enabled' );
		tinv_update_option( 'subscribe', 'allow', $enabled );
		TInvWL_Admin_Settings_General::subscribe_run();
	}

	/**
	 * Run method send mail
	 *
	 * @param string $recipient Recipient email.
	 * @param array $wishlist An array of wishlists in which the product is be found.
	 * @param array $products An array of products and actions.
	 *
	 * @return boolean
	 */
	function trigger( $recipient, $wishlist = array(), $products = array() ) {
		if ( ! filter_var( $recipient, FILTER_VALIDATE_EMAIL ) || empty( $wishlist ) || empty( $products ) ) {
			return false;
		}

		$this->heading = $this->get_option( 'heading' );
		$this->subject = $this->get_option( 'subject' );

		$this->recipient = $recipient;

		// Prepare User.
		$user             = get_user_by( 'email', $recipient );
		$this->user_login = ( ! $user || ! $user->exists() ) ? $this->recipient : $user->user_login;

		$this->wishlist = $wishlist;

		// Get wishlist url.
		$this->wishlist['url'] = tinv_url_wishlist( $wishlist['ID'] );

		// Prepare Products.
		foreach ( $products as &$_products ) {
			foreach ( $_products as &$product ) {
				$product_id   = absint( $product[0] );
				$variation_id = absint( $product[1] );
				$quantity     = absint( $product[2] );
				if ( 'product_variation' == get_post_type( $product_id ) ) { // WPCS: loose comparison ok.
					$variation_id = $product_id;
					$product_id   = wp_get_post_parent_id( $variation_id );
				}
				$product = wc_get_product( $variation_id ? $variation_id : $product_id );
				if ( $product ) {
					$product->wl_quantity = $quantity;
				}
			}
		}
		$this->products = $products;

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get content html function
	 *
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		tinv_wishlist_template( $this->template_html, apply_filters( 'tinvwl_subscribe_email_data_template_html', array(
			'email_heading' => $this->get_heading(),
			'blogname'      => $this->get_blogname(),
			'wishlist'      => $this->wishlist,
			'products'      => $this->products,
			'events_name'   => $this->events,
			'user_name'     => $this->user_login,
			'sent_to_admin' => true,
			'plain_text'    => false,
			'email'         => $this,
		) ) );

		return ob_get_clean();
	}

	/**
	 * Get content plain function
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		tinv_wishlist_template( $this->template_plain, apply_filters( 'tinvwl_subscribe_email_data_template_plain', array(
			'email_heading' => $this->get_heading(),
			'blogname'      => $this->get_blogname(),
			'wishlist'      => $this->wishlist,
			'products'      => $this->products,
			'events_name'   => $this->events,
			'user_name'     => isset( $this->user->user_login ) ? $this->user->user_login : '',
			'sent_to_admin' => true,
			'plain_text'    => true,
			'email'         => $this,
		) ) );

		return ob_get_clean();
	}

	/**
	 * Initialise Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'ti-woocommerce-wishlist-premium' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'ti-woocommerce-wishlist-premium' ),
				'default' => 'yes',
			),
			'subject'    => array(
				'title'       => __( 'Email Subject', 'ti-woocommerce-wishlist-premium' ),
				'type'        => 'text',
				'description' => sprintf( __( 'Defaults to <code>%s</code>', 'ti-woocommerce-wishlist-premium' ), $this->subject ),
				'default'     => $this->subject,
				'desc_tip'    => true,
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'ti-woocommerce-wishlist-premium' ),
				'type'        => 'text',
				'description' => sprintf( __( 'Defaults to <code>%s</code>', 'ti-woocommerce-wishlist-premium' ), $this->heading ),
				'default'     => $this->heading,
				'desc_tip'    => true,
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'ti-woocommerce-wishlist-premium' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'ti-woocommerce-wishlist-premium' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options(),
				'desc_tip'    => true,
			),
		);
	}
}
