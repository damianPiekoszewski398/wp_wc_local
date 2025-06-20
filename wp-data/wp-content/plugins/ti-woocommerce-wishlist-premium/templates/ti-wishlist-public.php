<?php
/**
 * The Template for displaying public wishlists this plugin.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-public.php.
 *
 * @version             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinv-wishlist">
	<div class="tinv-lists-nav tinv-wishlist-clear">
		<?php do_action( 'tinvwl_before_wishlistpublic_table', $wishlists ); ?>
	</div>
	<table class="tinvwl-table-manage-lists tinvwl-public">
		<thead>
		<tr>
			<th class="wishlist-name"><span
						class="tinvwl-full"><?php esc_html_e( 'List Name', 'ti-woocommerce-wishlist-premium' ); ?></span><span
						class="tinvwl-mobile"><?php esc_html_e( 'List', 'ti-woocommerce-wishlist-premium' ); ?></span>
			</th>
			<th class="wishlist-author"><?php esc_html_e( 'Author', 'ti-woocommerce-wishlist-premium' ); ?></th>
			<th class="wishlist-date"><?php esc_html_e( 'Date Added', 'ti-woocommerce-wishlist-premium' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php do_action( 'tinvwl_wishlistpublic_contents_before' ); ?>
		<?php
		foreach ( $wishlists as $wishlist ) {
			?>
			<tr>
				<td class="wishlist-name">
					<a href="<?php echo esc_url( tinv_url_wishlist( $wishlist['ID'] ) ); ?>">
						<?php
						echo apply_filters( 'tinvwl_wishlistpublic_item_name', $wishlist['title'], $wishlist ); // WPCS: xss ok.
						?>
					</a>
				</td>
				<td class="wishlist-author">
					<?php
					echo apply_filters( 'tinvwl_wishlistpublic_item_author', $wishlist['author'], $wishlist ); // WPCS: xss ok.
					?>
				</td>
				<td class="wishlist-date">
					<?php
					echo apply_filters( 'tinvwl_wishlistpublic_item_date', sprintf( // WPCS: xss ok.
							'<time class="entry-date" datetime="%1$s">%2$s</time>', $wishlist['date'], mysql2date( get_option( 'date_format' ), $wishlist['date'] )
					), $wishlist );
					?>
				</td>
			</tr>
		<?php } ?>
		<?php do_action( 'tinvwl_wishlistpublic_contents_after' ); ?>
		</tbody>
	</table>
	<?php do_action( 'tinvwl_after_wishlistpublic_table', $wishlists ); ?>
	<div class="tinv-lists-nav tinv-wishlist-clear">
		<?php do_action( 'tinvwl_pagenation_wishlistpublic_table', $wishlists ); ?>
	</div>
</div>
