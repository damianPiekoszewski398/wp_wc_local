<?php
/**
 * Admin settings class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin
 * @subpackage        Settings
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Admin settings class
 */
class TInvWL_Admin_Settings_Notification extends TInvWL_Admin_BaseSection {

	/**
	 * Priority for admin menu
	 *
	 * @var integer
	 */
	public $priority = 60;

	/**
	 * This class
	 *
	 * @var \TInvWL_Admin_Settings_General
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Admin_Settings_General
	 */
	public static function instance( $plugin_name = TINVWL_PREFIX, $plugin_version = TINVWL_VERSION ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $plugin_name, $plugin_version );
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	function __construct( $plugin_name, $version ) {
		$this->_name    = $plugin_name;
		$this->_version = $version;
		parent::__construct( $plugin_name, $version );
	}

	/**
	 * Menu array
	 *
	 * @return array
	 */
	function menu() {
		return array(
			'title'      => __( 'Emails Settings', 'ti-woocommerce-wishlist-premium' ),
			'page_title' => __( 'Emails Settings', 'ti-woocommerce-wishlist-premium' ),
			'method'     => array( $this, '_print_' ),
			'slug'       => 'notification-settings',
			'capability' => 'tinvwl_emails_settings',
		);
	}

	/**
	 * Create Scetions for this settings
	 *
	 * @return array
	 */
	function constructor_data() {

		$this->email_settings = TInvWL_Public_Email::instance();

		$sections   = array();
		$sections[] = array(
			'id'         => 'global_notifications',
			'title'      => __( 'Global settings', 'ti-woocommerce-wishlist-premium' ),
			'show_names' => true,
			'fields'     => array(
				array(
					'type' => 'checkboxonoff',
					'name' => 'enable_notifications',
					'text' => __( 'Enable all notifications for users by default', 'ti-woocommerce-wishlist-premium' ),
					'desc' => __( 'You can enable all notification by default. Users have options to manage notifications also.', 'ti-woocommerce-wishlist-premium' ),
					'std'  => false,
				),
			),
		);
		$sections[] = array(
			'id'         => 'estimate_button',
			'title'      => __( 'Ask for Estimate Options', 'ti-woocommerce-wishlist-premium' ),
			'show_names' => false,
			'fields'     => array(
				array(
					'type'  => 'checkboxonoff',
					'name'  => 'allow',
					'text'  => __( 'Enable "Ask For Estimate" Button', 'ti-woocommerce-wishlist-premium' ),
					'desc'  => __( 'Enable to show "Ask For an Estimate" button on wishlist page. Allows to send email with products from customer wishlist to store owner with request for estimate.', 'ti-woocommerce-wishlist-premium' ),
					'std'   => true,
					'extra' => array( 'tiwl-show' => '.tiwl-estimate-sett-allow' ),
					'class' => 'tinvwl-header-row',
				),
				array(
					'type'  => 'group',
					'id'    => 'estimate_allow',
					'class' => 'tiwl-estimate-sett-allow',
				),
				array(
					'type'  => 'checkboxonoff',
					'name'  => 'notes',
					'text'  => __( 'Enable additional notes for request', 'ti-woocommerce-wishlist-premium' ),
					'desc'  => __( 'Enable to show text area in popup, where wishlist owner can add message text to the estimate request.', 'ti-woocommerce-wishlist-premium' ),
					'std'   => true,
					'extra' => array( 'tiwl-show' => '.tiwl-estimate-notes' ),
				),
				array(
					'type'  => 'text',
					'name'  => 'text_notes',
					'text'  => __( 'Label for "Additional notes" text area', 'ti-woocommerce-wishlist-premium' ),
					'std'   => 'Additional notes',
					'class' => 'tiwl-estimate-notes',
				),
				array(
					'type' => 'checkboxonoff',
					'name' => 'guests',
					'text' => __( 'Enable "Ask For Estimate" feature for guests', 'ti-woocommerce-wishlist-premium' ),
					'desc' => __( 'Enable to show "Ask For an Estimate" button on wishlist page for guests. It will add required name and email fields.', 'ti-woocommerce-wishlist-premium' ),
					'std'  => true,
				),
			),
		);
		add_action( 'tinvwl_email_prepare_fields', array( $this, 'hide_enabled' ), 20 );
		$sections[] = array(
			'id'         => 'estimate_email',
			'title'      => __( 'Wishlist "Ask for Estimate" Email settings', 'ti-woocommerce-wishlist-premium' ),
			'desc'       => __( 'These are the settings related to the email that will be sent when a user clicks the "Ask for estimate" button.', 'ti-woocommerce-wishlist-premium' ),
			'show_names' => true,
			'class'      => 'tiwl-estimate-sett-allow',
			/**
			 * Field load from mail class
			 *
			 * @see  TInvWL_Public_Email_Estimate::init_form_fields()
			 * @link \public\email\estimate.class.php TInvWL_Public_Email_Estimate
			 * @see  tinv_prepare_fields
			 * @link tinv-wishlists-function.php
			 */
			'fields'     => $this->fields_email_prepare( $this->_name . '_Public_Email_Estimate' ),
		);
		remove_action( $this->_name . '_email_prepare_fields', array( $this, 'hide_enabled' ), 20 );
		add_action( 'tinvwl_notification_prepare_fields', array( $this, 'full_content' ), 20 );
		$sections[] = array(
			'id'         => 'promotional_email',
			'title'      => __( 'Promotional Email settings', 'ti-woocommerce-wishlist-premium' ),
			'desc'       => __( 'You can send promotional email for specific product to users that added this product to wishlist. Send promotion button can be found in <code>TI Wishlist > Product Analytics</code>', 'ti-woocommerce-wishlist-premium' ),
			'show_names' => true,
			/**
			 * Field load from mail class
			 *
			 * @see  TInvWL_Public_Email_Promotional::init_form_fields()
			 * @link \public\email\promotional.class.php TInvWL_Public_Email_Promotional
			 * @see  tinv_prepare_fields
			 * @link tinv-wishlists-function.php
			 */
			'fields'     => $this->fields_email_prepare( $this->_name . '_Public_Email_Promotional' ),
		);
		remove_action( $this->_name . '_notification_prepare_fields', array( $this, 'full_content' ), 20 );
		$sections[] = array(
			'id'         => 'notifications_style',
			'title'      => __( 'Promotional Email Style', 'ti-woocommerce-wishlist-premium' ),
			'desc'       => __( 'These are the style settings for predefined promotional email templates. You can choose promotional email temaplate in the "Email Templates" block below.', 'ti-woocommerce-wishlist-premium' ),
			'show_names' => true,
			'fields'     => array(
				array(
					'type'  => 'checkboxonoff',
					'name'  => 'current_logo',
					'text'  => __( 'Use Website Logo Image', 'ti-woocommerce-wishlist-premium' ),
					'std'   => true,
					'extra' => array(
						'tiwl-show' => '.tiwl-notifications-style-logo',
						'tiwl-hide' => '.tiwl-notifications-style-upload',
					),
				),
				array(
					'type'  => 'html',
					'name'  => 'logo_current',
					'text'  => __( 'Logo Image', 'ti-woocommerce-wishlist-premium' ),
					'std'   => '<i class="logo_heart"></i>',
					'class' => 'tiwl-notifications-style-logo',
				),
				array(
					'type'  => 'uploadfile',
					'name'  => 'logo',
					'text'  => __( 'Logo Image', 'ti-woocommerce-wishlist-premium' ),
					'std'   => '',
					'class' => 'tiwl-notifications-style-upload',
					'extra' => array(
						'button' => array(
							'value' => __( 'Upload', 'ti-woocommerce-wishlist-premium' ),
						),
						'type'   => array( 'image' ),
					),
				),
				array(
					'type' => 'textarea',
					'name' => 'logo_text',
					'text' => __( 'Logo Text', 'ti-woocommerce-wishlist-premium' ),
					'std'  => '<strong>' . get_bloginfo( 'name' ) . '</strong>',
				),
				array(
					'type'  => 'uploadfile',
					'name'  => 'header_image',
					'text'  => __( 'Header Image', 'ti-woocommerce-wishlist-premium' ),
					'desc'  => __( 'You can add header image between logo and email content if allowed in email template.', 'ti-woocommerce-wishlist-premium' ),
					'std'   => '',
					'extra' => array(
						'button' => array(
							'value' => __( 'Upload', 'ti-woocommerce-wishlist-premium' ),
						),
						'type'   => array( 'image' ),
					),
				),
				array(
					'type' => 'textarea',
					'name' => 'footer_text',
					'text' => __( 'Footer Text', 'ti-woocommerce-wishlist-premium' ),
					'std'  => 'Copyright © ' . (int) date( 'Y' ) . '. All rights reserved.',
				),
				array(
					'type'       => 'group',
					'title'      => __( 'Social Icons', 'ti-woocommerce-wishlist-premium' ),
					'desc'       => __( 'Add you social account name to show social icons in email footer area', 'ti-woocommerce-wishlist-premium' ),
					'show_names' => true,
					'skin'       => 'section-group-style-e',
				),
				array(
					'type' => 'text',
					'name' => 'facebook',
					'text' => __( 'Facebook', 'ti-woocommerce-wishlist-premium' ),
					'std'  => '',
					'skin' => 'section-field-style',
				),
				array(
					'type' => 'text',
					'name' => 'twitter',
					'text' => __( 'twitter', 'ti-woocommerce-wishlist-premium' ),
					'std'  => '',
					'skin' => 'section-field-style',
				),
				array(
					'type' => 'text',
					'name' => 'pinterest',
					'text' => __( 'Pinterest', 'ti-woocommerce-wishlist-premium' ),
					'std'  => '',
					'skin' => 'section-field-style',
				),
				array(
					'type' => 'text',
					'name' => 'google+',
					'text' => __( 'Google+', 'ti-woocommerce-wishlist-premium' ),
					'std'  => '',
					'skin' => 'section-field-style',
				),
				array(
					'type'       => 'group',
					'title'      => __( 'Color Options', 'ti-woocommerce-wishlist-premium' ),
					'show_names' => true,
					'skin'       => 'section-group-style-e',
				),
				array(
					'type' => 'color',
					'name' => 'main',
					'text' => __( 'Main color', 'ti-woocommerce-wishlist-premium' ),
					'std'  => '#ff5739',
					'skin' => 'section-field-style',
				),
				array(
					'type' => 'color',
					'name' => 'background',
					'text' => __( 'Email Background color', 'ti-woocommerce-wishlist-premium' ),
					'std'  => '#ffffff',
					'skin' => 'section-field-style',
				),
				array(
					'type' => 'color',
					'name' => 'background_content',
					'text' => __( 'Content Background Color', 'ti-woocommerce-wishlist-premium' ),
					'std'  => '#ffffff',
					'skin' => 'section-field-style',
				),
				array(
					'type' => 'color',
					'name' => 'title',
					'text' => __( 'Title Color', 'ti-woocommerce-wishlist-premium' ),
					'std'  => '#291c09',
					'skin' => 'section-field-style',
				),
				array(
					'type' => 'color',
					'name' => 'content',
					'text' => __( 'Content text color', 'ti-woocommerce-wishlist-premium' ),
					'std'  => '#000000',
					'skin' => 'section-field-style',
				),
			),
		);
		add_action( 'tinvwl_notification_prepare_fields', array( $this, 'full_content' ), 20 );
		$sections[] = array(
			'id'         => 'notification_price_email',
			'title'      => __( 'Wishlist Notification of change price', 'ti-woocommerce-wishlist-premium' ),
			'desc'       => __( 'If the product price is changed, customers that have this product in their Wishlist will receive the email notification.', 'ti-woocommerce-wishlist-premium' ),
			'show_names' => true,
			/**
			 * Field load from mail class
			 *
			 * @see  TInvWL_Public_Email_NotificationPrice::init_form_fields()
			 * @link \public\email\notificationprice.class.php TInvWL_Public_Email_NotificationPrice
			 * @see  tinv_prepare_fields
			 * @link tinv-wishlists-function.php
			 */
			'fields'     => $this->fields_email_prepare( $this->_name . '_Public_Email_NotificationPrice' ),
		);
		$sections[] = array(
			'id'         => 'notification_stock_email',
			'title'      => __( 'Wishlist Notification of change stock', 'ti-woocommerce-wishlist-premium' ),
			'desc'       => __( 'If the product stock status is changed, customers that have this product in their Wishlist will receive the email notification.', 'ti-woocommerce-wishlist-premium' ),
			'show_names' => true,
			/**
			 * Field load from mail class
			 *
			 * @see  TInvWL_Public_Email_NotificationStock::init_form_fields()
			 * @link \public\email\notificationstock.class.php TInvWL_Public_Email_NotificationStock
			 * @see  tinv_prepare_fields
			 * @link tinv-wishlists-function.php
			 */
			'fields'     => $this->fields_email_prepare( $this->_name . '_Public_Email_NotificationStock' ),
		);

		$sections[] = array(
			'id'         => 'notification_low_stock_email',
			'title'      => __( 'Wishlist Notification of low stock product', 'ti-woocommerce-wishlist-premium' ),
			'desc'       => __( 'If the product is low on stock, customers that have this product in their Wishlist will receive the email notification.', 'ti-woocommerce-wishlist-premium' ),
			'show_names' => true,
			/**
			 * Field load from mail class
			 *
			 * @see  TInvWL_Public_Email_NotificationLowStock::init_form_fields()
			 * @link \public\email\notificationlowstock.class.php TInvWL_Public_Email_NotificationLowStock
			 * @see  tinv_prepare_fields
			 * @link tinv-wishlists-function.php
			 */
			'fields'     => $this->fields_email_prepare( $this->_name . '_Public_Email_NotificationLowStock' ),
		);

		remove_action( $this->_name . '_notification_prepare_fields', array( $this, 'full_content' ), 20 );
		$templates  = array(
			'TInvWL_Public_Email_Estimate'             => TInvWL_Public_Email::gettemplates( 'ti-estimate' ),
			'TInvWL_Public_Email_Promotional'          => TInvWL_Public_Email::gettemplates( 'ti-promotional' ),
			'TInvWL_Public_Email_NotificationPrice'    => TInvWL_Public_Email::gettemplates( 'ti-notification-price' ),
			'TInvWL_Public_Email_NotificationStock'    => TInvWL_Public_Email::gettemplates( 'ti-notification-stock' ),
			'TInvWL_Public_Email_NotificationLowStock' => TInvWL_Public_Email::gettemplates( 'ti-notification-low-stock' ),
		);
		$sections[] = array(
			'id'         => 'notification_template',
			'title'      => __( 'Email Templates', 'ti-woocommerce-wishlist-premium' ),
			'desc'       => __( 'Here you can preview all wishlist emails. Also you can choose email template for promotional email.', 'ti-woocommerce-wishlist-premium' ),
			'show_names' => true,
			'fields'     => array(
				array(
					'type'    => 'previewselect',
					'name'    => 'TInvWL_Public_Email_Estimate',
					'text'    => __( 'Choose Estimate Email Template', 'ti-woocommerce-wishlist-premium' ),
					'std'     => '',
					'style'   => 'display:none;',
					'options' => $templates['TInvWL_Public_Email_Estimate'],
					'class'   => 'tiwl-estimate-sett-allow',
					'extra'   => array(
						'url'    => $this->admin_url( 'previewemail', '', array(
							'class'         => 'Estimate',
							'email'         => '%s',
							'default'       => 1,
							'_tinvwl_nonce' => wp_create_nonce( sprintf( '%s-%s', $this->_name, 'previewemail' ) )
						) ),
						'select' => ( 1 < count( $templates['TInvWL_Public_Email_Estimate'] ) ? array( 'class' => 'form-control' ) : array(
							'style' => 'display:none',
							'class' => 'tinvwl-empty-select'
						) ),
					),
				),
				array(
					'type'    => 'previewselect',
					'name'    => 'TInvWL_Public_Email_Promotional',
					'text'    => __( 'Choose Promotional Email Template', 'ti-woocommerce-wishlist-premium' ),
					'std'     => '',
					'options' => $templates['TInvWL_Public_Email_Promotional'],
					'extra'   => array(
						'url'    => $this->admin_url( 'previewemail', '', array(
							'class'         => 'Promotional',
							'email'         => '%s',
							'default'       => 1,
							'_tinvwl_nonce' => wp_create_nonce( sprintf( '%s-%s', $this->_name, 'previewemail' ) ),
						) ),
						'select' => ( 1 < count( $templates['TInvWL_Public_Email_Promotional'] ) ? array( 'class' => 'form-control' ) : array(
							'style' => 'display:none',
							'class' => 'tinvwl-empty-select',
						) ),
					),
				),
				array(
					'type'    => 'previewselect',
					'name'    => 'TInvWL_Public_Email_NotificationPrice',
					'text'    => __( 'Choose Price Notification Email Template', 'ti-woocommerce-wishlist-premium' ),
					'std'     => '',
					'options' => $templates['TInvWL_Public_Email_NotificationPrice'],
					'extra'   => array(
						'url'    => $this->admin_url( 'previewemail', '', array(
							'class'         => 'NotificationPrice',
							'email'         => '%s',
							'default'       => 1,
							'_tinvwl_nonce' => wp_create_nonce( sprintf( '%s-%s', $this->_name, 'previewemail' ) ),
						) ),
						'select' => ( 1 < count( $templates['TInvWL_Public_Email_NotificationPrice'] ) ? array( 'class' => 'form-control' ) : array(
							'style' => 'display:none',
							'class' => 'tinvwl-empty-select',
						) ),
					),
				),
				array(
					'type'    => 'previewselect',
					'name'    => 'TInvWL_Public_Email_NotificationStock',
					'text'    => __( 'Choose Stock Notification Email Template', 'ti-woocommerce-wishlist-premium' ),
					'std'     => '',
					'options' => $templates['TInvWL_Public_Email_NotificationStock'],
					'extra'   => array(
						'url'    => $this->admin_url( 'previewemail', '', array(
							'class'         => 'NotificationStock',
							'email'         => '%s',
							'default'       => 1,
							'_tinvwl_nonce' => wp_create_nonce( sprintf( '%s-%s', $this->_name, 'previewemail' ) ),
						) ),
						'select' => ( 1 < count( $templates['TInvWL_Public_Email_NotificationStock'] ) ? array( 'class' => 'form-control' ) : array(
							'style' => 'display:none',
							'class' => 'tinvwl-empty-select',
						) ),
					),
				),
				array(
					'type'    => 'previewselect',
					'name'    => 'TInvWL_Public_Email_NotificationLowStock',
					'text'    => __( 'Choose Low Stock Notification Email Template', 'ti-woocommerce-wishlist-premium' ),
					'std'     => '',
					'options' => $templates['TInvWL_Public_Email_NotificationLowStock'],
					'extra'   => array(
						'url'    => $this->admin_url( 'previewemail', '', array(
							'class'         => 'NotificationLowStock',
							'email'         => '%s',
							'default'       => 1,
							'_tinvwl_nonce' => wp_create_nonce( sprintf( '%s-%s', $this->_name, 'previewemail' ) ),
						) ),
						'select' => ( 1 < count( $templates['TInvWL_Public_Email_NotificationLowStock'] ) ? array( 'class' => 'form-control' ) : array(
							'style' => 'display:none',
							'class' => 'tinvwl-empty-select',
						) ),
					),
				),
				array(
					'type' => 'html',
					'name' => 'logo_current',
					'std'  => '<div class="tinvwl-notification-preview-emails tinvwl-modal">
<div class="tinvwl-overlay"></div>
<div class="tinvwl-table">
<div class="tinvwl-cell">
<div class="tinvwl-modal-inner">
<p class="ti-content">' . __( 'You are about to change promotional email template. All changes in promotional email settings and content will be lost!', 'ti-woocommerce-wishlist-premium' ) . '</p>
<a class="tinvwl-btn large tinvwl-close-modal tinvwl-continue" href="javascript:void(0)">' . __( 'Continue', 'ti-woocommerce-wishlist-premium' ) . '</a>
<a class="tinvwl-btn black large tinvwl-close-modal tinvwl-close" href="javascript:void(0)">' . __( 'Cancel', 'ti-woocommerce-wishlist-premium' ) . '</a>
</div>
</div>
</div>
</div>',
				),
			),
		);
		$sections[] = array(
			'id'     => 'save_buttons',
			'class'  => 'only-button',
			'noform' => true,
			'fields' => array(
				array(
					'type'  => 'button_submit',
					'name'  => 'setting_save',
					'std'   => '<span><i class="ftinvwl ftinvwl-check"></i></span>' . __( 'Save Settings', 'ti-woocommerce-wishlist-premium' ),
					'extra' => array( 'class' => 'tinvwl-btn split status-btn-ok' ),
				),
				array(
					'type'  => 'button_submit',
					'name'  => 'setting_reset',
					'std'   => '<span><i class="ftinvwl ftinvwl-times"></i></span>' . __( 'Reset', 'ti-woocommerce-wishlist-premium' ),
					'extra' => array( 'class' => 'tinvwl-btn split status-btn-ok tinvwl-confirm-reset' ),
				),
				array(
					'type' => 'button_submit_quick',
					'name' => 'setting_save_quick',
					'std'  => '<span><i class="ftinvwl ftinvwl-floppy-o"></i></span>' . __( 'Save', 'ti-woocommerce-wishlist-premium' ),
				),
			),
		);

		return $sections;
	}

