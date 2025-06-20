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
class TInvWL_Admin_Settings_General extends TInvWL_Admin_BaseSection {

	/**
	 * Priority for admin menu
	 *
	 * @var integer
	 */
	public $priority = 20;

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
			'title'      => __( 'General Settings', 'ti-woocommerce-wishlist-premium' ),
			'method'     => array( $this, '_print_' ),
			'slug'       => 'general-settings',
			'capability' => 'tinvwl_general_settings',
		);
	}

	/**
	 * Get WP menus
	 *
	 * @return array
	 */
	public function get_wp_menus() {
		$menus     = array( '' => __( 'None', 'ti-woocommerce-wishlist-premium' ) );
		$get_menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
		foreach ( $get_menus as $menu ) {
			$menus[ $menu->term_id ] = $menu->name;
		}

		return $menus;
	}

	/**
	 * Create sections for this settings
	 *
	 * @return array
	 */
	function constructor_data() {
		$processing_statuses = array(
			'tinvwl-addcart' => __( 'Add to Cart', 'ti-woocommerce-wishlist-premium' ),
		);
		$order_statuses      = get_terms( 'shop_order_status', array( 'hide_empty' => false ) );
		$status_message      = __( 'Order status "%s"', 'ti-woocommerce-wishlist-premium' );
		if ( is_wp_error( $order_statuses ) ) {
			$order_statuses = array();
			if ( function_exists( 'wc_get_order_statuses' ) ) {
				$order_statuses = wc_get_order_statuses();
			}
			foreach ( $order_statuses as $key => $value ) {
				$key = str_replace( 'wc-', '', $key );

				$processing_statuses[ $key ] = sprintf( $status_message, $value );
			}
		} else {
			foreach ( $order_statuses as $s ) {
				$key = str_replace( 'wc-', '', $s->slug );

				$processing_statuses[ $key ] = sprintf( $status_message, $s->slug );
			}
		}
		$lists     = get_pages( array( 'number' => 9999999 ) ); // @codingStandardsIgnoreLine WordPress.VIP.RestrictedFunctions.get_pages
		$page_list = array( '' => '' );
		foreach ( $lists as $list ) {
			$page_list[ $list->ID ] = $list->post_title;
		}
		$subscibe_fields = array(
			array(
				'type'  => 'checkboxonoff',
				'name'  => 'allow',
				'text'  => __( 'Allow to Follow Wishlist', 'ti-woocommerce-wishlist-premium' ),
				'desc'  => __( 'This option adds Follow button to Wishlist page and allows users to receive email notifications each time Wishlist owner make changes in his Wishlist.', 'ti-woocommerce-wishlist-premium' ),
				'std'   => true,
				'extra' => array( 'tiwl-show' => '.tiwl-follow-sett-allow' ),
			),
			array(
				'type'  => 'group',
				'id'    => 'follow_period',
				'class' => 'tiwl-follow-sett-allow',
			),
			array(
				'type'    => 'select',
				'name'    => 'period_send',
				'text'    => __( 'Period for sending notifications to followers', 'ti-woocommerce-wishlist-premium' ),
				'desc'    => __( 'You can control how often followers will be receiving email notifications about changes in Wishlists that they follow. However, they will not receive any notifications if no changes were made in wishlist, within selected period of time.', 'ti-woocommerce-wishlist-premium' ),
				'std'     => 'daily',
				'options' => array(
					'hourly' => __( 'Hourly', 'ti-woocommerce-wishlist-premium' ),
					'daily'  => __( 'Daily', 'ti-woocommerce-wishlist-premium' ),
				),
			),
			array(
				'type'  => 'group',
				'id'    => 'follow_actions',
				'class' => 'tiwl-follow-sett-allow',
				'title' => __( 'Event list status', 'ti-woocommerce-wishlist-premium' ),
				'desc'  => __( 'Choose what kind of wishlist events users can follow. Users will have a choice from enabled options.', 'ti-woocommerce-wishlist-premium' ),
			),
		);
		$events          = TInvWL_Subscribers::event_lists();
		foreach ( $events as $key => $name ) {
			$subscibe_fields[] = array(
				'type' => 'checkboxonoff',
				'name' => 'event_' . $key,
				'text' => $name,
				'std'  => true,
			);
		}
		$menus = $this->get_wp_menus();

		$settings = array(
			//General
			array(
				'id'         => 'general',
				'title'      => __( 'General Settings', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
				'fields'     => array(
					array(
						'type' => 'text',
						'name' => 'default_title',
						'text' => __( 'Default Wishlist Name', 'ti-woocommerce-wishlist-premium' ),
						'std'  => 'Default wishlist',
					),
					array(
						'type'    => 'select',
						'name'    => 'default_privacy',
						'text'    => __( 'Default Wishlist Privacy', 'ti-woocommerce-wishlist-premium' ),
						'std'     => 'share',
						'options' => array(
							'public'  => __( 'Public', 'ti-woocommerce-wishlist-premium' ),
							'share'   => __( 'Share', 'ti-woocommerce-wishlist-premium' ),
							'private' => __( 'Private', 'ti-woocommerce-wishlist-premium' ),
						),
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'my_account_endpoint',
						'text'  => __( 'Setup wishlist page under WooCommerce My Account section', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => '<span class="tiwl-button-show-notice">' . __( 'This option will set up the wishlist page under WooCommerce My Account section and prevents all sharing features.', 'ti-woocommerce-wishlist-premium' ) . '</span>',
						'std'   => false,
						'extra' => array(
							'tiwl-show' => '.tinwl-general-my-account-endpoint',
							'tiwl-hide' => '.tinwl-general-my-account-link',
						),
					),
					array(
						'type'  => 'text',
						'name'  => 'my_account_endpoint_slug',
						'text'  => __( 'WooCommerce My Account Wishlist page slug', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 'wishlist',
						'class' => 'tinwl-general-my-account-endpoint',
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'link_in_myaccount',
						'text'  => __( 'Show Link to Wishlist in my account', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => '',
						'std'   => true,
						'class' => 'tinwl-general-my-account-link',
					),
					array(
						'type' => 'group',
						'id'   => 'guests',
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'guests',
						'text'  => __( 'Enable wishlist functionality for unauthenticated users', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'You can allow guests to add products to a temporary Wishlist to keep them on your website and force to create an account. Or you can make Wishlist as privileges for registered users only. Guests will not be able to create and manage wishlists. After login, products from guest wishlist will be added to default wishlist.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => array( 'tiwl-show' => '.tiwl-general-login' ),
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'require_login',
						'text'  => __( 'Require Login', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'Disallows guests to use Wishlist functionality until they sign-in.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => false,
						'class' => 'tiwl-general-login',
					),
					array(
						'type'          => 'checkboxonoff',
						'name'          => 'redirect_require_login',
						'text'          => __( 'Redirect to Login Page', 'ti-woocommerce-wishlist-premium' ),
						'desc'          => '<span class="tiwl-general-show-notice-warning-popup">' . __( '<strong>Currently this option could not be changed because "Show successful notice in popup" is disabled. Guests will be redirected automatically to a login page.</strong>', 'ti-woocommerce-wishlist-premium' ) . '</span><span class="tiwl-general-show-notice">' . __( 'If enabled, guests will be redirected to a login page once clicking the "Add to Wishlist" button or "Wishlist Products Counter" link. Otherwise a popup with login required notice will appear.', 'ti-woocommerce-wishlist-premium' ) . '</span>',
						'std'           => false,
						'tiwl-required' => array(
							'general-guests'        => 'on',
							'general-require_login' => 'on',
						),
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'login_notice',
						'text'  => __( 'Show Login Notice for Guests', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'This option enables login notice on wishlist page, so customers can login directly from wishlist page.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'class' => 'tiwl-general-login',
					),
					array(
						'type'  => 'textarea',
						'name'  => 'text_login_anchor',
						'text'  => __( 'Login Anchor Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 'Please login or register to use all wishlist features',
						'class' => 'tiwl-general-login',
					),
					array(
						'type'  => 'text',
						'name'  => 'text_login_link',
						'text'  => __( 'Login Link Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 'Click here to login',
						'class' => 'tiwl-general-login',
					),
					array(
						'type'    => 'select',
						'name'    => 'login_link_behavior',
						'text'    => __( 'Login Link Behavior', 'ti-woocommerce-wishlist-premium' ),
						'desc'    => __( 'Allow customers to login directly on wishlist page or redirect them to default login page.', 'ti-woocommerce-wishlist-premium' ),
						'std'     => 'link',
						'options' => array(
							'link' => __( 'Redirect to login page', 'ti-woocommerce-wishlist-premium' ),
							'form' => __( 'Show inline form', 'ti-woocommerce-wishlist-premium' ),
						),
						'class'   => 'tiwl-general-login',
					),
					array(
						'type' => 'group',
						'id'   => 'multi',
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'quantity_func',
						'text'  => __( 'Enable products quantity functionality', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'desc'  => __( 'You can allow customers to add as many products to wishlist as they need. If it’s disabled, the only one product of the same configuration can be added.', 'ti-woocommerce-wishlist-premium' ),
						'extra' => array(
							'tiwl-hide' => '.tiwl-general-quantity-func-hide>td',
						),
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'multi',
						'text'  => __( 'Enable multi-wishlist support', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'You can allow customers to create and manage Wishlists, so they can create an event specific wishlists i.e. “Christmas Wishlist” or “Birthday gifts Wishlist” and so on. Or you can limit customers to use only one (Default) Wishlist.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => array(
							'tiwl-hide' => '.tiwl-general-multi,.tiwl-general-simple-flow>td>*',
							'tiwl-show' => '.tiwl-general-multi-warning-popup',
						),
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'simple_flow',
						'text'  => __( 'Remove product from Wishlist on second click', 'ti-woocommerce-wishlist-premium' ),
						'std'   => false,
						'desc'  => __( 'If enabled, the product will be removed from a Wishlist on second click the "Add to Wishlist" button.', 'ti-woocommerce-wishlist-premium' ),
						'class' => 'tiwl-general-multi',
						'extra' => array(
							'tiwl-show' => '.tiwl-general-simple-flow>td',
							'tiwl-hide' => '.tiwl-general-quantity-func-hide>td>*',
						),
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'download_pdf',
						'text' => __( 'Show a button to download wishlist as PDF', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'product_stats',
						'text'  => __( 'Show stats for each product', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'Show number of times each product added to wishlists across the site.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => false,
						'class' => '',
					),
					array(
						'type'  => 'number',
						'name'  => 'guests_timeout',
						'text'  => __( 'Days after which the guest wishlist will be deleted', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 30,
						'extra' => array(
							'step' => '1',
							'min'  => '1',
						),
						'desc'  => __( "Guest's wishlists are automatically deleted after a defined period of days from the latest product addition.", 'ti-woocommerce-wishlist-premium' ),
					),
					array(
						'type' => 'group',
						'id'   => 'show_notice',
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'show_notice',
						'text'  => __( 'Show successful notice in popup', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => tinv_get_option_admin( 'general', 'multi' ) ? array(
							'tiwl-show' => '.tiwl-general-show-notice',
							'tiwl-hide' => '.tiwl-general-show-notice-warning-popup',
							'disabled'  => 'disabled',
						) : array(
							'tiwl-show' => '.tiwl-general-show-notice',
							'tiwl-hide' => '.tiwl-general-show-notice-warning-popup',
						),
						'desc'  => '<span class="tiwl-general-multi-warning-popup">' . __( '<strong>Currently this option could not be changed because "Multi-Wishlist support" is enabled. It is needed to choose in which wishlist to add the product.</strong>', 'ti-woocommerce-wishlist-premium' ) . '</span><span class="tiwl-general-multi">' . __( 'This option allows to show/hide a popup with successful or error notices after addition or removing products from a Wishlist.', 'ti-woocommerce-wishlist-premium' ) . '</span>',
					),
					array(
						'type'  => 'text',
						'name'  => 'text_browse',
						'text'  => __( '"View Wishlist" button Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 'View Wishlist',
						'class' => 'tiwl-general-show-notice',
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'redirect',
						'text'  => __( 'Redirect to Wishlist', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'If enabled, user will be redirected to wishlist page after 5 sec from adding product to wishlist.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'class' => 'tiwl-general-show-notice',
					),
					array(
						'type'  => 'text',
						'name'  => 'text_added_to',
						'text'  => __( '"Product added to Wishlist" Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => '{product_name} added to {wishlist_title}',
						'desc'  => __( 'You can use next placeholder in this field to get current product name: <code>{product_name}</code>, <code>{product_sku}</code>, <code>{wishlist_title}</code>', 'ti-woocommerce-wishlist-premium' ),
						'class' => 'tiwl-general-show-notice',
					),
					array(
						'type'  => 'text',
						'name'  => 'text_already_in',
						'text'  => __( '"Product already in Wishlist" Text', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'This notification will be shown if user will try to add product that already in wishlist when quantity on wishlist page is disabled. ', 'ti-woocommerce-wishlist-premium' ) . __( 'You can use next placeholder in this field to get current product name: <code>{product_name}</code>', 'ti-woocommerce-wishlist-premium' ),
						'std'   => '{product_name} already in Wishlist',
						'class' => 'tiwl-general-show-notice tiwl-general-quantity-func-hide',
					),
					array(
						'type'  => 'text',
						'name'  => 'text_removed_from',
						'text'  => __( '"Product removed from Wishlist" Text', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'This notification will be shown if user will try to add product that already in wishlist when multi-wishlist support & quantity on wishlist page is disabled.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 'Product removed from Wishlist',
						'class' => 'tiwl-general-show-notice tiwl-general-simple-flow',
					),
				),
			),
			//Rewrites
			array(
				'id'         => 'permalinks',
				'title'      => __( 'Permalinks Settings', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => false,
				'fields'     => array(
					array(
						'type' => 'checkboxonoff',
						'name' => 'force',
						'text' => __( 'Force permalinks rewrite', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'This option should be enabled to avoid any issues with URL rewrites between other plugins and Wishlist', 'ti-woocommerce-wishlist-premium' ),
						'std'  => false,
					),
				),
			),
			//Rename
			array(
				'id'         => 'rename',
				'title'      => __( 'Rename wishlist Settings', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => false,
				'fields'     => array(
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'rename',
						'text'  => __( 'Rename wishlist word across the plugin', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'These options allow changing word <code>wishlist</code> across all plugin instance', 'ti-woocommerce-wishlist-premium' ),
						'std'   => false,
						'extra' => array( 'tiwl-show' => '.tiwl-rename-single, .tiwl-rename-plural' ),
					),
					array(
						'type'  => 'text',
						'name'  => 'rename_single',
						'text'  => __( 'Single form', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'This option allowing you to change a single form of the word. You need to write a new word in lowercase and the proper case will be applied automatically for all instances.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => '',
						'class' => 'tiwl-rename-single',
					),
					array(
						'type'  => 'text',
						'name'  => 'rename_plural',
						'text'  => __( 'Plural form', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'This option allowing you to change a plural form of the word. Left it empty if you need to add just "s" suffix to the single form word that you set above.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => '',
						'class' => 'tiwl-rename-plural',
					),
				),
			),
			//Processing
			array(
				'id'         => 'processing',
				'title'      => __( 'Wishlist Processing Options', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => __( 'Following options allow you to set products to be automatically removed from a wishlist and under what conditions.', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
				'fields'     => array(
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'autoremove',
						'text'  => __( 'Automatic removal', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => array( 'tiwl-show' => '.tiwl-processing-autoremove' ),
					),
					array(
						'type'    => 'select',
						'name'    => 'autoremove_status',
						'text'    => __( 'Removal condition', 'ti-woocommerce-wishlist-premium' ),
						'std'     => 'completed',
						'options' => $processing_statuses,
						'class'   => 'tiwl-processing-autoremove',
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'redirect_checkout',
						'text' => __( 'Redirect to the checkout page from Wishlist if added to cart', 'ti-woocommerce-wishlist-premium' ),
						'std'  => false,
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'autoremove_anyone',
						'text'  => __( 'Remove by anyone', 'ti-woocommerce-wishlist-premium' ),
						'std'   => false,
						'class' => 'tiwl-processing-autoremove',
						'extra' => array( 'tiwl-show' => '.tiwl-processing-autoremove-anyone>td' ),
					),
					array(
						'type'    => 'select',
						'name'    => 'autoremove_anyone_type',
						'text'    => __( 'Apply for Wishlist privacy type(s)', 'ti-woocommerce-wishlist-premium' ),
						'std'     => '',
						'options' => array(
							''       => __( 'All', 'ti-woocommerce-wishlist-premium' ),
							'share'  => __( 'Share', 'ti-woocommerce-wishlist-premium' ),
							'public' => __( 'Public', 'ti-woocommerce-wishlist-premium' ),
						),
						'class'   => 'tiwl-processing-autoremove tiwl-processing-autoremove-anyone',
					),
				),
			),
			//References
			array(
				'id'         => 'references',
				'title'      => __( 'Wishlist References on Cart, Checkout, Order', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => __( 'Following options allow you to show wishlist references for each product on cart, checkout, order if it added from wishlist.', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
				'fields'     => array(
					array(
						'type' => 'text',
						'name' => 'label',
						'text' => __( 'Reference label', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'Add this text before the wishlist reference link for cart and order items.', 'ti-woocommerce-wishlist-premium', 'ti-woocommerce-wishlist-premium' ),
						'std'  => __( 'From wishlist:', 'ti-woocommerce-wishlist-premium' ),
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'cart',
						'text' => __( 'Show on cart', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'checkout',
						'text' => __( 'Show on checkout', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'order',
						'text' => __( 'Show on order', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
				),
			),
			//Pages
			array(
				'id'         => 'page',
				'title'      => __( 'Wishlist Page Options', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => __( 'Following pages needs to be selected so the wishlist knows where they are. These pages should be created upon installation of the plugin, if not you will need to create them manually.', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => true,
				'fields'     => array(
					array(
						'type'     => 'select',
						'name'     => 'wishlist',
						'text'     => __( 'My Wishlist', 'ti-woocommerce-wishlist-premium' ),
						'std'      => '',
						'options'  => $page_list,
						'validate' => FILTER_VALIDATE_INT,
					),
					array(
						'type'     => 'select',
						'name'     => 'manage',
						'text'     => __( 'Manage Wishlists', 'ti-woocommerce-wishlist-premium' ),
						'std'      => '',
						'options'  => $page_list,
						'validate' => FILTER_VALIDATE_INT,
					),
					array(
						'type'     => 'select',
						'name'     => 'search',
						'text'     => __( 'Search for Wishlist Results', 'ti-woocommerce-wishlist-premium' ),
						'std'      => '',
						'options'  => $page_list,
						'validate' => FILTER_VALIDATE_INT,
					),
					array(
						'type' => 'group',
						'id'   => 'secondpage',
					),
					array(
						'type'     => 'select',
						'name'     => 'public',
						'text'     => __( 'Public Wishlists', 'ti-woocommerce-wishlist-premium' ),
						'desc'     => __( 'You can also set additional (not necessarily) pages to show in quick menu on wishlist page. These are simple pages with search and recent wishlist shortcodes, so you can add any additional content by simply editing a page.', 'ti-woocommerce-wishlist-premium' ),
						'std'      => '',
						'options'  => $page_list,
						'validate' => FILTER_VALIDATE_INT,
					),
					array(
						'type'     => 'select',
						'name'     => 'searchp',
						'text'     => __( 'Search for Wishlist', 'ti-woocommerce-wishlist-premium' ),
						'std'      => '',
						'options'  => $page_list,
						'validate' => FILTER_VALIDATE_INT,
					),
					array(
						'type'     => 'select',
						'name'     => 'create',
						'text'     => __( 'Create Wishlist', 'ti-woocommerce-wishlist-premium' ),
						'std'      => '',
						'options'  => $page_list,
						'validate' => FILTER_VALIDATE_INT,
					),
				),
			),
			//Follow
			array(
				'id'         => 'subscribe',
				'title'      => __( 'Follow Wishlist', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => false,
				'fields'     => $subscibe_fields,
			),
			//Wishlist products counter
			array(
				'id'         => 'topline',
				'title'      => __( 'Wishlist Products Counter', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => sprintf( __( 'Add this shortcode <code>[ti_wishlist_products_counter]</code> anywhere into a page content to show Wishlist Counter.<br/><br/>It can be also added as a widget <code>TI Wishlist Products Counter</code> under the <a href="%s">Appearance -> Widgets</a> section.', 'ti-woocommerce-wishlist-premium' ), esc_url( admin_url( 'widgets.php' ) ) ),
				'show_names' => true,
				'fields'     => array(
					array(
						'type'    => 'select',
						'name'    => 'icon',
						'text'    => __( '"Wishlist" Counter Icon', 'ti-woocommerce-wishlist-premium' ),
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
							'topline-icon' => 'custom',
						),
					),
					array(
						'type'          => 'select',
						'name'          => 'icon_style',
						'std'           => '',
						'text'          => __( '"Wishlist" Counter Icon Color', 'ti-woocommerce-wishlist-premium' ),
						'options'       => array(
							''      => __( 'Use font color', 'ti-woocommerce-wishlist-premium' ),
							'black' => __( 'Black', 'ti-woocommerce-wishlist-premium' ),
							'white' => __( 'White', 'ti-woocommerce-wishlist-premium' ),
						),
						'tiwl-required' => array(
							'topline-icon' => array( 'heart', 'heart-plus' ),
						),
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'link',
						'text' => __( 'Add Link For "Wishlist" Counter Title', 'ti-woocommerce-wishlist-premium' ),
						'desc' => __( 'Add or remove link to wishlist page for wishlist counter title and icon. The link leads to the Default Wishlist page or to Manage Wishlists page (if multi-wishlist support is enabled).', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'show_text',
						'text'  => __( 'Show  "Wishlist" Counter Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => array(
							'tiwl-show' => '.tiwl-dropdown-text',
						),
					),
					array(
						'type'  => 'text',
						'name'  => 'text',
						'text'  => __( '"Wishlist" Counter Text', 'ti-woocommerce-wishlist-premium' ),
						'std'   => __( 'Wishlist - ', 'ti-woocommerce-wishlist-premium' ),
						'class' => 'tiwl-dropdown-text',
					),
					array(
						'type' => 'checkboxonoff',
						'name' => 'counter',
						'text' => __( 'Show "Wishlist" Counter', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
					array(
						'type'    => 'multiselect',
						'name'    => 'menu',
						'text'    => __( 'Add counter to menu', 'ti-woocommerce-wishlist-premium' ),
						'options' => $menus,
						'desc'    => __( 'You can add a wishlist products counter as item to the selected menu.', 'ti-woocommerce-wishlist-premium' ),
						'extra'   => array(
							'tiwl-value' => '0',
							'tiwl-hide'  => '.tiwl-menu-position, .tiwl-menu-hide-counter',
						),
					),
					array(
						'type'  => 'number',
						'name'  => 'menu_order',
						'text'  => __( 'Counter position (Menu item order)', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'Allows you to add the wishlist counter as a menu item and apply its position.', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 100,
						'class' => 'tiwl-menu-position',
						'extra' => array(
							'step' => '1',
							'min'  => '1',
						),
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'show_counter',
						'text'  => __( 'Show number of products in counter', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'extra' => array(
							'tiwl-show' => '.tiwl-zero-counter',
						),
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'hide_zero_counter',
						'text'  => __( 'Hide zero value', 'ti-woocommerce-wishlist-premium' ),
						'desc'  => __( 'Do not show the "0" value in a counter if wishlist is empty.', 'ti-woocommerce-wishlist-premium' ),
						'class' => 'tiwl-zero-counter',
						'std'   => false,
					),
					array(
						'type' => 'group',
						'id'   => 'mini_wishlist',
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'drop_down',
						'text'  => __( 'Show dropdown with mini wishlist', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'desc'  => __( 'You can show a drop down with the products recently added to wishlist. Similar to the woocommerce mini cart.', 'ti-woocommerce-wishlist-premium' ),
						'extra' => array(
							'tiwl-show' => '.tiwl-dropdown-position-wishlist',
						),
					),
					array(
						'type'  => 'number',
						'name'  => 'drop_down_count_product',
						'text'  => __( 'Maximum products in mini wishlist', 'ti-woocommerce-wishlist-premium' ),
						'std'   => 5,
						'class' => 'tiwl-dropdown-position-wishlist',
						'desc'  => __( 'Limit amount of products displayed in mini wishlist. Default value is 5.', 'ti-woocommerce-wishlist-premium' ),
						'extra' => array(
							'min' => 1,
						),
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'show_wishlists',
						'text'  => __( 'Show wishlists', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'desc'  => '',
						'class' => 'tiwl-dropdown-position-wishlist',
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'remove',
						'text'  => __( 'Show remove button', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'desc'  => '',
						'class' => 'tiwl-dropdown-position-wishlist',
					),
					array(
						'type'  => 'checkboxonoff',
						'name'  => 'add_to_cart',
						'text'  => __( 'Show add to cart button', 'ti-woocommerce-wishlist-premium' ),
						'std'   => true,
						'desc'  => '',
						'class' => 'tiwl-dropdown-position-wishlist',
					),
				),
			),
		);
		//Support chat option
		if ( ! empty( $_GET['chat'] ) ) {
			$settings[] = array(
				'id'         => 'chat',
				'title'      => __( 'Support chat settings', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => '',
				'show_names' => true,
				'fields'     => array(
					array(
						'type' => 'checkboxonoff',
						'name' => 'enabled',
						'text' => __( 'Enable support chat', 'ti-woocommerce-wishlist-premium' ),
						'std'  => true,
					),
				),
			);
		}

		// Buttons.
		$settings[] = array(
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

		return $settings;
	}

	/**
	 * Save value to database and flush rewrite.
	 *
	 * @param array $data Post section data.
	 */
	function constructor_save( $data ) {
		if ( filter_input( INPUT_POST, 'save_buttons-setting_reset' ) ) {
			foreach ( array_keys( $data ) as $key ) {
				if ( ! in_array( $key, array( 'page' ) ) ) {
					$data[ $key ] = array();
				}
			}
		}
		parent::constructor_save( $data );

		if ( empty( $data ) || ! is_array( $data ) ) {
			return false;
		} else {
			if ( isset( $data['general']['multi'] ) && $data['general']['multi'] ) {
				tinv_update_option( 'general', 'show_notice', true );
				tinv_update_option( 'general', 'simple_flow', false );
			}
		}
		delete_option( 'rewrite_rules' );
		self::subscribe_run();
	}

	/**
	 * Run subscribe and schedule event check subscribers.
	 *
	 * @return boolean
	 */
	public static function subscribe_run() {
		$func      = 'tinvwl_subscribers_notification';
		$timestamp = wp_next_scheduled( $func );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, $func );
		}
		if ( ! tinv_get_option( 'subscribe', 'allow' ) ) {
			return null;
		}
		$_events = array();
		$events  = TInvWL_Subscribers::event_lists();
		foreach ( array_keys( $events ) as $key ) {
			if ( tinv_get_option( 'subscribe', 'event_' . $key ) ) {
				$_events[] = absint( $key );
			}
		}
		tinv_update_option( 'subscribe', 'event', $_events );
		if ( empty( $_events ) ) {
			tinv_update_option( 'subscribe', 'allow', false );

			return null;
		}
		$period = tinv_get_option( 'subscribe', 'period_send' );

		switch ( $period ) {
			case 'twicedaily':
			case 'daily':
				$ve   = absint( get_option( 'gmt_offset' ) ) . ' HOURS';
				$time = strtotime( '00:00 today +19 HOURS ' . $ve );
				if ( $time < time() ) {
					$time = strtotime( '00:00 tomorrow +19 HOURS ' . $ve );
				}
				break;
			case 'hourly':
			default:
				$time = strtotime( '00:00 today +' . date( 'H' ) . ' HOURS +1 HOURS' );
				break;
		}
		wp_schedule_event( $time, $period, $func );

		return (bool) wp_next_scheduled( $func );
	}
}
