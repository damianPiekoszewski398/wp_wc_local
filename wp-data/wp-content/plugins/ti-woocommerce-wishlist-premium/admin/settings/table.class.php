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
class TInvWL_Admin_Settings_Table extends TInvWL_Admin_BaseSection {

	/**
	 * Priority for admin menu
	 *
	 * @var integer
	 */
	public $priority = 40;

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
			'title'      => __( 'Table Options', 'ti-woocommerce-wishlist-premium' ),
			'page_title' => __( 'Wishlist Table Options', 'ti-woocommerce-wishlist-premium' ),
			'method'     => array( $this, '_print_' ),
			'slug'       => 'table-settings',
			'capability' => 'tinvwl_table_options',
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
				'id'         => 'product_table',
				'title'      => __( 'Product Settings', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => __( 'Following options allows you to choose what information/functionality to show/enable in wishlist table on wishlist page.', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
				'fields'     => array(
					array(
						'type' => 'checkboxonoff',
						'name' => 'colm_image',
						'text' => __( 'Show Product Image', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'add_to_cart',
						'text'  => __( 'Show "Add to Cart" button', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
					),
					array(
						'type'  => 'text',
						'name'  => 'text_add_to_cart',
						'text'  => __( '"Add to Cart" Button Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 'Add to Cart',
						'class' => 'tiwl-table-action-addcart',
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'colm_price',
						'text' => __( 'Show Unit price', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'colm_stock',
						'text' => __( 'Show Stock status', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'colm_date',
						'text' => __( 'Show Date of addition', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'colm_quantity',
						'text'  => __( 'Show Quantity', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'This option enables "Quantity" functionality on the Wishlist page. Generally if a customer adds the same product to the same Wishlist more than one time, he will receive a notification that this product is already in the Wishlist. But if "Quantity" functionality is enabled, notifications won\'t be shown and the product quantity will be increased instead.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => tinv_get_option_admin( 'general', 'quantity_func' ) ? array() : array( 'disabled' => 'disabled' ),
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'move',
						'text' => __( 'Show "Move" button', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'This option allows customers to move products between their wishlists', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'subtotal',
						'text' => __( 'Show subtotal price per wishlist product', 'ti-woocommerce-wishlist-premium' ),
						'std'  => false,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'total',
						'text' => __( 'Show the total price for all products in the current wishlist', 'ti-woocommerce-wishlist-premium' ),
						'std'  => false,
					),
				),
			),
			array(
				'id'         => 'table',
				'title'      => __( 'Table Settings', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => __( 'Following options will help user to manage and add products to cart from wishlist table in bulk.', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
				'fields'     => array(
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'sort',
						'text'  => __( 'Enable sorting products feature', 'ti-woocommerce-wishlist-premium' ),
						'std'   => false,
						'desc'  => __( 'The drag-and-drop product sorting feature will force to display of all products on one wishlist page.', 'ti-woocommerce-wishlist-premium' ),
						'extra' => array( 'tiwl-hide' => '.tiwl-table-per_page' ),
					),
					array(
						'type'  => 'number',
						'name'  => 'per_page',
						'text'  => __( 'Products per page', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 10,
						'extra' => array(
							'step' => '1',
							'min'  => '1',
						),
						'class' => 'tiwl-table-per_page',
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'colm_checkbox',
						'text'  => __( 'Show Checkboxes', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => array( 'tiwl-show' => '.tiwl-table-cb-button' ),
					),
					array(
						'type'  => 'group',
						'id'    => 'cb_button',
						'class' => 'tiwl-table-cb-button',
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'colm_actions',
						'text' => __( 'Show Actions button', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'Bulk actions drop down at the bottom of wishlist table', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'add_select_to_cart',
						'text'  => __( 'Show "Add Selected to Cart" button', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => array( 'tiwl-show' => '.tiwl-table-addcart-sel', 'ti-woocommerce-wishlist-premium' ),
					),
					array(
						'type'  => 'text',
						'name'  => 'text_add_select_to_cart',
						'text'  => __( '"Add Selected to Cart" Button Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 'Add Selected to Cart',
						'class' => 'tiwl-table-addcart-sel',
					),
					array(
						'type' => 'group',
						'id'   => '_button',
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'add_all_to_cart',
						'text'  => __( 'Show "Add All to Cart" button', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => array( 'tiwl-show' => '.tiwl-table-addcart-all' ),
					),
					array(
						'type'  => 'text',
						'name'  => 'text_add_all_to_cart',
						'text'  => __( '"Add All to Cart" Button Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 'Add All to Cart',
						'class' => 'tiwl-table-addcart-all',
					),
					array(
						'type' => 'group',
						'id'   => '_popup',
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'hide_popup',
						'text' => __( 'Hide popup for wishlist products management actions', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'This option allows hide/show a popup after any action is processed with wishlist products on the wishlist page.', 'ti-woocommerce-wishlist-premium' ),
						'std'  => false,
					),
				),
			),
			array(
				'id'         => 'navigation',
				'title'      => __( 'Wishlist Table Navigation Buttons', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => __( 'These are the options to enable/disable/manage the quick navigational menu that can be placed above or/and below the Wishlist table.', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
				'fields'     => array(
					array(
						'type' => 'checkboxonoff',
						'name' => 'in_title',
						'text' => __( 'Show in title area', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'after_table',
						'text' => __( 'Show after table', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type'    => 'select',
						'name'    => 'type',
						'text'    => __( 'Button type', 'ti-woocommerce-wishlist-premium' ),
						'std'     => 'link',
						'options' => array(
							'link'   => __( 'Link', 'ti-woocommerce-wishlist-premium' ),
							'button' => __( 'Button', 'ti-woocommerce-wishlist-premium' ),
						),
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'icon',
						'text' => __( 'Show icons', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'public',
						'text' => __( 'Show "All Wishlists" Button', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'This button opens a page with Recent Wishlist shortcode. You can assign page to this button in <code>TI Wishlist > General Settings > Wishlist Page Options: Public Wishlists</code>', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'searchp',
						'text' => __( 'Show "Search" Button', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'This button opens a page with Search shortcode. You can assign page to this button in <code>TI Wishlist > General Settings > Wishlist Page Options: Search for Wishlist</code>', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'continue',
						'text' => __( 'Show "Continue Shopping" Button', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
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
