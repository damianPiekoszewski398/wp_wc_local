<?php
/**
 * File that define P24_Config class.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;

/**
 * Methods for Przelewy 24 plugin to display on admin order.
 */
class P24_MC_Admin_Order {

	const META_ACTIVE_MULTIPLIER = '_p24_mc_active_multiplier';

	/**
	 * Instance of core of plugin.
	 *
	 * @var P24_Multi_Currency
	 */
	private $mc;

	/**
	 * Instance of core of plugin.
	 *
	 * @var P24_Core
	 */
	private $plugin_core;

	/**
	 * Construct class instance.
	 *
	 * @param P24_Multi_Currency $mc The multi currency part of plugin.
	 * @param P24_Core           $plugin_core The core class of plugin.
	 */
	public function __construct( P24_Multi_Currency $mc, P24_Core $plugin_core ) {
		$this->mc          = $mc;
		$this->plugin_core = $plugin_core;
	}

	/**
	 * Add box to set currency for order.
	 *
	 * This box is used on admin panel.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function add_admin_order_change_currency( WP_Post $post ) {
		$currency_options = $this->plugin_core->get_multi_currency_instance()->get_available_currencies();
		$params           = compact( 'post', 'currency_options' );
		$this->plugin_core->render_template( 'multi-currency-order-edit', $params );
	}

	/**
	 * Add all meta boxes for different pages.
	 */
	public function add_meta_boxes() {
		foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
			add_meta_box( 'p24_admin_order_multi_currency', __( 'Aktywna waluta', 'przelewy24' ), array( $this, 'add_admin_order_change_currency' ), $type, 'side', 'high' );
		}
	}

	/**
	 * Set currency of order created by admin.
	 */
	public function admin_order_edit() {
		if ( is_admin() ) {
			wp_verify_nonce( null );
			if ( isset( $_POST['order_id'] ) && isset( $_POST['currency_code'] ) ) {
				$order_id = (int) $_POST['order_id'];
				$currency = sanitize_text_field( wp_unslash( $_POST['currency_code'] ) );
				update_post_meta( $order_id, '_order_currency', $currency );
				wp_die( 'ok' );
			} else {
				wp_die( 'fail' );
			}
		}
		wp_die( 'fail' );
	}

	/**
	 * Fix currencies if mismatched.
	 *
	 * @param WP_Error   $error Error, may be empty.
	 * @param WC_Product $product A product.
	 * @param WC_Order   $order An order.
	 * @param int        $qty Quantity.
	 * @return WP_Error
	 */
	public function fix_currencies( WP_Error $error, WC_Product $product, WC_Order $order, $qty ) {
		if ( $product instanceof P24_MC_Product_Common_Interface ) {
			$order_currency   = $order->get_currency();
			$product_currency = $product->get_currency();
			if ( $product_currency !== $order_currency ) {
				$product->overwrite_currency( $order_currency );
			}
		}
		/* Assume other products have correct price already. */

		return $error;
	}

	/**
	 * Fix currencies if mismatched in additional place.
	 *
	 * @param array             $items Items to analyse.
	 * @param WC_Abstract_Order $order An order.
	 * @param array             $type Array with types.
	 * @return array
	 */
	public function fix_currencies_again( $items, WC_Abstract_Order $order, $type ) {
		if ( ! in_array( 'line_item', $type, true ) ) {
			return $items;
		}

		$currency    = $order->get_currency();
		$multipliers = $this->mc->get_multipliers();
		$multiplier  = (float) $multipliers[ $currency ];

		foreach ( $items as $product ) {
			if ( ! $product instanceof WC_Order_Item_Product ) {
				/* Only products need changes. */
				continue;
			}
			$active_multiplier = (float) $product->get_meta( self::META_ACTIVE_MULTIPLIER );
			if ( $active_multiplier === $multiplier ) {
				/* Nothing to change. */
				continue;
			} elseif ( $active_multiplier ) {
				/* The product has wrong multiplier. Fix it. */
				$subtotal = $product->get_subtotal() / $active_multiplier * $multiplier;
				$product->set_subtotal( $subtotal );
				$total = $product->get_total() / $active_multiplier * $multiplier;
				$product->set_total( $total );
				$subtotal_tax = $product->get_subtotal_tax() / $active_multiplier * $multiplier;
				$product->set_subtotal_tax( $subtotal_tax );
				$total_tax = $product->get_total_tax() / $active_multiplier * $multiplier;
				$product->set_total_tax( $total_tax );
			}

			$product->update_meta_data( self::META_ACTIVE_MULTIPLIER, $multiplier, true );
			$product->save_meta_data();
		}

		return $items;
	}

	/**
	 * Hide additional metas.
	 *
	 * @param array $hidden Provided hidden metas.
	 * @return array
	 */
	public function hide_meta( $hidden ) {
		if ( ! in_array( self::META_ACTIVE_MULTIPLIER, $hidden, true ) ) {
			$hidden[] = self::META_ACTIVE_MULTIPLIER;
		}

		return $hidden;
	}

	/**
	 * Bind multi currency events.
	 */
	public function bind_common_events() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'wp_ajax_p24_mc_admin_order_edit', array( $this, 'admin_order_edit' ) );

		add_filter( 'woocommerce_ajax_add_order_item_validation', array( $this, 'fix_currencies' ), 10, 4 );
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_meta' ), 10, 1 );
		add_filter( 'woocommerce_order_get_items', array( $this, 'fix_currencies_again' ), 10, 4 );
	}
}
