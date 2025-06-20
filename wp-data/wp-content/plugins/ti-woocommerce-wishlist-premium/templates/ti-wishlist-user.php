<?php
/**
 * The Template for displaying wishlist if a current user not an owner.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-user.php.
 *
 * @version             2.3.3
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
wp_enqueue_script( 'tinvwl' );
?>
<div class="tinv-wishlist woocommerce tinv-wishlist-clear">
	<?php do_action( 'tinvwl_before_wishlist', $wishlist ); ?>
	<?php if ( function_exists( 'wc_print_notices' ) && isset( WC()->session ) ) {
		wc_print_notices();
	} ?>
	<?php $form_url = tinv_url_wishlist( $wishlist['share_key'], $wl_paged, true ); ?>
	<form action="<?php echo esc_url( $form_url ); ?>" method="post" autocomplete="off"
		  data-tinvwl_paged="<?php echo $wl_paged; ?>" data-tinvwl_per_page="<?php echo $wl_per_page; ?>"
		  data-tinvwl_sharekey="<?php echo $wishlist['share_key'] ?>">
		<?php do_action( 'tinvwl_before_wishlist_table', $wishlist ); ?>
		<table class="tinvwl-table-manage-list">
			<thead>
			<tr>
				<?php if ( isset( $wishlist_table['colm_checkbox'] ) && $wishlist_table['colm_checkbox'] ) { ?>
					<th class="product-cb"><input type="checkbox" class="global-cb input-checkbox"
												  title="<?php _e( 'Select all for bulk action', 'ti-woocommerce-wishlist-premium' ) ?>">
					</th>
				<?php } ?>
				<?php if ( isset( $wishlist_table_row['colm_image'] ) && $wishlist_table_row['colm_image'] ) { ?>
					<th class="product-thumbnail">&nbsp;</th>
				<?php } ?>
				<th class="product-name"><span
						class="tinvwl-full"><?php esc_html_e( 'Product Name', 'ti-woocommerce-wishlist-premium' ); ?></span><span
						class="tinvwl-mobile"><?php esc_html_e( 'Product', 'ti-woocommerce-wishlist-premium' ); ?></span>
				</th>
				<?php if ( isset( $wishlist_table_row['colm_price'] ) && $wishlist_table_row['colm_price'] ) { ?>
					<th class="product-price"><?php esc_html_e( 'Unit Price', 'ti-woocommerce-wishlist-premium' ); ?></th>
				<?php } ?>
				<?php if ( isset( $wishlist_table_row['colm_date'] ) && $wishlist_table_row['colm_date'] ) { ?>
					<th class="product-date"><?php esc_html_e( 'Date Added', 'ti-woocommerce-wishlist-premium' ); ?></th>
				<?php } ?>
				<?php if ( isset( $wishlist_table_row['colm_stock'] ) && $wishlist_table_row['colm_stock'] ) { ?>
					<th class="product-stock"><?php esc_html_e( 'Stock Status', 'ti-woocommerce-wishlist-premium' ); ?></th>
				<?php } ?>
				<?php if ( $qty && isset( $wishlist_table_row['colm_quantity'] ) && $wishlist_table_row['colm_quantity'] ) { ?>
					<th class="product-quantity"><?php esc_html_e( 'Qty', 'ti-woocommerce-wishlist-premium' ); ?></th>
				<?php } ?>
				<?php if ( isset( $wishlist_table_row['subtotal'] ) && $wishlist_table_row['subtotal'] ) { ?>
					<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'ti-woocommerce-wishlist-premium' ); ?></th>
				<?php } ?>
				<?php if ( isset( $wishlist_table_row['add_to_cart'] ) && $wishlist_table_row['add_to_cart'] ) { ?>
					<th class="product-action">&nbsp;</th>
				<?php } ?>
			</tr>
			</thead>
			<tbody>
			<?php do_action( 'tinvwl_wishlist_contents_before' ); ?>

			<?php

			global $product, $post;
			// store global product data.
			$_product_tmp = $product;
			// store global post data.
			$_post_tmp = $post;

			foreach ( $products as $wl_product ) {

				if ( empty( $wl_product['data'] ) ) {
					continue;
				}

				// override global product data.
				$product = apply_filters( 'tinvwl_wishlist_item', $wl_product['data'] );
				// override global post data.
				$post = get_post( $product->get_id() );

				unset( $wl_product['data'] );
				if ( ( $wl_product['quantity'] > 0 || apply_filters( 'tinvwl_allow_zero_quantity', false ) ) && apply_filters( 'tinvwl_wishlist_item_visible', true, $wl_product, $product ) ) {
					$product_url = apply_filters( 'tinvwl_wishlist_item_url', $product->get_permalink(), $wl_product, $product );
					do_action( 'tinvwl_wishlist_row_before', $wl_product, $product );
					?>
					<tr class="<?php echo esc_attr( apply_filters( 'tinvwl_wishlist_item_class', 'wishlist_item', $wl_product, $product ) ); ?>"
						data-tinvwl-pid="<?php echo esc_attr( $wl_product['ID'] ); ?>">
						<?php if ( isset( $wishlist_table['colm_checkbox'] ) && $wishlist_table['colm_checkbox'] ) { ?>
							<td class="product-cb">
								<?php
								echo apply_filters( 'tinvwl_wishlist_item_cb', sprintf( // WPCS: xss ok.
									'<input type="checkbox" class="input-checkbox" name="wishlist_pr[]" value="%d" title="%s">', esc_attr( $wl_product['ID'] ), __( 'Select for bulk action', 'ti-woocommerce-wishlist-premium' )
								), $wl_product, $product );
								?>
							</td>
						<?php } ?>
						<?php if ( isset( $wishlist_table_row['colm_image'] ) && $wishlist_table_row['colm_image'] ) { ?>
							<td class="product-thumbnail">
								<?php
								$thumbnail = apply_filters( 'tinvwl_wishlist_item_thumbnail', $product->get_image(), $wl_product, $product );

								if ( ! $product->is_visible() ) {
									echo $thumbnail; // WPCS: xss ok.
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $product_url ), $thumbnail ); // WPCS: xss ok.
								}
								?>
							</td>
						<?php } ?>
						<td class="product-name">
							<?php
							if ( ! $product->is_visible() ) {
								echo apply_filters( 'tinvwl_wishlist_item_name', is_callable( array(
										$product,
										'get_name'
									) ) ? $product->get_name() : $product->get_title(), $wl_product, $product ) . '&nbsp;'; // WPCS: xss ok.
							} else {
								echo apply_filters( 'tinvwl_wishlist_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_url ), is_callable( array(
									$product,
									'get_name'
								) ) ? $product->get_name() : $product->get_title() ), $wl_product, $product ); // WPCS: xss ok.
							}

							echo apply_filters( 'tinvwl_wishlist_item_meta_data', tinv_wishlist_get_item_data( $product, $wl_product ), $wl_product, $product ); // WPCS: xss ok.
							?>
						</td>
						<?php if ( isset( $wishlist_table_row['colm_price'] ) && $wishlist_table_row['colm_price'] ) { ?>
							<td class="product-price">
								<?php
								echo apply_filters( 'tinvwl_wishlist_item_price', $product->get_price_html(), $wl_product, $product, false ); // WPCS: xss ok.
								?>
							</td>
						<?php } ?>
						<?php if ( isset( $wishlist_table_row['colm_date'] ) && $wishlist_table_row['colm_date'] ) { ?>
							<td class="product-date">
								<?php
								echo apply_filters( 'tinvwl_wishlist_item_date', sprintf( // WPCS: xss ok.
									'<time class="entry-date" datetime="%1$s">%2$s</time>', $wl_product['date'], mysql2date( get_option( 'date_format' ), $wl_product['date'] )
								), $wl_product, $product );
								?>
							</td>
						<?php } ?>
						<?php if ( isset( $wishlist_table_row['colm_stock'] ) && $wishlist_table_row['colm_stock'] ) { ?>
							<td class="product-stock">
								<?php
								$availability = (array) $product->get_availability();
								if ( ! array_key_exists( 'availability', $availability ) ) {
									$availability['availability'] = '';
								}
								if ( ! array_key_exists( 'class', $availability ) ) {
									$availability['class'] = '';
								}
								$availability_html = empty( $availability['availability'] ) ? '<p class="stock ' . esc_attr( $availability['class'] ) . '"><span><i class="ftinvwl ftinvwl-check"></i></span><span class="tinvwl-txt">' . esc_html__( 'In stock', 'ti-woocommerce-wishlist-premium' ) . '</span></p>' : '<p class="stock ' . esc_attr( $availability['class'] ) . '"><span><i class="ftinvwl ftinvwl-' . ( ( 'out-of-stock' === esc_attr( $availability['class'] ) ? 'times' : 'check' ) ) . '"></i></span><span>' . wp_kses_post( $availability['availability'] ) . '</span></p>';

								echo apply_filters( 'tinvwl_wishlist_item_status', $availability_html, $availability['availability'], $wl_product, $product ); // WPCS: xss ok.
								?>
							</td>
						<?php } ?>
						<?php if ( $qty && isset( $wishlist_table_row['colm_quantity'] ) && $wishlist_table_row['colm_quantity'] ) { ?>
							<td class="product-quantity">
								<?php
								if ( $product->is_sold_individually() ) {
									$product_quantity = sprintf( '1 <input type="hidden" name="wishlist_qty[%d]" value="1" />', $wl_product['ID'] );
								} else {
									$product_quantity = woocommerce_quantity_input( array(
										'input_name'  => "wishlist_qty[{$wl_product['ID']}]",
										'input_value' => $wl_product['quantity'],
										'min_value'   => '0',
									), $product, false );
								}

								echo apply_filters( 'tinvwl_wishlist_item_quantity', $product_quantity, $wl_product, $product ); // WPCS: xss ok.
								?>
							</td>
						<?php } ?>
						<?php if ( isset( $wishlist_table_row['subtotal'] ) && $wishlist_table_row['subtotal'] ) { ?>
							<td class="product-subtotal">
								<?php echo wc_price( $wl_product['subtotal'] ); ?>
							</td>
						<?php } ?>
						<?php if ( ! isset( $wishlist_table_row['add_to_cart'] ) || $wishlist_table_row['add_to_cart'] ) { ?>
							<td class="product-action">
								<?php
								if ( apply_filters( 'tinvwl_wishlist_item_action_add_to_cart', $wishlist_table_row['add_to_cart'], $wl_product, $product ) ) {
									?>
									<button class="button tinvwl-button alt" name="tinvwl-add-to-cart"
											value="<?php echo esc_attr( $wl_product['ID'] ); ?>"
											title="<?php echo esc_html( apply_filters( 'tinvwl_wishlist_item_add_to_cart', $wishlist_table_row['text_add_to_cart'], $wl_product, $product ) ); ?>">
										<i class="ftinvwl ftinvwl-shopping-cart"></i><span
											class="tinvwl-txt"><?php echo wp_kses_post( apply_filters( 'tinvwl_wishlist_item_add_to_cart', $wishlist_table_row['text_add_to_cart'], $wl_product, $product ) ); ?></span>
									</button>
								<?php } elseif ( apply_filters( 'tinvwl_wishlist_item_action_default_loop_button', $wishlist_table_row['add_to_cart'], $wl_product, $product ) ) {
									woocommerce_template_loop_add_to_cart();
								} ?>
							</td>
						<?php } ?>
					</tr>
					<?php
					do_action( 'tinvwl_wishlist_row_after', $wl_product, $product );
				} // End if().
			} // End foreach().
			// restore global product data.
			$product = $_product_tmp;
			// restore global post data.
			$post = $_post_tmp;
			do_action( 'tinvwl_wishlist_contents_after' ); ?>
			</tbody>
			<tfoot>
			<?php if ( isset( $total ) && $total ) { ?>
				<tr>
					<td colspan="100" class="wishlist-total">
						<?php echo __( 'Total: ', 'ti-woocommerce-wishlist-premium' ) . wc_price( $total ); ?>
					</td>
				</tr>
			<?php } ?>
			<tr>
				<td colspan="100">
					<?php do_action( 'tinvwl_after_wishlist_table', $wishlist ); ?>
					<?php wp_nonce_field( 'tinvwl_wishlist_user', 'wishlist_nonce' ); ?>
				</td>
			</tr>
			</tfoot>
		</table>
	</form>
	<?php do_action( 'tinvwl_after_wishlist', $wishlist, $wl_per_page, $wl_paged ); ?>
	<div class="tinv-lists-nav tinv-wishlist-clear">
		<?php do_action( 'tinvwl_pagenation_wishlist', $wishlist ); ?>
	</div>
</div>
