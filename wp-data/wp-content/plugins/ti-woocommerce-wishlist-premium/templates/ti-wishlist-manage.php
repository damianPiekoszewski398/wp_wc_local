<?php
/**
 * The Template for displaying wishlist manage this plugin.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-manage.php.
 *
 * @version             2.5.0
 */

/**
 * Template variables.
 *
 * @var array $wishlists Wishlists data.
 * @var string $wl_paged Number of the current page.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinv-wishlist woocommerce tinv-wishlist-clear">
	<?php do_action( 'tinvwl_before_wishlistmanage' ); ?>
	<?php if ( function_exists( 'wc_print_notices' ) && isset( WC()->session ) ) {
		wc_print_notices();
	} ?>
	<form
		action="<?php echo esc_url( get_permalink( apply_filters( 'wpml_object_id', tinv_get_option( 'page', 'manage' ), 'page', true ) ) ); ?>"
		method="post"
		data-tinvwl_paged="<?php echo $wl_paged; ?>"
		id="tinvwl-manage-wishlists">
		<?php do_action( 'tinvwl_before_wishlistmanage_table', $wishlists ); ?>
		<table class="tinvwl-table-manage-lists">
			<thead>
			<tr>
				<th class="wishlist-cb"><input type="checkbox" class="global-cb input-checkbox"></th>
				<th class="wishlist-name"><span
						class="tinvwl-full"><?php esc_html_e( 'List Name', 'ti-woocommerce-wishlist-premium' ); ?></span><span
						class="tinvwl-mobile"><?php esc_html_e( 'List', 'ti-woocommerce-wishlist-premium' ); ?></span>
				</th>
				<th class="wishlist-date"><?php esc_html_e( 'Date Added', 'ti-woocommerce-wishlist-premium' ); ?></th>
				<?php if ( ! tinv_get_option( 'general', 'my_account_endpoint' ) && TInvWL_Public_Manage_Wishlist::get_wishlists_privacy() ) { ?>
					<th class="wishlist-privacy"><?php esc_html_e( 'Privacy Settings', 'ti-woocommerce-wishlist-premium' ); ?></th>
				<?php } ?>
				<th class="wishlist-action">&nbsp;</th>
			</tr>
			</thead>
			<tbody>
			<?php do_action( 'tinvwl_wishlistmanage_contents_before' ); ?>
			<?php foreach ( $wishlists as $wishlist ) {
				?>
				<tr>
					<td class="wishlist-cb">
						<?php
						echo apply_filters( 'tinvwl_wishlistmanage_item_cb', sprintf( // WPCS: xss ok.
							'<input type="checkbox" class="input-checkbox" name="wishlist_pr[]" value="%d">', $wishlist['ID']
						), $wishlist );
						?>
					</td>
					<td class="wishlist-name">
						<?php
						echo apply_filters( 'tinvwl_wishlistmanage_item_name', sprintf( '<a href="%s">%s</a> <sup>%d</sup>', esc_url( tinv_url_wishlist( $wishlist['ID'] ) ), esc_html( $wishlist['title'] ), esc_html( $wishlist['count'] ) ), $wishlist ); // WPCS: xss ok.
						?>
					</td>
					<td class="wishlist-date">
						<?php
						echo apply_filters( 'tinvwl_wishlistmanage_item_date', sprintf( // WPCS: xss ok.
							'<time class="entry-date" datetime="%1$s">%2$s</time>', $wishlist['date'], mysql2date( get_option( 'date_format' ), $wishlist['date'] )
						), $wishlist );
						?>
					</td>
					<?php if ( ! tinv_get_option( 'general', 'my_account_endpoint' ) && TInvWL_Public_Manage_Wishlist::get_wishlists_privacy() ) { ?>
						<td class="wishlist-privacy">
							<?php
							echo apply_filters( 'tinvwl_wishlistmanage_item_privacy', $wishlist['status'], $wishlist ); // WPCS: xss ok.
							?>
						</td>
					<?php } ?>
					<td class="wishlist-action">
						<?php if ( tinv_get_option( 'general', 'download_pdf' ) ) { ?>
							<a href="<?php echo add_query_arg( 'tinvwl_download_wishlist', $wishlist['share_key'] ); ?>"
							   class="button tinvwl-button"><span
									class="tinvwl-txt"><?php esc_html_e( 'Download', 'ti-woocommerce-wishlist-premium' ); ?></span>
							</a>
						<?php } ?>
						<?php if ( ! in_array( $wishlist['type'], apply_filters( 'tinvwl_wishlist_type_exclusion', array( 'default' ) ) ) ) : ?>
							<button type="button" class="button tinvwl-button tinv-modal-btn" name="tinvwl-action"
									value="manage_remove"><i
									class="ftinvwl ftinvwl-times"></i><span><?php esc_html_e( 'Delete', 'ti-woocommerce-wishlist-premium' ); ?></span>
							</button>
							<div class="tinv-modal">
								<div class="tinv-overlay"></div>
								<div class="tinv-table">
									<div class="tinv-cell">
										<div class="tinv-modal-inner">
											<div class="ti-ps-form"
												 data-action="<?php echo esc_url( get_permalink( apply_filters( 'wpml_object_id', tinv_get_option( 'page', 'manage' ), 'page', true ) ) ); ?>"
												 data-method="post">
												<i class="icon_big_times"></i>
												<div
													class="delete-notification"><?php printf( __( 'Remove Wishlist "%s"?', 'ti-woocommerce-wishlist-premium' ), esc_html( $wishlist['title'] ) ); // WPCS: xss ok. ?></div>
												<button class="tinvwl_button_view" type="submit"
														name="tinvwl-wishlist-remove-btn"><?php esc_html_e( 'Confirm', 'ti-woocommerce-wishlist-premium' ); ?></button>

												<button class="button tinvwl_button_close" type="button"><i
														class="ftinvwl ftinvwl-times"></i><?php esc_html_e( 'Close', 'ti-woocommerce-wishlist-premium' ); ?>
												</button>
												<input type="hidden" name="tinvwl-wishlist-remove"
													   value="<?php echo esc_attr( $wishlist['ID'] ); ?>">
												<?php wp_nonce_field( 'tinvwl_wishlistmanage', 'wishlistmanage_nonce' ); ?>
											</div>
											<div class="tinv-wishlist-clear"></div>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>
					</td>
				</tr>
			<?php } // End foreach(). ?>
			<?php do_action( 'tinvwl_wishlistmanage_contents_after' ); ?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="100">
					<?php do_action( 'tinvwl_after_wishlistmanage_table', $wishlists ); ?>
					<?php wp_nonce_field( 'tinvwl_wishlistmanage', 'wishlistmanage_nonce' ); ?>
				</td>
			</tr>
			</tfoot>
		</table>
	</form>
	<?php do_action( 'tinvwl_after_wishlistmanage' ); ?>
	<div class="tinv-lists-nav tinv-wishlist-clear">
		<?php do_action( 'tinvwl_pagenation_wishlistmanage' ); ?>
	</div>
</div>
