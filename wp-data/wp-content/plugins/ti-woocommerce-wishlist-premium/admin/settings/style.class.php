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
class TInvWL_Admin_Settings_Style extends TInvWL_Admin_BaseStyle {

	/**
	 * Priority for admin menu
	 *
	 * @var integer
	 */
	public $priority = 100;

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
			'title'      => __( 'Style Options', 'ti-woocommerce-wishlist-premium' ),
			'page_title' => __( 'Wishlist Style Options', 'ti-woocommerce-wishlist-premium' ),
			'method'     => array( $this, '_print_' ),
			'slug'       => 'style-settings',
			'capability' => 'tinvwl_style_options',
		);
	}

	/**
	 * The modifiable attributes for the Default theme
	 *
	 * @return array
	 */
	function default_style_settings() {
		$font_family = apply_filters( 'tinwl_prepare_fonts', array(
			'inherit'                                                            => __( 'Use Default Font', 'ti-woocommerce-wishlist-premium' ),
			'Georgia, serif'                                                     => 'Georgia',
			"'Times New Roman', Times, serif"                                    => 'Times New Roman, Times',
			'Arial, Helvetica, sans-serif'                                       => 'Arial, Helvetica',
			"'Courier New', Courier, monospace"                                  => 'Courier New, Courier',
			"Georgia, 'Times New Roman', Times, serif"                           => 'Georgia, Times New Roman, Times',
			'Verdana, Arial, Helvetica, sans-serif'                              => 'Verdana, Arial, Helvetica',
			'Geneva, Arial, Helvetica, sans-serif'                               => 'Geneva, Arial, Helvetica',
			"'Source Sans Pro', 'Open Sans', sans-serif"                         => 'Source Sans Pro, Open Sans',
			"'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif"             => 'Helvetica Neue, Helvetica, Roboto, Arial',
			'Arial, sans-serif'                                                  => 'Arial',
			"'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif" => 'Lucida Grande, Verdana, Arial, Bitstream Vera Sans',
		) );

		return array(
			array(
				'type'       => 'group',
				'title'      => __( 'text', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-header h2',
				'element'  => 'color',
				'text'     => __( 'Title Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist .tinv-header h2',
				'element'  => 'font-size',
				'text'     => __( 'Title Font Size', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist',
				'element'  => 'color',
				'text'     => __( 'Content Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist,.tinv-wishlist button,.tinv-wishlist input,.tinv-wishlist select,.tinv-wishlist textarea,.tinv-wishlist button,.tinv-wishlist input[type="button"],.tinv-wishlist input[type="reset"],.tinv-wishlist input[type="submit"]',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'links', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist a:not(.tinvwl-button)',
				'element'  => 'color',
				'text'     => __( 'Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist a:not(.tinvwl-button):hover,.tinv-wishlist a:not(.tinvwl-button):active,.tinv-wishlist a:not(.tinvwl-button):focus',
				'element'  => 'color',
				'text'     => __( 'Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist a:not(.tinvwl-button)',
				'element'  => 'text-decoration',
				'text'     => __( 'Underline', 'ti-woocommerce-wishlist-premium' ),
				'options'  => array(
					'underline'       => __( 'Yes', 'ti-woocommerce-wishlist-premium' ),
					'none !important' => __( 'No', 'ti-woocommerce-wishlist-premium' ),
				),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist a:not(.tinvwl-button)',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'fields', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist input[type="text"],.tinv-wishlist input[type="email"],.tinv-wishlist input[type="url"],.tinv-wishlist input[type="password"],.tinv-wishlist input[type="search"],.tinv-wishlist input[type="tel"],.tinv-wishlist input[type="number"],.tinv-wishlist textarea,.tinv-wishlist select,.tinv-wishlist .product-quantity input[type="text"].qty',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist input[type="text"],.tinv-wishlist input[type="email"],.tinv-wishlist input[type="url"],.tinv-wishlist input[type="password"],.tinv-wishlist input[type="search"],.tinv-wishlist input[type="tel"],.tinv-wishlist input[type="number"],.tinv-wishlist textarea,.tinv-wishlist select,.tinv-wishlist .product-quantity input[type="text"].qty',
				'element'  => 'border-color',
				'text'     => __( 'Border Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist input[type="text"],.tinv-wishlist input[type="email"],.tinv-wishlist input[type="url"],.tinv-wishlist input[type="password"],.tinv-wishlist input[type="search"],.tinv-wishlist input[type="tel"],.tinv-wishlist input[type="number"],.tinv-wishlist textarea,.tinv-wishlist select,.tinv-wishlist .product-quantity input[type="text"].qty',
				'element'  => 'border-radius',
				'text'     => __( 'Border Radius', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist input[type="text"],.tinv-wishlist input[type="email"],.tinv-wishlist input[type="url"],.tinv-wishlist input[type="password"],.tinv-wishlist input[type="search"],.tinv-wishlist input[type="tel"],.tinv-wishlist input[type="number"],.tinv-wishlist textarea,.tinv-wishlist select,.tinv-wishlist .product-quantity input[type="text"].qty',
				'element'  => 'color',
				'text'     => __( 'Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist input[type="text"],.tinv-wishlist input[type="email"],.tinv-wishlist input[type="url"],.tinv-wishlist input[type="password"],.tinv-wishlist input[type="search"],.tinv-wishlist input[type="tel"],.tinv-wishlist input[type="number"],.tinv-wishlist textarea,.tinv-wishlist select,.tinv-wishlist .product-quantity input[type="text"].qty',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist input[type="text"],.tinv-wishlist input[type="email"],.tinv-wishlist input[type="url"],.tinv-wishlist input[type="password"],.tinv-wishlist input[type="search"],.tinv-wishlist input[type="tel"],.tinv-wishlist input[type="number"],.tinv-wishlist textarea,.tinv-wishlist .product-quantity input[type="text"].qty',
				'element'  => 'font-size',
				'text'     => __( 'Font Size', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist select',
				'element'  => 'font-size',
				'text'     => __( 'Font Size Of Select Element', 'ti-woocommerce-wishlist-premium' ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'add to wishlist catalog button', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinvwl_add_to_wishlist_button.tinvwl-skin-:before,.woocommerce ul.products li a.tinvwl-button.tinvwl_add_to_wishlist_button.tinvwl-skin-:before,.woocommerce ul.products li.product a.tinvwl-button.tinvwl_add_to_wishlist_button.tinvwl-skin-:before',
				'element'  => 'color',
				'text'     => __( 'Icon Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinvwl_add_to_wishlist_button:hover:before,.woocommerce ul.products li a.tinvwl-button.tinvwl_add_to_wishlist_button:hover:before,.woocommerce ul.products li.product a.tinvwl-button.tinvwl_add_to_wishlist_button:hover:before',
				'element'  => 'color',
				'text'     => __( 'Icon Hover/Active Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button:hover,.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button:active,.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button:focus',
				'element'  => 'background-color',
				'text'     => __( 'Background Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button',
				'element'  => 'color',
				'text'     => __( 'Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button',
				'element'  => 'color',
				'text'     => __( 'Button Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button:hover,.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button:active,.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button:focus',
				'element'  => 'color',
				'text'     => __( 'Text Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button:hover,.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button:active,.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button:focus',
				'element'  => 'color',
				'text'     => __( 'Button Text Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button',
				'element'  => 'font-size',
				'text'     => __( 'Font Size', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce ul.products li.product .tinvwl_add_to_wishlist_button.tinvwl-button',
				'element'  => 'border-radius',
				'text'     => __( 'Border Radius', 'ti-woocommerce-wishlist-premium' ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'add to wishlist product page button', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce div.product form.cart a.tinvwl_add_to_wishlist_button:before',
				'element'  => 'color',
				'text'     => __( 'Icon Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce div.product form.cart a.tinvwl_add_to_wishlist_button:hover:before',
				'element'  => 'color',
				'text'     => __( 'Icon Hover/Active Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button:hover,.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button:active,.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button:focus',
				'element'  => 'background-color',
				'text'     => __( 'Background Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button',
				'element'  => 'color',
				'text'     => __( 'Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button',
				'element'  => 'color',
				'text'     => __( 'Button Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button:hover,.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button:active,.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button:focus',
				'element'  => 'color',
				'text'     => __( 'Text Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button:hover,.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button:active,.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button:focus',
				'element'  => 'color',
				'text'     => __( 'Button Text Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button',
				'element'  => 'font-size',
				'text'     => __( 'Font Size', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce div.product form.cart .tinvwl_add_to_wishlist_button.tinvwl-button',
				'element'  => 'border-radius',
				'text'     => __( 'Border Radius', 'ti-woocommerce-wishlist-premium' ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'accent buttons style', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist button',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist button:hover,.tinv-wishlist button:focus,.tinv-wishlist input[type="button"]:hover,.tinv-wishlist input[type="button"]:focus,.tinv-wishlist input[type="reset"]:hover,.tinv-wishlist input[type="reset"]:focus,.tinv-wishlist input[type="submit"]:hover,.tinv-wishlist input[type="submit"]:focus',
				'element'  => 'background-color',
				'text'     => __( 'Background Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist button',
				'element'  => 'color',
				'text'     => __( 'Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist button:hover,.tinv-wishlist button:focus,.tinv-wishlist input[type="button"]:hover,.tinv-wishlist input[type="button"]:focus,.tinv-wishlist input[type="reset"]:hover,.tinv-wishlist input[type="reset"]:focus,.tinv-wishlist input[type="submit"]:hover,.tinv-wishlist input[type="submit"]:focus',
				'element'  => 'color',
				'text'     => __( 'Text Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist button',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist button',
				'element'  => 'font-size',
				'text'     => __( 'Font Size', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.widget .tinvwl-search-submit:before',
				'element'  => 'font-size',
				'text'     => __( 'Search Widget Font Size', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist button',
				'element'  => 'border-radius',
				'text'     => __( 'Border Radius', 'ti-woocommerce-wishlist-premium' ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'normal buttons style', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit,.woocommerce.tinv-wishlist a.tinvwl-button,.woocommerce.tinv-wishlist button.tinvwl-button,.woocommerce.tinv-wishlist input.tinvwl-button',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit:hover,.woocommerce.tinv-wishlist a.tinvwl-button:hover,.woocommerce.tinv-wishlist button.tinvwl-button:hover,.woocommerce.tinv-wishlist input.tinvwl-button:hover',
				'element'  => 'background-color',
				'text'     => __( 'Background Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit,.woocommerce.tinv-wishlist a.tinvwl-button,.woocommerce.tinv-wishlist button.tinvwl-button,.woocommerce.tinv-wishlist input.tinvwl-button',
				'element'  => 'color',
				'text'     => __( 'Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit:hover,.woocommerce.tinv-wishlist a.tinvwl-button:hover,.woocommerce.tinv-wishlist button.tinvwl-button:hover,.woocommerce.tinv-wishlist input.tinvwl-button:hover',
				'element'  => 'color',
				'text'     => __( 'Text Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit,.woocommerce.tinv-wishlist a.tinvwl-button,.woocommerce.tinv-wishlist button.tinvwl-button,.woocommerce.tinv-wishlist input.tinvwl-button',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit,.woocommerce.tinv-wishlist a.tinvwl-button,.woocommerce.tinv-wishlist button.tinvwl-button,.woocommerce.tinv-wishlist input.tinvwl-button',
				'element'  => 'font-size',
				'text'     => __( 'Font Size', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit,.woocommerce.tinv-wishlist a.tinvwl-button,.woocommerce.tinv-wishlist button.tinvwl-button,.woocommerce.tinv-wishlist input.tinvwl-button',
				'element'  => 'border-radius',
				'text'     => __( 'Border Radius', 'ti-woocommerce-wishlist-premium' ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'add to cart button', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt,.woocommerce.tinv-wishlist a.tinvwl-button.alt,.woocommerce.tinv-wishlist button.tinvwl-button.alt,.woocommerce.tinv-wishlist input.tinvwl-button.alt',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt:hover,.woocommerce.tinv-wishlist a.tinvwl-button.alt:hover,.woocommerce.tinv-wishlist button.tinvwl-button.alt:hover,.woocommerce.tinv-wishlist input.tinvwl-button.alt:hover',
				'element'  => 'background-color',
				'text'     => __( 'Background Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt,.woocommerce.tinv-wishlist a.tinvwl-button.alt,.woocommerce.tinv-wishlist button.tinvwl-button.alt,.woocommerce.tinv-wishlist input.tinvwl-button.alt',
				'element'  => 'color',
				'text'     => __( 'Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt:hover,.woocommerce.tinv-wishlist a.tinvwl-button.alt:hover,.woocommerce.tinv-wishlist button.tinvwl-button.alt:hover,.woocommerce.tinv-wishlist input.tinvwl-button.alt:hover',
				'element'  => 'color',
				'text'     => __( 'Text Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt,.woocommerce.tinv-wishlist a.tinvwl-button.alt,.woocommerce.tinv-wishlist button.tinvwl-button.alt,.woocommerce.tinv-wishlist input.tinvwl-button.alt',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt,.woocommerce.tinv-wishlist a.tinvwl-button.alt,.woocommerce.tinv-wishlist button.tinvwl-button.alt,.woocommerce.tinv-wishlist input.tinvwl-button.alt',
				'element'  => 'font-size',
				'text'     => __( 'Font Size', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'text',
				'selector' => '.woocommerce.tinv-wishlist #respond input#submit.alt,.woocommerce.tinv-wishlist a.tinvwl-button.alt,.woocommerce.tinv-wishlist button.tinvwl-button.alt,.woocommerce.tinv-wishlist input.tinvwl-button.alt',
				'element'  => 'border-radius',
				'text'     => __( 'Border Radius', 'ti-woocommerce-wishlist-premium' ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'wrapped blocks', 'ti-woocommerce-wishlist-premium' ) . '<br/>' . __( '(create wishlist, search, login)', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-wrapped-block',
				'element'  => 'background-color',
				'text'     => __( 'Block Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-wrapped-block input[type="text"],.tinv-wishlist .tinv-wrapped-block input[type="password"],.tinv-wishlist .tinv-wrapped-block input[type="search"]',
				'element'  => 'background-color',
				'text'     => __( 'Fields Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-wrapped-block input[type="text"],.tinv-wishlist .tinv-wrapped-block input[type="password"],.tinv-wishlist .tinv-wrapped-block input[type="search"]',
				'element'  => 'border-color',
				'text'     => __( 'Fields Border Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist .tinv-wrapped-block input[type="text"],.tinv-wishlist .tinv-wrapped-block input[type="password"],.tinv-wishlist .tinv-wrapped-block input[type="search"]',
				'element'  => 'border-radius',
				'text'     => __( 'Fields Border Radius', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-wrapped-block input[type="text"],.tinv-wishlist .tinv-wrapped-block input[type="password"],.tinv-wishlist .tinv-wrapped-block input[type="search"]',
				'element'  => 'color',
				'text'     => __( 'Fields Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-wrapped-block input::-webkit-input-placeholder',
				'element'  => 'color',
				'text'     => __( 'Fields Placeholder Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist .tinv-wrapped-block input[type="text"],.tinv-wishlist .tinv-wrapped-block input[type="password"],.tinv-wishlist .tinv-wrapped-block input[type="search"]',
				'element'  => 'font-family',
				'text'     => __( 'Fields Text Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist .tinv-wrapped-block input[type="text"],.tinv-wishlist .tinv-wrapped-block input[type="password"],.tinv-wishlist .tinv-wrapped-block input[type="search"]',
				'element'  => 'font-size',
				'text'     => __( 'Fields Text Font Size', 'ti-woocommerce-wishlist-premium' ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'Cart page buttons', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce-cart .tinv-wishlist .tinvwl_cart_to_wishlist_button:before',
				'element'  => 'color',
				'text'     => __( '"Save for Later" Icon Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.woocommerce-cart .tinv-wishlist .tinvwl_cart_to_wishlist_button:hover:before,.woocommerce-cart .tinv-wishlist .tinvwl_cart_to_wishlist_button.tinvwl-product-in-list:before',
				'element'  => 'color',
				'text'     => __( '"Save for Later" Icon Hover/Active Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinvwl_all_cart_to_wishlist_button:before',
				'element'  => 'color',
				'text'     => __( '"Save Cart" Icon Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinvwl_all_cart_to_wishlist_button:hover:before,.tinv-wishlist .tinvwl_all_cart_to_wishlist_button.tinvwl-product-in-list:before',
				'element'  => 'color',
				'text'     => __( '"Save Cart" Icon Hover/Active Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'table', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist table,.tinv-wishlist table td',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist table,.tinv-wishlist table th,.tinv-wishlist table td',
				'element'  => 'border-color',
				'text'     => __( 'Border Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist table th',
				'element'  => 'background-color',
				'text'     => __( 'Table Head Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist table th',
				'element'  => 'color',
				'text'     => __( 'Table Head Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist table th',
				'element'  => 'font-family',
				'text'     => __( 'Table Head Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist table th',
				'element'  => 'font-size',
				'text'     => __( 'Table Head Font Size', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist table td',
				'element'  => 'color',
				'text'     => __( 'Content Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist table td',
				'element'  => 'font-family',
				'text'     => __( 'Content Text Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist table td',
				'element'  => 'font-size',
				'text'     => __( 'Content Text Font Size', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist td.product-price',
				'element'  => 'color',
				'text'     => __( 'Price Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist td.product-price',
				'element'  => 'font-family',
				'text'     => __( 'Price Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist td.product-price',
				'element'  => 'font-size',
				'text'     => __( 'Price Font Size', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist td.product-price ins span.amount',
				'element'  => 'color',
				'text'     => __( 'Special Price Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist td.product-price ins span.amount',
				'element'  => 'background-color',
				'text'     => __( 'Special Price Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .social-buttons li a',
				'element'  => 'background-color',
				'text'     => __( 'Social Icons Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .social-buttons li a:hover',
				'element'  => 'background-color',
				'text'     => __( 'Social Icons Background Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .social-buttons li a i:before',
				'element'  => 'color',
				'text'     => __( 'Social Icons Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .social-buttons li a:hover i:before',
				'element'  => 'color',
				'text'     => __( 'Social Icons Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'popups', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-modal-inner',
				'element'  => 'background-color',
				'text'     => __( 'Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal h2',
				'element'  => 'color',
				'text'     => __( 'Title Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist .tinv-modal h2',
				'element'  => 'font-family',
				'text'     => __( 'Title Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist .tinv-modal h2',
				'element'  => 'font-size',
				'text'     => __( 'Title Font Size', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-modal-inner',
				'element'  => 'color',
				'text'     => __( 'Content Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-modal-inner,.tinv-wishlist .tinv-modal .tinv-modal-inner select',
				'element'  => 'font-family',
				'text'     => __( 'Content Text Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-modal-inner,.tinv-wishlist .tinv-modal .tinv-modal-inner select',
				'element'  => 'font-size',
				'text'     => __( 'Content Text Font Size', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-modal-inner input,.tinv-wishlist .tinv-modal .tinv-modal-inner select,.tinv-wishlist .tinv-modal .tinv-modal-inner textarea',
				'element'  => 'background-color',
				'text'     => __( 'Fields Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-modal-inner input,.tinv-wishlist .tinv-modal .tinv-modal-inner select,.tinv-wishlist .tinv-modal .tinv-modal-inner textarea',
				'element'  => 'border-color',
				'text'     => __( 'Fields Border Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'text',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-modal-inner input,.tinv-wishlist .tinv-modal .tinv-modal-inner select',
				'element'  => 'border-radius',
				'text'     => __( 'Fields Border Radius', 'ti-woocommerce-wishlist-premium' ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-modal-inner input,.tinv-wishlist .tinv-modal .tinv-modal-inner select,.tinv-wishlist .tinv-modal .tinv-modal-inner textarea',
				'element'  => 'color',
				'text'     => __( 'Fields Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal .tinv-modal-inner input::-webkit-input-placeholder',
				'element'  => 'color',
				'text'     => __( 'Fields Placeholder Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal button.tinvwl-button,.tinv-wishlist .tinv-modal .tinv-close-modal',
				'element'  => 'background-color',
				'text'     => __( 'Normal Buttons Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal button.tinvwl-button:hover,.tinv-wishlist .tinv-modal .tinv-close-modal:hover',
				'element'  => 'background-color',
				'text'     => __( 'Normal Buttons Background Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal button.tinvwl-button,.tinv-wishlist .tinv-modal .tinv-close-modal',
				'element'  => 'color',
				'text'     => __( 'Normal Buttons Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal button.tinvwl-button:hover,.tinv-wishlist .tinv-modal .tinv-close-modal:hover',
				'element'  => 'color',
				'text'     => __( 'Normal Buttons Text Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal button:not(.tinvwl-button)',
				'element'  => 'background-color',
				'text'     => __( 'Accent Buttons Background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal button:not(.tinvwl-button):hover,.tinv-wishlist .tinv-modal button:not(.tinvwl-button):active,.tinv-wishlist .tinv-modal button:not(.tinvwl-button):focus',
				'element'  => 'background-color',
				'text'     => __( 'Accent Buttons Background Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal button:not(.tinvwl-button)',
				'element'  => 'color',
				'text'     => __( 'Accent Buttons Text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.tinv-wishlist .tinv-modal button:not(.tinvwl-button):hover,.tinv-wishlist .tinv-modal button:not(.tinvwl-button):active,.tinv-wishlist .tinv-modal button:not(.tinvwl-button):focus',
				'element'  => 'color',
				'text'     => __( 'Accent Buttons Text Hover Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),

			array(
				'type'       => 'group',
				'title'      => __( 'Counter and Mini Wishlist', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
			),
			array(
				'type'     => 'color_important',
				'selector' => '.wishlist_products_counter .widget_wishlist',
				'element'  => 'background-color',
				'text'     => __( 'Drop down background Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'       => 'box_shadow',
				'selector'   => '.wishlist_products_counter .widget_wishlist',
				'element'    => 'box-shadow',
				'subelement' => array( '-webkit-box-shadow', '-moz-box-shadow' ),
				'text'       => __( 'Drop down shadow', 'ti-woocommerce-wishlist-premium' ),
				'extra'      => array(
					'color'         => array(
						'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ),
					),
					'format'        => __( 'Inset: %1$s<br/>H-Shadow: %2$s<br/>V-Shadow: %3$s<br/>Blur: %4$s<br/>Spread: %5$s<br/>Color: %6$s', 'ti-woocommerce-wishlist-premium' ),
					'options_inset' => array(
						'inset' => __( 'Inset', 'ti-woocommerce-wishlist-premium' ),
						''      => __( 'Outset', 'ti-woocommerce-wishlist-premium' ),
					),
				),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.widget-area .widget .wishlist_products_counter,.wishlist_products_counter .widget_wishlist,.wishlist_products_counter',
				'element'  => 'color',
				'text'     => __( 'Mini wishlist text Color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.widget-area .widget .wishlist_products_counter .wishlist_products_counter_number,.wishlist_products_counter .wishlist_products_counter_number,.widget-area .widget .wishlist_products_counter .wishlist_products_counter_text,.wishlist_products_counter .wishlist_products_counter_text',
				'element'  => 'color',
				'text'     => __( 'Counter text color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'select',
				'selector' => '.widget-area .widget .wishlist_products_counter,.wishlist_products_counter .widget_wishlist,.wishlist_products_counter',
				'element'  => 'font-family',
				'text'     => __( 'Font', 'ti-woocommerce-wishlist-premium' ),
				'options'  => $font_family,
				'validate' => array( 'filter' => FILTER_DEFAULT ),
			),
			array(
				'type'     => 'color_important',
				'selector' => 'div.wishlist_products_counter .wishlist_products_counter_wishlist.widget_wishlist ul.wishlist_list_titles li > a:hover,div.wishlist_products_counter .wishlist_products_counter_wishlist.widget_wishlist ul.wishlist_list_titles li > a',
				'element'  => 'color',
				'text'     => __( 'Wishlist tags color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.widget-area .widget .wishlist_products_counter .widget_wishlist a,.wishlist_products_counter .widget_wishlist a',
				'element'  => 'color',
				'text'     => __( 'Mini wishlist links color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.widget-area .widget .wishlist_products_counter .widget_wishlist a:hover,.wishlist_products_counter .widget_wishlist a:hover',
				'element'  => 'color',
				'text'     => __( 'Mini wishlist links hover color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.wishlist_products_counter:before',
				'element'  => 'color',
				'text'     => __( 'Wishlist icon color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
			array(
				'type'     => 'color_important',
				'selector' => '.wishlist_products_counter.wishlist-counter-with-products:before',
				'element'  => 'color',
				'text'     => __( 'Wishlist active icon color', 'ti-woocommerce-wishlist-premium' ),
				'extra'    => array( 'important_label' => __( 'Important', 'ti-woocommerce-wishlist-premium' ) ),
			),
		);
	}

}
