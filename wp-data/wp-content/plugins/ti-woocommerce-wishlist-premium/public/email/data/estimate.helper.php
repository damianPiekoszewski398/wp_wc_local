<?php
/**
 * Ask for Estimate email data
 *
 * @since             1.9.2
 * @package           TInvWishlist\Public
 * @subpackage          Email
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Ask for Estimate create email
 */
class TInvWL_Public_Email_Data_Estimate {

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
	function __construct( $plugin_name = TINVWL_PREFIX, $version = TINVWL_VERSION ) {
		$this->_name    = $plugin_name;
		$this->_version = $version;
		$this->load_data();
		$this->init_form_fields();
	}

	/**
	 * Set email defaults
	 */
	function load_data() {
		$this->id          = $this->_name . '_estimate_email';
		$this->title       = __( 'Wishlist Ask for Estimate', 'ti-woocommerce-wishlist-premium' );
		$this->description = __( 'This email is sent when a user click the button "Ask for Estimate".', 'ti-woocommerce-wishlist-premium' );

		// These are the default heading and subject lines that can be overridden using the settings.
		$this->heading = __( 'Estimate Request', 'ti-woocommerce-wishlist-premium' );
		$this->subject = __( 'Estimate Request', 'ti-woocommerce-wishlist-premium' );

		// These define the locations of the templates that this email should use, we'll just use the new order template since this email is similar.
		$this->template_name = 'ti-estimate';
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
			'recipient'  => array(
				'title'       => __( 'Recipient(s)', 'ti-woocommerce-wishlist-premium' ),
				'type'        => 'textarea',
				'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'ti-woocommerce-wishlist-premium' ), esc_attr( get_option( 'admin_email' ) ) ),
				'default'     => get_option( 'admin_email' ),
				'css'         => 'width:350px;resize:none;',
			),
			'copy'       => array(
				'title'   => __( 'CC copy', 'ti-woocommerce-wishlist-premium' ),
				'type'    => 'checkbox',
				'label'   => __( 'Send CC copy to wishlist owner', 'ti-woocommerce-wishlist-premium' ),
				'default' => '',
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

	/**
	 * Email type options.
	 *
	 * @return array
	 */
	public function get_email_type_options() {
		$types = array( 'plain' => __( 'Plain text', 'ti-woocommerce-wishlist-premium' ) );

		if ( class_exists( 'DOMDocument' ) ) {
			$types['html']      = __( 'HTML', 'ti-woocommerce-wishlist-premium' );
			$types['multipart'] = __( 'Multipart', 'ti-woocommerce-wishlist-premium' );
		}

		return $types;
	}
}
