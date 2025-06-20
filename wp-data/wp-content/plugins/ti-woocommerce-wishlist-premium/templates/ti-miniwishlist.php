<?php
/**
 * The Template for displaying mini wishlist products.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-miniwishlist.php.
 *
 * @version             2.2.0
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

$tmp_product = $product;
?>
<div class="tinvwl-mini-wishlist-wrapper">
	<?php do_action( 'tinvwl_before_mini_wishlist' ); ?>
	<ul class="product_list_widget  <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
		if ( ! empty( $products ) ) :
			do_action( 'tinvwl_before_mini_wishlist_contents' );
			foreach ( $products as $wl_product ) {
				if ( empty( $wl_product['data'] ) ) {
					continue;
				}

				$product = apply_filters( 'tinvwl_mini_wishlist_item', $wl_product['data'] );
				unset( $wl_product['data'] );
				if ( $wl_product['quantity'] > 0 && apply_filters( 'tinvwl_mini_wishlist_item_visible', true, $wl_product, $product ) ) {
					$product_url   = apply_filters( 'tinvwl_mini_wishlist_item_url', $product->get_permalink(), $wl_product, $product );
					$product_name  = apply_filters( 'tinvwl_mini_wishlist_item_name', is_callable( array(
						$product,
						'get_name'
					) ) ? $product->get_name() : $product->get_title(), $wl_product, $product );
					$thumbnail     = apply_filters( 'tinvwl_mini_wishlist_item_thumbnail', $product->get_image(), $wl_product, $product );
					$product_meta  = apply_filters( 'tinvwl_mini_wishlist_item_meta_data', tinv_wishlist_get_item_data( $product ), $wl_product, $product );
					$product_price = apply_filters( 'tinvwl_mini_wishlist_item_price', $product->get_price_html(), $wl_product, $product );
					$wishlists     = apply_filters( 'tinvwl_mini_wishlist_item_wishlist', $wl_product['wishlists'], $wl_product, $product );
					$sharekey      = is_array( $wishlists ) && isset( array_values( $wishlists )[0]['share_key'] ) ? array_values( $wishlists )[0]['share_key'] : '';
					?>
				<li
					class="<?php echo esc_attr( apply_filters( 'tinvwl_mini_wishlist_item_class', 'mini_wishlist_item', $wl_product, $product ) ); ?>">
					<?php if ( $args['remove'] ) { ?>
						<button type="submit" name="tinvwl-remove"
								value="<?php echo esc_attr( $wl_product['ID'] ); ?>"
								data-tinvwl_paged="1"
								data-tinvwl_per_page="<?php echo $args['count_product']; ?>"
								data-tinvwl_sharekey="<?php echo $sharekey; ?>"
								title="<?php _e( 'Remove', 'ti-woocommerce-wishlist-premium' ) ?>"><i
								class="ftinvwl ftinvwl-times"></i>
						</button>
					<?php } ?>
					<div>
						<?php
						if ( ! $product->is_visible() ) {
							echo $thumbnail; // WPCS: xss ok.
							echo $product_name; // WPCS: xss ok.
							echo '&nbsp;';
						} else {
							?>
							<a href="<?php echo esc_url( $product_url ); ?>">
								<?php
								echo $thumbnail; // WPCS: xss ok.
								echo $product_name; // WPCS: xss ok.
								echo '&nbsp;';
								?>
							</a>
							<?php
						}
						echo $product_meta; // WPCS: xss ok.
						echo apply_filters( 'tinvwl_mini_wishlist_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $wl_product['quantity'], $product_price ) . '</span>', $wl_product, $product ); // WPCS: xss ok.
						if ( $args['show_wishlist'] ) {
							?>
							<ul class="wishlist_list_titles">
								<?php
								foreach ( $wishlists as $key => $wishlist ) {
									$wishlists[ $key ] = apply_filters( 'tinvwl_mini_wishlist_item_wishlist', sprintf( '<li><a href="%s" class="wishlist">%s</a></li>', esc_url( $wishlist['url'] ), esc_html( $wishlist['title'] ) ), $wl_product, $product, $wishlist ); // WPCS: xss ok.
								}
								echo implode( ' ', $wishlists ); // WPCS: xss ok.
								if ( 3 < count( $wishlists ) ) {
									echo sprintf( ' <li class="wishlist_title_more"><a href="#" class="wishlist_title_more">%s</a></li>', esc_html__( 'more...', 'ti-woocommerce-wishlist-premium' ) ); // WPCS: xss ok.
								}
								?>
							</ul>
						<?php } ?>
					</div>
					<?php if ( $args['add_to_cart'] ) { ?>
						<div class="tinvwl-action-buttons">
							<?php if ( apply_filters( 'tinvwl_wishlist_item_action_add_to_cart', true, $wl_product, $product ) ) { ?>
								<button class="button tinvwl-button alt" name="tinvwl-add-to-cart"
										value="<?php echo esc_attr( $wl_product['ID'] ); ?>"
										data-tinvwl_paged="1"
										data-tinvwl_per_page="<?php echo $args['count_product']; ?>"
										data-tinvwl_sharekey="<?php echo $sharekey; ?>"
										title="<?php echo esc_html( apply_filters( 'tinvwl_wishlist_item_add_to_cart', apply_filters( 'tinvwl_add_to_cart_text', tinv_get_option( 'product_table', 'text_add_to_cart' ) ), $wl_product, $product ) ); ?>">
						<span
							class="tinvwl-txt"><?php echo wp_kses_post( apply_filters( 'tinvwl_wishlist_item_add_to_cart', apply_filters( 'tinvwl_add_to_cart_text', tinv_get_option( 'product_table', 'text_add_to_cart' ) ), $wl_product, $product ) ); ?></span>
								</button>
							<?php } elseif ( apply_filters( 'tinvwl_wishlist_item_action_default_loop_button', true, $wl_product, $product ) ) {
								woocommerce_template_loop_add_to_cart();
							} ?>
						</div>
					<?php } ?>
					</li><?php
				} // End if().
			} // End foreach().
			do_action( 'tinvwl_after_mini_wishlist_contents' );
		else :
			?>
			<li class="empty"><?php esc_html_e( 'No products in the wishlist.', 'ti-woocommerce-wishlist-premium' ); ?></li>
		<?php endif; ?>
	</ul>

	<?php if ( ! empty( $products ) ) : ?>
		<p class="total"><strong><?php esc_html_e( 'Total products', 'ti-woocommerce-wishlist-premium' ); ?>
				:</strong> <?php echo $subtotal_count; // WPCS: xss ok. ?></p>
	<?php endif; ?>

	<?php do_action( 'tinvwl_after_mini_wishlist', $products );

	$product = $tmp_product;
	?>
</div>