	/**
	 * Create email fields
	 *
	 * @param string $class Name email class.
	 *
	 * @return array
	 * @see tinv_prepare_fields
	 */
	function fields_email_prepare( $class ) {
		$form_fields = array();

		if ( array_key_exists( $class, (array) @$this->email_settings->parent_settings ) ) { // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
			$form_fields = $this->email_settings->parent_settings[ $class ]->get_form_fields();
		}

		$form_fields = apply_filters( 'tinvwl_email_prepare_fields', $form_fields );

		return apply_filters( 'tinvwl_notification_prepare_fields', $form_fields, $class );
	}

	/**
	 * Hide Enabled field in estimate_email
	 *
	 * @param array $fields Array of fields.
	 */
	function hide_enabled( $fields = array() ) {
		if ( ! is_array( $fields ) ) {
			return $fields;
		}
		foreach ( $fields as &$field ) {
			if ( isset( $field['name'] ) && 'enabled' == $field['name'] ) { // WPCS: loose comparison ok.
				if ( array_key_exists( 'style', $field ) ) {
					$field['style'] .= 'display:none;';
				} else {
					$field['style'] = 'display:none;';
				}
			}
		}

		return $fields;
	}

	/**
	 * Save value to database
	 *
	 * @param array $data Post section data.
	 */
	function constructor_save( $data ) {
		if ( empty( $data ) || ! is_array( $data ) ) {
			return false;
		}
		if ( filter_input( INPUT_POST, 'save_buttons-setting_reset' ) ) {
			foreach ( array_keys( $data ) as $key ) {
				if ( ! in_array( $key, array( 'notification_template' ) ) ) {
					$data[ $key ] = array();
				}
			}
		}
		if ( array_key_exists( 'notification_template', $data ) ) {
			$templates   = $data['notification_template'];
			$curtemplate = tinv_get_option( 'notification_template', 'TInvWL_Public_Email_Promotional' );
			if ( array_key_exists( 'TInvWL_Public_Email_Promotional', $templates ) ) {
				$template = $templates['TInvWL_Public_Email_Promotional'];
				if ( $curtemplate !== $template ) {
					$data['promotional_email']['content'] = tinv_wishlist_template_html( sprintf( implode( DIRECTORY_SEPARATOR, array(
						'emails',
						'ti-contentpromotional%s.php',
					) ), $template ) );
					$data['notifications_style']          = apply_filters( 'tinvwl_preview_email_defaultstyle', $data['notifications_style'], 'TInvWL_Public_Email_Promotional', $template );
				}
				tinv_update_option( 'promotional_email_tmp', 'content', null );
			}
		}

		if ( ! empty( $data['estimate_email'] ) ) {
			update_option( 'woocommerce_' . $this->_name . '_estimate_email_settings', $data['estimate_email'] );
		}
		if ( ! empty( $data['promotional_email'] ) ) {
			update_option( 'woocommerce_' . $this->_name . '_promotional_email_settings', $data['promotional_email'] );
		}
		if ( ! empty( $data['notification_price_email'] ) ) {
			update_option( 'woocommerce_' . $this->_name . '_notification_price_email_settings', $data['notification_price_email'] );
		}
		if ( ! empty( $data['notification_stock_email'] ) ) {
			update_option( 'woocommerce_' . $this->_name . '_notification_stock_email_settings', $data['notification_stock_email'] );
		}
		if ( ! empty( $data['notification_low_stock_email'] ) ) {
			update_option( 'woocommerce_' . $this->_name . '_notification_low_stock_email_settings', $data['notification_stock_email'] );
		}

		parent::constructor_save( $data );

		$allow = tinv_get_option( 'estimate_button', 'allow' );
		if ( ! $allow ) {
			tinv_update_option( 'estimate_button', 'notes', $allow );
		}
		tinv_update_option( 'estimate_email', 'enabled', $allow );
	}

	/**
	 * Modify class for content field
	 *
	 * @param array $fields Fields for email.
	 *
	 * @return array
	 */
	function full_content( $fields ) {
		if ( empty( $fields ) ) {
			return $fields;
		}
		foreach ( $fields as &$field ) {
			if ( 'textarea' === $field['type'] ) {
				$field['class'] = 'tinvwl-full-width';
			}
		}

		return $fields;
	}
}
