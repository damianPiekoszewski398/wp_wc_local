<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Min Max Quantity & Step Control Single
 *
 * @version 1.6
 *
 * @slug woo-min-max-quantity-step-control-single
 *
 * @url https://codecanyon.net/item/woocommerce-min-max-quantity-step-control/22962198
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woo-min-max-quantity-step-control-single";

$name = "WooCommerce Min Max Quantity & Step Control Single";

$available = function_exists('wcmmq_s_quantity_input_args');

$tinvwl_integrations = is_array($tinvwl_integrations) ? $tinvwl_integrations : [];

$tinvwl_integrations[$slug] = array(
	'name' => $name,
	'available' => $available,
);

if (!tinv_get_option('integrations', $slug)) {
	return;
}

if (!$available) {
	return;
}

if (!function_exists('tinvwl_disable_wcmmq')) {

	/**
	 * Disable WooCommerce Min Max Quantity & Step Control Single
	 *
	 */
	function tinvwl_disable_wcmmq($cookies = array())
	{
		remove_filter('woocommerce_quantity_input_args', 'wcmmq_s_quantity_input_args', 10, 2);
	}

	add_action('tinvwl_wishlist_contents_before', 'tinvwl_disable_wcmmq');
}
