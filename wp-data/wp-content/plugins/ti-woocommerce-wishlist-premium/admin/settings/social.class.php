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
class TInvWL_Admin_Settings_Social extends TInvWL_Admin_BaseSection {

	/**
	 * Priority for admin menu
	 *
	 * @var integer
	 */
	public $priority = 50;

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
			'title'      => __( 'Sharing Options', 'ti-woocommerce-wishlist-premium' ),
			'page_title' => __( 'Social Networks Sharing Options', 'ti-woocommerce-wishlist-premium' ),
			'method'     => array( $this, '_print_' ),
			'slug'       => 'social-settings',
			'capability' => 'tinvwl_sharing_options',
		);
	}

	/**
	 * Create Scetions for this settings
	 *
	 * @return array
	 */
	function constructor_data() {
		return array(
			array(
				'id'         => 'social',
				'show_names' => false,
				'fields'     => array(
					array(
						'type'  => 'group',
						'id'    => 'social',
						'desc'  => __( 'Following options enable/disable Social share icons below wishlist table on wishlist page. Wishlist owner can easily share their wishlists using this button on social networks. Wishlist privacy should be set to public or shared status, private wishlists can\'t be shared.', 'ti-woocommerce-wishlist-premium' ),
						'class' => 'tinvwl-info-top',
					),
					array(
						'type'  => 'html',
						'name'  => 'social',
						'text'  => __( 'Social Networks Sharing Options', 'ti-woocommerce-wishlist-premium' ),
						'class' => 'tinvwl-header-row tinvwl-line-border',
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'facebook',
						'text' => __( 'Show "Facebook" Button', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'twitter',
						'text' => __( 'Show "Twitter" Button', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'pinterest',
						'text' => __( 'Show "Pinterest" Button', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'whatsapp',
						'text' => __( 'Show "Share via WhatsApp" Button', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'clipboard',
						'text' => __( 'Show "Copy URL to clipboard" Button', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'email',
						'text' => __( 'Show "Share by Email" Button', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'text',
						'name' => 'share_on',
						'text' => __( '"Share on" Text', 'ti-woocommerce-wishlist-premium' ),
						'std'  => 'Share on',
					),
					array(
						'type'     => 'select',
						'name'     => 'icon_style',
						'text'     => __( 'Social Icons Color', 'ti-woocommerce-wishlist-premium' ),
						'std'      => '',
						'options'  => array(
							''      => __( 'Use font color', 'ti-woocommerce-wishlist-premium' ),
							'dark'  => __( 'Dark', 'ti-woocommerce-wishlist-premium' ),
							'white' => __( 'White', 'ti-woocommerce-wishlist-premium' ),
						),
						'validate' => FILTER_DEFAULT,
					),
				),
			),
			array(
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
			),
		);
	}

	/**
	 * Save value to database
	 *
	 * @param array $data Post section data.
	 */
	function constructor_save( $data ) {
		if ( filter_input( INPUT_POST, 'save_buttons-setting_reset' ) ) {
			foreach ( array_keys( $data ) as $key ) {
				$data[ $key ] = array();
			}
		}
		parent::constructor_save( $data );
	}
}
