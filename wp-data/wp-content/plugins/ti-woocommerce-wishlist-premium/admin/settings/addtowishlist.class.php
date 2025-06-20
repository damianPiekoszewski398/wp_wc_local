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
class TInvWL_Admin_Settings_Addtowishlist extends TInvWL_Admin_BaseSection {

	/**
	 * Priority for admin menu
	 *
	 * @var integer
	 */
	public $priority = 30;

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
			'title'      => __( 'Button Options', 'ti-woocommerce-wishlist-premium' ),
			'page_title' => __( '"Add to Wishlist" Button Options', 'ti-woocommerce-wishlist-premium' ),
			'method'     => array( $this, '_print_' ),
			'slug'       => 'button-settings',
			'capability' => 'tinvwl_button_settings',
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
				'id'         => 'add_to_wishlist',
				'title'      => __( 'Product page Button Settings', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
				'fields'     => array(
					array(
						'type'    => 'select',
						'name'    => 'position',
						'text'    => __( 'Button position', 'ti-woocommerce-wishlist-premium' ),
						'desc'    => __( 'Add this shortcode <code>[ti_wishlists_addtowishlist]</code> anywhere on product page, if you have chosen custom position for product button. You will have to do this for each product.', 'ti-woocommerce-wishlist-premium' ),
						'std'     => 'after',
						'options' => array(
							'after'      => __( 'After "Add to Cart" button', 'ti-woocommerce-wishlist-premium' ),
							'before'     => __( 'Before "Add to Cart" button', 'ti-woocommerce-wishlist-premium' ),
							'thumbnails' => __( 'After Thumbnails', 'ti-woocommerce-wishlist-premium' ),
							'summary'    => __( 'After summary', 'ti-woocommerce-wishlist-premium' ),
							'shortcode'  => __( 'Custom position with code', 'ti-woocommerce-wishlist-premium' ),
						),
					),
					array(
						'type'  => 'text',
						'name'  => 'class',
						'text'  => __( 'Button custom CSS class', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'You can add custom CSS classes to button markup separated by spaces. Most of themes using <code>button</code> class for this type of buttons.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => '',
						'extra' => array(
							'placeholder' => 'button btn-primary',
						),
					),
					array(
						'type'    => 'select',
						'name'    => 'icon',
						'text'    => __( '"Add to Wishlist" Icon', 'ti-woocommerce-wishlist-premium' ),
						'desc'    => __( 'You can choose from our predefined icons or upload your custom icon. Custom icon size is limited to 16x16 px.', 'ti-woocommerce-wishlist-premium' ),
						'std'     => 'heart',
						'options' => array(
							''           => __( 'None', 'ti-woocommerce-wishlist-premium' ),
							'heart'      => __( 'Heart', 'ti-woocommerce-wishlist-premium' ),
							'heart-plus' => __( 'Heart+', 'ti-woocommerce-wishlist-premium' ),
							'custom'     => __( 'Custom', 'ti-woocommerce-wishlist-premium' ),
						),
						'extra'   => array(
							'class'      => 'tiwl-button-icon',
							'tiwl-show'  => '.tiwl-button-icon-custom',
							'tiwl-hide'  => '.tiwl-button-icon-style',
							'tiwl-value' => 'custom',
						),
					),
					array(
						'type'  => 'uploadfile',
						'name'  => 'icon_upload',
						'std'   => '',
						'text'  => __( 'Default state', 'ti-woocommerce-wishlist-premium' ),
						'class' => 'tiwl-button-icon-custom',
						'extra' => array(
							'button' => array(
								'value' => __( 'Upload', 'ti-woocommerce-wishlist-premium' ),
							),
							'type'   => array( 'image' ),
						),
					),
					array(
						'type'  => 'uploadfile',
						'name'  => 'icon_upload_added',
						'std'   => '',
						'text'  => __( 'Already added state', 'ti-woocommerce-wishlist-premium' ),
						'class' => 'tiwl-button-icon-custom',
						'extra' => array(
							'button' => array(
								'value' => __( 'Upload', 'ti-woocommerce-wishlist-premium' ),
							),
							'type'   => array( 'image' ),
						),
					),
					array(
						'type'          => 'select',
						'name'          => 'icon_style',
						'std'           => '',
						'text'          => __( '"Add to Wishlist" Icon Color', 'ti-woocommerce-wishlist-premium' ),
						'options'       => array(
							''      => __( 'Use font color', 'ti-woocommerce-wishlist-premium' ),
							'black' => __( 'Black', 'ti-woocommerce-wishlist-premium' ),
							'white' => __( 'White', 'ti-woocommerce-wishlist-premium' ),
						),
						'tiwl-required' => array(
							'add_to_wishlist-icon' => array( 'heart', 'heart-plus' ),
						),
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'show_preloader',
						'text' => __( 'Show preloader', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'If enabled, applies animation for the button icon until product adding or removing processed. (Usable for servers with slow connection mostly.)', 'ti-woocommerce-wishlist-premium' ),
						'std'  => false,
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'show_text',
						'text'  => __( 'Show button text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => array(
							'tiwl-show' => '.tiwl-button-text',
						),
					),
					array(
						'type'  => 'group',
						'id'    => 'show_text_single',
						'class' => 'tiwl-button-text',
						'style' => 'border-top: 0px; padding-top: 0px;',
					),
					array(
						'type' => 'text',
						'name' => 'text',
						'text' => __( '"Add to Wishlist" button Text', 'ti-woocommerce-wishlist-premium' ),
						'std'  => 'Add to Wishlist',
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'already_on',
						'text'  => __( 'Show "Already In Wishlist" button Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => false,
						'extra' => array(
							'tiwl-show' => '.tiwl-button-already-on',
						),
						'style' => tinv_get_option_admin( 'general', 'simple_flow' ) ? 'display:none' : '',
					),
					array(
						'type'  => 'text',
						'name'  => 'text_already_on',
						'text'  => __( '"Already In Wishlist" button Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 'Already In Wishlist',
						'class' => 'tiwl-button-already-on',
						'style' => tinv_get_option_admin( 'general', 'simple_flow' ) ? 'display:none' : '',
					),
					array(
						'type'  => 'text',
						'name'  => 'text_remove',
						'text'  => __( '"Remove from Wishlist" Button Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 'Remove from Wishlist',
						'style' => tinv_get_option_admin( 'general', 'simple_flow' ) ? '' : 'display:none',
					),
				),
			),
			array(
				'id'         => 'add_to_wishlist_catalog',
				'title'      => __( 'Product Listing page Button Settings', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => __( 'This is separate settings for "Add to wishlist" button on product listing (Shop page, categories, etc.). You can also adjust button and text colors, size, etc. in <code>TI Wishlist > Style Options.</code>', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
				'fields'     => array(
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'show_in_loop',
						'text'  => __( 'Show in Product Listing', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => array(
							'tiwl-show' => '.tiwl-buttoncat-button',
						),
					),
					array(
						'type'  => 'group',
						'id'    => 'add_to_wishlist_catalog',
						'class' => 'tiwl-buttoncat-button',
						'style' => 'border-top: 0px; padding-top: 0px;',
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'default_variation',
						'text' => __( 'Show for variable products', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'Will show "Add to wishlist" button for variable products if product variations are set by default.', 'ti-woocommerce-wishlist-premium' ),
						'std'  => false,
					),
					array(
						'type'    => 'select',
						'name'    => 'position',
						'text'    => __( 'Button position', 'ti-woocommerce-wishlist-premium' ),
						'std'     => 'after',
						'options' => array(
							'after'       => __( 'After "Add to Cart" button', 'ti-woocommerce-wishlist-premium' ),
							'before'      => __( 'Before "Add to Cart" button', 'ti-woocommerce-wishlist-premium' ),
							'above_thumb' => __( 'Above Thumbnail', 'ti-woocommerce-wishlist-premium' ),
							'shortcode'   => __( 'Custom position with code', 'ti-woocommerce-wishlist-premium' ),
						),
					),
					array(
						'type'  => 'text',
						'name'  => 'class',
						'text'  => __( 'Button custom CSS class', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'You can add custom CSS classes to button markup separated by spaces. Most of themes using <code>button</code> class for this type of buttons.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => '',
						'extra' => array(
							'placeholder' => 'button btn-primary',
						),
					),
					array(
						'type'    => 'select',
						'name'    => 'icon',
						'text'    => __( '"Add to Wishlist" Icon', 'ti-woocommerce-wishlist-premium' ),
						'std'     => 'heart',
						'options' => array(
							''           => __( 'None', 'ti-woocommerce-wishlist-premium' ),
							'heart'      => __( 'Heart', 'ti-woocommerce-wishlist-premium' ),
							'heart-plus' => __( 'Heart+', 'ti-woocommerce-wishlist-premium' ),
							'custom'     => __( 'Custom', 'ti-woocommerce-wishlist-premium' ),
						),
						'extra'   => array(
							'tiwl-show'  => '.tiwl-buttoncat-icon-custom',
							'tiwl-hide'  => '.tiwl-buttoncat-icon-style',
							'tiwl-value' => 'custom',
						),
						'desc'    => __( 'You can choose from our predefined icons or upload your custom icon. Custom icon size is limited to 16x16 px.', 'ti-woocommerce-wishlist-premium' ),
					),
					array(
						'type'  => 'uploadfile',
						'name'  => 'icon_upload',
						'std'   => '',
						'text'  => __( 'Default state', 'ti-woocommerce-wishlist-premium' ),
						'class' => 'tiwl-buttoncat-icon-custom',
						'extra' => array(
							'button' => array(
								'value' => __( 'Upload', 'ti-woocommerce-wishlist-premium' ),
							),
							'type'   => array( 'image' ),
						),
					),
					array(
						'type'  => 'uploadfile',
						'name'  => 'icon_upload_added',
						'std'   => '',
						'text'  => __( 'Already added state', 'ti-woocommerce-wishlist-premium' ),
						'class' => 'tiwl-buttoncat-icon-custom',
						'extra' => array(
							'button' => array(
								'value' => __( 'Upload', 'ti-woocommerce-wishlist-premium' ),
							),
							'type'   => array( 'image' ),
						),
					),
					array(
						'type'          => 'select',
						'name'          => 'icon_style',
						'std'           => '',
						'text'          => __( '"Add to Wishlist" Icon Color', 'ti-woocommerce-wishlist-premium' ),
						'options'       => array(
							''      => __( 'Use font color', 'ti-woocommerce-wishlist-premium' ),
							'black' => __( 'Black', 'ti-woocommerce-wishlist-premium' ),
							'white' => __( 'White', 'ti-woocommerce-wishlist-premium' ),
						),
						'tiwl-required' => array(
							'add_to_wishlist_catalog-icon' => array( 'heart', 'heart-plus' ),
						),
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'show_preloader',
						'text' => __( 'Show preloader', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'If enabled, applies animation for the button icon until product adding or removing processed. (Usable for servers with slow connection mostly.)', 'ti-woocommerce-wishlist-premium' ),
						'std'  => false,
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'show_text',
						'text'  => __( 'Show button text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => array(
							'tiwl-show' => '.tiwl-button-text-catalog',
						),
					),
					array(
						'type'  => 'group',
						'id'    => 'show_text_catalog',
						'class' => 'tiwl-button-text-catalog',
						'style' => 'border-top: 0px; padding-top: 0px;',
					),
					array(
						'type' => 'text',
						'name' => 'text',
						'text' => __( '"Add to Wishlist" Text', 'ti-woocommerce-wishlist-premium' ),
						'std'  => 'Add to Wishlist',
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'already_on',
						'text'  => __( 'Show "Already In Wishlist" button Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => false,
						'extra' => array(
							'tiwl-show' => '.tiwl-button-already-on-catalog',
						),
						'style' => tinv_get_option_admin( 'general', 'simple_flow' ) ? 'display:none' : '',
					),
					array(
						'type'  => 'text',
						'name'  => 'text_already_on',
						'text'  => __( '"Already In Wishlist" button Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 'Already In Wishlist',
						'class' => 'tiwl-button-already-on-catalog',
						'style' => tinv_get_option_admin( 'general', 'simple_flow' ) ? 'display:none' : '',
					),
					array(
						'type'  => 'text',
						'name'  => 'text_remove',
						'text'  => __( '"Remove from Wishlist" Button Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 'Remove from Wishlist',
						'style' => tinv_get_option_admin( 'general', 'simple_flow' ) ? '' : 'display:none',
					),
				),
			),
			array(
				'id'         => 'add_to_wishlist_cart',
				'title'      => __( 'Cart page Button Settings', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => __( 'This is separate settings for "Save for Later" button on a Cart page. You can also adjust button and text colors, size, etc. in <code>TI Wishlist > Style Options.</code>', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
				'fields'     => array(
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'item_show_in_cart',
						'text'  => __( 'Show button for Cart Items', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'desc'  => __( 'Enables/disables "Save for Later" button for every cart item in a table.', 'ti-woocommerce-wishlist-premium' ),
						'extra' => array(
							'tiwl-show' => '.tiwl-buttoncart-item-button',
						),
					),
					array(
						'type'  => 'group',
						'id'    => 'add_to_wishlist_in_cart_item',
						'class' => 'tiwl-buttoncart-item-button',
						'style' => 'border-top: 0px; padding-top: 0px;',
					),
					array(
						'type'  => 'text',
						'name'  => 'item_class',
						'text'  => __( 'Button custom CSS class', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'You can add custom CSS classes to button markup separated by spaces. Most of themes using <code>button</code> class for this type of buttons.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => '',
						'extra' => array(
							'placeholder' => 'button btn-primary',
						),
					),
					array(
						'type'    => 'select',
						'name'    => 'item_icon',
						'text'    => __( '"Save for Later" Icon', 'ti-woocommerce-wishlist-premium' ),
						'std'     => 'heart',
						'options' => array(
							''           => __( 'None', 'ti-woocommerce-wishlist-premium' ),
							'heart'      => __( 'Heart', 'ti-woocommerce-wishlist-premium' ),
							'heart-plus' => __( 'Heart+', 'ti-woocommerce-wishlist-premium' ),
							'custom'     => __( 'Custom', 'ti-woocommerce-wishlist-premium' ),
						),
						'desc'    => __( 'You can choose from our predefined icons or upload your custom icon. Custom icon size is limited to 16x16 px.', 'ti-woocommerce-wishlist-premium' ),
					),
					array(
						'type'          => 'uploadfile',
						'name'          => 'item_icon_upload',
						'std'           => '',
						'text'          => ' ',
						'extra'         => array(
							'button' => array(
								'value' => __( 'Upload', 'ti-woocommerce-wishlist-premium' ),
							),
							'type'   => array( 'image' ),
						),
						'tiwl-required' => array(
							'add_to_wishlist_cart-item_icon' => 'custom',
						),
					),
					array(
						'type'          => 'select',
						'name'          => 'item_icon_style',
						'std'           => '',
						'text'          => __( '"Save for Later" Icon Color', 'ti-woocommerce-wishlist-premium' ),
						'options'       => array(
							''      => __( 'Use font color', 'ti-woocommerce-wishlist-premium' ),
							'black' => __( 'Black', 'ti-woocommerce-wishlist-premium' ),
							'white' => __( 'White', 'ti-woocommerce-wishlist-premium' ),
						),
						'tiwl-required' => array(
							'add_to_wishlist_cart-item_icon' => array( 'heart', 'heart-plus' ),
						),
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'item_show_preloader',
						'text' => __( 'Show preloader', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'If enabled, applies animation for the button icon until product adding or removing processed. (Usable for servers with slow connection mostly.)', 'ti-woocommerce-wishlist-premium' ),
						'std'  => false,
					),
					array(
						'type' => 'text',
						'name' => 'item_text',
						'text' => __( '"Save for Later" Text', 'ti-woocommerce-wishlist-premium' ),
						'std'  => 'Save for later',
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'item_remove_from_cart',
						'text' => __( 'Remove from cart if added to Wishlist', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'Remove from cart when products are added to wishlist.', 'ti-woocommerce-wishlist-premium' ),
						'std'  => false,
					),
					array(
						'type' => 'group',
						'id'   => 'show_in_cart',
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'show_in_cart',
						'text'  => __( 'Show button for Cart', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'desc'  => __( 'Enables/disables "Save Cart" button that allows to add all cart items to wishlist in one click.', 'ti-woocommerce-wishlist-premium' ),
						'extra' => array(
							'tiwl-show' => '.tiwl-buttoncart-button',
						),
					),
					array(
						'type'  => 'group',
						'id'    => 'add_to_wishlist_in_cart',
						'class' => 'tiwl-buttoncart-button',
						'style' => 'border-top: 0px; padding-top: 0px;',
					),
					array(
						'type'  => 'text',
						'name'  => 'class',
						'text'  => __( 'Button custom CSS class', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'You can add custom CSS classes to button markup separated by spaces. Most of themes using <code>button</code> class for this type of buttons.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 'button',
						'extra' => array(
							'placeholder' => 'button btn-primary',
						),
					),
					array(
						'type'    => 'select',
						'name'    => 'icon',
						'text'    => __( '"Save Cart" Icon', 'ti-woocommerce-wishlist-premium' ),
						'std'     => 'heart',
						'options' => array(
							''           => __( 'None', 'ti-woocommerce-wishlist-premium' ),
							'heart'      => __( 'Heart', 'ti-woocommerce-wishlist-premium' ),
							'heart-plus' => __( 'Heart+', 'ti-woocommerce-wishlist-premium' ),
							'custom'     => __( 'Custom', 'ti-woocommerce-wishlist-premium' ),
						),
						'desc'    => __( 'You can choose from our predefined icons or upload your custom icon. Custom icon size is limited to 16x16 px.', 'ti-woocommerce-wishlist-premium' ),
					),
					array(
						'type'          => 'uploadfile',
						'name'          => 'icon_upload',
						'std'           => '',
						'text'          => ' ',
						'extra'         => array(
							'button' => array(
								'value' => __( 'Upload', 'ti-woocommerce-wishlist-premium' ),
							),
							'type'   => array( 'image' ),
						),
						'tiwl-required' => array(
							'add_to_wishlist_cart-icon' => 'custom',
						),
					),
					array(
						'type'          => 'select',
						'name'          => 'icon_style',
						'std'           => '',
						'text'          => __( '"Save Cart" Icon Color', 'ti-woocommerce-wishlist-premium' ),
						'options'       => array(
							''      => __( 'Use font color', 'ti-woocommerce-wishlist-premium' ),
							'black' => __( 'Black', 'ti-woocommerce-wishlist-premium' ),
							'white' => __( 'White', 'ti-woocommerce-wishlist-premium' ),
						),
						'tiwl-required' => array(
							'add_to_wishlist_cart-icon' => array( 'heart', 'heart-plus' ),
						),
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'show_preloader',
						'text' => __( 'Show preloader', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'If enabled, applies animation for the button icon until product adding or removing processed. (Usable for servers with slow connection mostly.)', 'ti-woocommerce-wishlist-premium' ),
						'std'  => false,
					),
					array(
						'type' => 'text',
						'name' => 'text',
						'text' => __( '"Save Cart" Text', 'ti-woocommerce-wishlist-premium' ),
						'std'  => 'Save Cart',
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'remove_from_cart',
						'text' => __( 'Remove from cart if added to Wishlist', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'Remove from cart when products are added to wishlist.', 'ti-woocommerce-wishlist-premium' ),
						'std'  => false,
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
		if ( 'custom' !== tinv_get_option( 'add_to_wishlist', 'icon' ) ) {
			tinv_update_option( 'add_to_wishlist', 'icon_upload', '' );
		}
	}

}
