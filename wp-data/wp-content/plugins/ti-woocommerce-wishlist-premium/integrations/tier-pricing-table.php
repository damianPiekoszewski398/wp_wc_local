<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Tiered Price Table
 *
 * @version 2.3.3
 *
 * @slug tier-pricing-table
 *
 * @url https://wordpress.org/plugins/tier-pricing-table
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "tier-pricing-table";

$name = "WooCommerce Tiered Price Table";

$available = class_exists('TierPricingTable\PriceManager');

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

if (!function_exists('tinvwl_item_price_tier_pricing_table')) {

	/**
	 * Modify price for WooCommerce Tiered Price Table
	 *
	 * @param string $price Returned price.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_tier_pricing_table($price, $wl_product, $product, $raw)
	{
		if (class_exists('TierPricingTable\PriceManager') && is_object($product)) {
			$total = TierPricingTable\PriceManager::getPriceByRules($wl_product['quantity'], $product->get_id());
			if (!$total) {
				$total = $product->get_price();
			}

			if ( $raw ) {
				return $total / $wl_product['quantity'];
			}

			$price = wc_price($total / $wl_product['quantity']);
		}

		return $price;
	}

	add_filter('tinvwl_wishlist_item_price', 'tinvwl_item_price_tier_pricing_table', 10, 4);
} // End if().
