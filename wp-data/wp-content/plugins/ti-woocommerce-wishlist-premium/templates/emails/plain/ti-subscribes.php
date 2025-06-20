<?php
/**
 * The Template for displaying notification email for change actions by follow wishlist plain this plugin.
 *
 * @version             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '= ' . $email_heading . " =\n\n"; // WPCS: xss ok.
if ( $user_name ) {
	echo sprintf( esc_html( 'Hi %s', 'ti-woocommerce-wishlist-premium' ), esc_html( $user_name ) ) . "\n\n";
}
echo sprintf( esc_html( '"%s" wishlist has been changed:', 'ti-woocommerce-wishlist-premium' ), esc_html( $wishlist['title'] ) ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
foreach ( $events_name as $key => $name ) {
	if ( ! array_key_exists( $key, $products ) ) {
		continue;
	}
	echo $name_event; // WPCS: xss ok.
	foreach ( $products[ $key ] as $product ) {
		echo "----------\n\n";
		echo apply_filters( 'tinvwl_email_wishlist_item_name', is_callable( array(
				$product,
				'get_name'
			) ) ? $product->get_name() : $product->get_title(), $product, $plain_text ) . "\n"; // WPCS: xss ok.
		echo apply_filters( 'tinvwl_email_wishlist_item_meta_data', tinv_wishlist_get_item_data( $product, true ), $product, $plain_text ) . "\n"; // WPCS: xss ok.
		echo apply_filters( 'tinvwl_email_wishlist_item_price', $product->get_price(), $product, $plain_text ) . "\n"; // WPCS: xss ok.
		$availability      = $product->get_availability();
		$availability_text = empty( $availability['availability'] ) ? esc_html( __( 'In stock', 'ti-woocommerce-wishlist-premium' ) ) : esc_html( $availability['availability'] );

		echo apply_filters( 'tinvwl_email_wishlist_item_status', $availability_text, $availability['availability'], $product, $plain_text ) . "\n"; // WPCS: xss ok.
		echo apply_filters( 'tinvwl_email_wishlist_item_quantity', $product->wl_quantity, $product, $plain_text ) . "\n"; // WPCS: xss ok.
		echo "----------\n\n";
	}
}
echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ); // WPCS: xss ok.
