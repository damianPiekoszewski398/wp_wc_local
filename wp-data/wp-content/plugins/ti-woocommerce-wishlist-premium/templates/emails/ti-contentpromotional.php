<?php
/**
 * The Template for promotional email content this plugin.
 *
 * @version             1.0.0
 * @package           TInvWishlist\Admin\Template
 * @codingStandardsIgnoreFile Generic.Files.LowercasedFilename.NotFound
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<p><?php esc_html_e( 'Hi', 'ti-woocommerce-wishlist-premium' ); ?> {user_name}</p>
<p><?php esc_html_e( 'A product from your wishlist is on sale!', 'ti-woocommerce-wishlist-premium' ); ?></p>
<p>{product_in_wishlists}</p>
<p>
<table>
	<tr>
		<td>{product_image}</td>
		<td>{product_name}</td>
		<td>{product_price}</td>
	</tr>
</table>
</p>
<p><?php echo sprintf( __( 'Use this code %s to obtain a discount.', 'ti-woocommerce-wishlist-premium' ), '<b><a href="{wishlist_with_product}" >{coupon_code}</a></b>' ); // WPCS: xss ok. ?></p>
