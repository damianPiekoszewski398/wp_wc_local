<?php
/**
 * The Template for estimate email this plugin.
 *
 * @version             1.12.2
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

	<p><?php printf( __( 'You have received an Estimate Request from %s. The Request is as follows:', 'ti-woocommerce-wishlist-premium' ), sprintf( '<a href="%s">%s</a>', 'mailto:' . $wishlist['author_user_email'], $wishlist['author_display_name'] ) ); // WPCS: xss ok. ?></p>
	<h2><a href="<?php echo esc_url( $wishlist['url'] ) ?>"><?php echo esc_html( $wishlist['title'] ) ?></a></h2>
<?php if ( ! empty( $additional_note ) ) : ?>
	<h2><?php _e( 'Additional info:', 'ti-woocommerce-wishlist-premium' ); // WPCS: xss ok. ?></h2>
	<blockquote><?php echo wpautop( wptexturize( $additional_note ) ); // WPCS: xss ok. ?></blockquote>
<?php endif; ?>
<?php do_action( 'tinvwl_email_before_wishlist', $wishlist ); ?>
	<table>
		<thead>
		<tr>
			<th class="product-thumbnail">&nbsp;</th>
			<th class="product-name"><?php _e( 'Product Name', 'ti-woocommerce-wishlist-premium' ); // WPCS: xss ok. ?></th>
			<?php if ( isset( $wishlist_table_row['colm_price'] ) && $wishlist_table_row['colm_price'] ) { ?>
				<th class="product-price"><?php _e( 'Unit Price', 'ti-woocommerce-wishlist-premium' ); // WPCS: xss ok. ?></th>
			<?php } ?>
			<?php if ( isset( $wishlist_table_row['colm_stock'] ) && $wishlist_table_row['colm_stock'] ) { ?>
				<th class="product-stock"><?php _e( 'Stock Status', 'ti-woocommerce-wishlist-premium' ); // WPCS: xss ok. ?></th>
			<?php } ?>
			<?php if ( isset( $wishlist_table_row['colm_quantity'] ) && $wishlist_table_row['colm_quantity'] ) { ?>
				<th class="product-quantity"><?php _e( 'Qty', 'ti-woocommerce-wishlist-premium' ); // WPCS: xss ok. ?></th>
			<?php } ?>
		</tr>
		</thead>
		<tbody>
		<?php do_action( 'tinvwl_email_wishlist_contents_before' ); ?>

		<?php
		foreach ( $wishlist['products'] as $wl_product ) {

			if ( empty( $wl_product['data'] ) ) {
				continue;
			}

			$product = apply_filters( 'tinvwl_email_wishlist_item', $wl_product['data'], $plain_text );
			unset( $wl_product['data'] );
			if ( $wl_product['quantity'] > 0 && apply_filters( 'tinvwl_email_wishlist_item_visible', true, $wl_product, $product, $plain_text ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'tinvwl_email_wishlist_item_class', 'wishlist_item', $wl_product, $product, $plain_text ) ); ?>">
					<td class="product-thumbnail">
						<?php
						$thumbnail = apply_filters( 'tinvwl_email_wishlist_item_thumbnail', '<img src="' . ( $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'ti-woocommerce-wishlist-premium' ) . '" height="180" />', $wl_product, $product, $plain_text );

						if ( ! $product->is_visible() ) {
							echo $thumbnail; // WPCS: xss ok.
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product->get_permalink() ), $thumbnail ); // WPCS: xss ok.
						}
						?>
					</td>
					<td class="product-name">
						<?php
						if ( ! $product->is_visible() ) {
							echo apply_filters( 'tinvwl_email_wishlist_item_name', is_callable( array(
											$product,
											'get_name'
									) ) ? $product->get_name() : $product->get_title(), $wl_product, $product, $plain_text ) . '&nbsp;'; // WPCS: xss ok.
						} else {
							echo apply_filters( 'tinvwl_email_wishlist_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product->get_permalink() ), is_callable( array(
									$product,
									'get_name'
							) ) ? $product->get_name() : $product->get_title() ), $wl_product, $product, $plain_text ); // WPCS: xss ok.
						}

						echo apply_filters( 'tinvwl_email_wishlist_item_meta_data', tinv_wishlist_get_item_data( $product ), $wl_product, $product, $plain_text ); // WPCS: xss ok.
						?>
					</td>
					<?php if ( isset( $wishlist_table_row['colm_price'] ) && $wishlist_table_row['colm_price'] ) { ?>
						<td class="product-price">
							<?php
							echo apply_filters( 'tinvwl_email_wishlist_item_price', $product->get_price_html(), $wl_product, $product, $plain_text ); // WPCS: xss ok.
							?>
						</td>
					<?php } ?>
					<?php if ( isset( $wishlist_table_row['colm_stock'] ) && $wishlist_table_row['colm_stock'] ) { ?>
						<td class="product-stock">
							<?php
							$availability = $product->get_availability();

							$availability_html = empty( $availability['availability'] ) ? '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( __( 'In stock', 'ti-woocommerce-wishlist-premium' ) ) . '</p>' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';

							echo apply_filters( 'tinvwl_email_wishlist_item_status', $availability_html, $availability['availability'], $wl_product, $product, $plain_text ); // WPCS: xss ok.
							?>
						</td>
					<?php } ?>
					<?php if ( isset( $wishlist_table_row['colm_quantity'] ) && $wishlist_table_row['colm_quantity'] ) { ?>
						<td class="product-quantity">
							<?php echo apply_filters( 'tinvwl_email_wishlist_item_quantity', $wl_product['quantity'], $wl_product, $product, $plain_text ); // WPCS: xss ok. ?>
						</td>
					<?php } ?>
				</tr>
				<?php
			} // End if().
		} // End foreach().
		?>
		<?php do_action( 'tinvwl_email_wishlist_contents_after' ); ?>
		</tbody>
	</table>

<?php do_action( 'tinvwl_email_after_wishlist', $wishlist ); ?>
<?php
/**
 * Output the email footer
 *
 * @hooked WC_Emails::email_footer()
 */
do_action( 'woocommerce_email_footer', $email );
