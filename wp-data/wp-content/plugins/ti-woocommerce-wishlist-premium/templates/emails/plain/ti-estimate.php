<?php
/**
 * The Template for displaying esctimate email plain this plugin.
 *
 * @version             1.12.2
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '= ' . $email_heading . " =\n\n"; // WPCS: xss ok.
echo sprintf( __( 'You have received an Estimate Request from %1$s ( %2$s ). The Request is as follows:', 'ti-woocommerce-wishlist-premium' ), esc_html( $wishlist['author_display_name'] ), esc_html( $wishlist['author_user_email'] ) ) . "\n\n"; // WPCS: xss ok.
echo sprintf( __( '%1$s ( %2$s )', 'ti-woocommerce-wishlist-premium' ), $wishlist_data['title'], $wishlist['url'] ) . "\n\n"; // WPCS: xss ok.
if ( ! empty( $additional_note ) ) {
	echo __( 'Additional info:', 'ti-woocommerce-wishlist-premium' ) . "\n\n"; // WPCS: xss ok.
	echo "----------\n\n";
	echo wptexturize( $additional_note ) . "\n\n"; // WPCS: xss ok.
	echo "----------\n\n";
}
echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
foreach ( $wishlist['products'] as $wl_product ) {

	if ( empty( $wl_product['data'] ) ) {
		continue;
	}

	$product = apply_filters( 'tinvwl_email_wishlist_item', $wl_product['data'], $plain_text );
	unset( $wl_product['data'] );
	if ( $wl_product['quantity'] > 0 && apply_filters( 'tinvwl_email_wishlist_item_visible', true, $wl_product, $product, $plain_text ) ) {
		echo "----------\n\n";
		echo apply_filters( 'tinvwl_email_wishlist_item_name', is_callable( array(
				$product,
				'get_name'
			) ) ? $product->get_name() : $product->get_title(), $wl_product, $product, $plain_text ) . "\n"; // WPCS: xss ok.
		echo apply_filters( 'tinvwl_email_wishlist_item_meta_data', tinv_wishlist_get_item_data( $product, true ), $wl_product, $product, $plain_text ) . "\n\n"; // WPCS: xss ok.
		if ( isset( $wishlist_table_row['colm_price'] ) && $wishlist_table_row['colm_price'] ) {
			echo apply_filters( 'tinvwl_email_wishlist_item_price', $product->get_price(), $wl_product, $product, $plain_text ) . "\n\n"; // WPCS: xss ok.
		}
		if ( isset( $wishlist_table_row['colm_stock'] ) && $wishlist_table_row['colm_stock'] ) {
			$availability      = $product->get_availability();
			$availability_text = empty( $availability['availability'] ) ? esc_html( __( 'In stock', 'ti-woocommerce-wishlist-premium' ) ) : esc_html( $availability['availability'] );

			echo apply_filters( 'tinvwl_email_wishlist_item_status', $availability_text, $availability['availability'], $wl_product, $product, $plain_text ) . "\n\n"; // WPCS: xss ok.
		}
		if ( isset( $wishlist_table_row['colm_quantity'] ) && $wishlist_table_row['colm_quantity'] ) {
			echo apply_filters( 'tinvwl_email_wishlist_item_quantity', $wl_product['quantity'], $wl_product, $product, $plain_text ); // WPCS: xss ok.
		}

		echo "----------\n\n";
	}
}
echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ); // WPCS: xss ok.
