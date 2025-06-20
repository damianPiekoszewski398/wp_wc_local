<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Fees and Discounts
 *
 * @version 1.5.0
 *
 * @slug woocommerce-fees-discounts
 *
 * @url https://pluginrepublic.com/wordpress-plugins/woocommerce-fees-and-discounts/
 *
 */

// If this file is called directly, abort.
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woocommerce-fees-discounts";

$name = "WooCommerce Fees and Discounts";

$available = defined( 'WCFAD_PLUGIN_VERSION' );

$tinvwl_integrations = is_array( $tinvwl_integrations ) ? $tinvwl_integrations : [];

$tinvwl_integrations[ $slug ] = array(
	'name'      => $name,
	'available' => $available,
);

if ( ! tinv_get_option( 'integrations', $slug ) ) {
	return;
}

if ( ! $available ) {
	return;
}
if ( ! function_exists( 'tinvwl_item_price_wcfad' ) ) {

	/**
	 * Modify price for WooCommerce Fees and Discounts.
	 *
	 * @param string $price Returned price.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_wcfad( $price, $wl_product, $product, $raw ) {

		if ( defined( 'WCFAD_PLUGIN_VERSION' ) ) {

			if ( wcfad_is_dynamic_pricing_enabled() == 'yes' ) {
				$rules = get_option( 'wcfad_dynamic_pricing_rules', array() );
				foreach ( $rules as $rule ) {
					if ( ! empty( $rule['rule'] ) ) {
						switch ( $rule['rule'] ) {
							case 'bxgx':
								// The number of products needed to qualify for the offer
								$buy = ! empty( $rule['buy'] ) ? $rule['buy'] : 1;
								// The number of products to receive for free
								$get = ! empty( $rule['get'] ) ? $rule['get'] : 1;
								// The maximum number of products to receive for free
								$max = ! empty( $rule['max'] ) ? $rule['max'] : 9999;
								// How we total up items to see if the offer is triggered
								$count_by = isset( $rule['count_by'] ) ? $rule['count_by'] : 'all';
								// Which products we need to check
								$applies_to = isset( $rule['applies_to'] ) ? $rule['applies_to'] : 'all';
								// The adjustment we're applying
								$adjustment = isset( $rule['adjustment'] ) ? $rule['adjustment'] : 'percentage';
								// How much of a fee or discount we're applying
								$amount = ! empty( $rule['amount'] ) ? $rule['amount'] : 0;

								// The number of items required to trigger the offer
								$required_items = $buy + $get;

								// The list of products that qualify for the offer
								if ( $applies_to == 'all' ) {
									$buy_products = 'all';
								} else if ( $applies_to == 'products' ) {
									$buy_products = isset( $rule['buy_products'] ) ? $rule['buy_products'] : 'all';
								} else if ( $applies_to == 'categories' ) {
									$buy_categories = isset( $rule['buy_categories'] ) ? $rule['buy_categories'] : 'all';
								}

								$product_id = $product->get_id();
								// Check for variable products
								$parent_id = $product->get_parent_id();

								// Check if this product is in the "Buy" list
								if (
									$applies_to == 'all' ||
									( $applies_to == 'products' && $buy_products != 'all' && ( in_array( $product_id, $buy_products ) || in_array( $parent_id, $buy_products ) ) ) ||
									( $applies_to == 'categories' && $buy_categories != 'all' && ( has_term( $buy_categories, 'product_cat', $product_id ) || has_term( $buy_categories, 'product_cat', $parent_id ) ) ) ) {

									$quantity = $wl_product['quantity'];
									// If $count_by is by product, we need to check if an eligible product has enough items
									$quantity       = $wl_product['quantity'];
									$calculated_max = floor( $quantity / $required_items );
									$calculated_max = min( $calculated_max * $get, $max );
									if ( $quantity > $buy ) {
										$price = tinvwl_wcfad_get_price( $wl_product, $product, $calculated_max, $adjustment, $amount, $rule, $raw );
									}
								}


								break;
						}
					}
				}
			}
		}

		return $price;
	}

	add_filter( 'tinvwl_wishlist_item_price', 'tinvwl_item_price_wcfad', 10, 4 );
} // End if().

function tinvwl_wcfad_get_price( $wl_product, $product, $calculated_max, $adjustment, $amount, $rule, $raw ) {
	// The original price for the product, before any discount
	$original_price = $product->get_price();
	$quantity       = $wl_product['quantity'];

	$max_get = $calculated_max;

	// The number of items from this line item that are not discounted
	$non_discounted_quantity = ( $max_get <= $quantity ) ? $quantity - $max_get : 0;
	// The number of items from this line item that are discounted
	$discounted_quantity = $quantity - $non_discounted_quantity;
	// The number of discountable items can't be greater than the maximum number of permitted discountable items
	$discounted_quantity = min( $discounted_quantity, $max_get );

	// Apply the adjustment to the price
	$adjusted_price = 0;
	if ( $adjustment == 'fixed-discount' ) {
		// Reduce the product price by a set amount
		$adjusted_price = ( $original_price > $amount ) ? $original_price - $amount : 0;
	} else if ( $adjustment == 'percentage-discount' ) {
		// Reduce the product price by a percentage
		$percentage_price = $original_price * ( $amount / 100 );
		$adjusted_price   = ( $original_price > $percentage_price ) ? $original_price - $percentage_price : 0;
	} else if ( $adjustment == 'fixed-fee' ) {
		// Increase the product price by a set amount
		$adjusted_price = $original_price + $amount;
	} else if ( $adjustment == 'percentage-fee' ) {
		// Increase the product price by a percentage
		$percentage_price = $original_price * ( $amount / 100 );
		$adjusted_price   = $original_price + $percentage_price;
	}

	if ( $raw ) {
		return $adjusted_price;
	}

	// Set price string
	if ( $original_price != $adjusted_price ) {

		// Show strikethrough if adjusted and original prices are different
		$price = sprintf(
			'<span class="wcfad-adjusted-price-quantity">%s x </span><del>%s</del> %s',
			$discounted_quantity,
			wc_price( $original_price ),
			wc_price( $adjusted_price )
		);
	} else {

		$price = sprintf(
			'<span class="wcfad-adjusted-price-quantity">%s x </span>%s',
			$discounted_quantity,
			wc_price( $original_price )
		);
	}

	if ( $non_discounted_quantity > 0 ) {
		$price .= sprintf(
			'<br>%s x %s',
			$non_discounted_quantity,
			wc_price( $original_price )
		);
	}

	return $price;
}
