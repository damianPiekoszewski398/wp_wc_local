<?php
/**
 * The Template for displaying notification email for change actions by follow wishlist this plugin.
 *
 * @version             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Output the email header
 *
 * @hooked WC_Emails::email_header()
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
?>
<?php if ( $user_name ) { ?>
	<p><?php printf( 'Hi %s', esc_html( $user_name ) ); ?></p>
<?php } ?>
	<p><?php printf( __( '"%s" wishlist has been changed:', 'ti-woocommerce-wishlist-premium' ), sprintf( '<a target="_blank" href="%s">%s</a>', esc_url( $wishlist['url'] ), esc_html( $wishlist['title'] ) ) ); // WPCS: xss ok. ?></p>
<?php
foreach ( $events_name as $key => $name_event ) {
	if ( ! array_key_exists( $key, $products ) ) {
		continue;
	}
	?>
	<p><?php echo $name_event; // WPCS: xss ok. ?></p>
	<table>
		<?php foreach ( $products[ $key ] as $product ) { ?>
			<tr>
				<td><?php echo '<img src="' . ( $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'ti-woocommerce-wishlist-premium' ) . '" width="180" />'; // WPCS: xss ok. ?></td>
				<td>
					<?php echo apply_filters( 'tinvwl_email_wishlist_item_name', is_callable( array(
							$product,
							'get_name'
					) ) ? $product->get_name() : $product->get_title(), $product, $plain_text ); // WPCS: xss ok. ?>
					<?php echo apply_filters( 'tinvwl_email_wishlist_item_meta_data', tinv_wishlist_get_item_data( $product, true ), $product, $plain_text ); // WPCS: xss ok. ?>
				</td>
				<td><?php echo apply_filters( 'tinvwl_email_wishlist_item_price', $product->get_price_html(), $product, $plain_text ); // WPCS: xss ok. ?></td>
				<td><?php
					$availability      = $product->get_availability();
					$availability_text = empty( $availability['availability'] ) ? esc_html( __( 'In stock', 'ti-woocommerce-wishlist-premium' ) ) : esc_html( $availability['availability'] );

					echo apply_filters( 'tinvwl_email_wishlist_item_status', $availability_text, $availability['availability'], $product, $plain_text ); // WPCS: xss ok.
					?></td>
				<td><?php echo $product->wl_quantity; // WPCS: xss ok. ?></td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>
<?php
/**
 * Output the email footer
 *
 * @hooked WC_Emails::email_footer()
 */
do_action( 'woocommerce_email_footer', $email );
