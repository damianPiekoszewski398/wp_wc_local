<?php
/**
 * The Template for displaying wishlist confirm deleting in table.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<a class="tinvwl-modal-btn tinvwl-btn tinvwl-w-mobile white small" href="javascript:void(0)" rel="nofollow"><i
			class="tinvwl-mobile ftinvwl ftinvwl-times"></i><span
			class="tinvwl-full"><?php esc_html_e( 'Delete', 'ti-woocommerce-wishlist-premium' ) ?></span></a>
<div class="tinvwl-send-promo-emails tinvwl-modal">
	<div class="tinvwl-overlay"></div>
	<div class="tinvwl-table">
		<div class="tinvwl-cell">
			<div class="tinvwl-modal-inner">
				<p><?php printf( __( 'Are you sure you want to delete wishlist "%s"?', 'ti-woocommerce-wishlist-premium' ), esc_html( $wishlist['title'] ) ); // WPCS: xss ok. ?></p>
				<a class="tinvwl-btn black large tinvwl-close-modal" href="javascript:void(0)"
				   rel="nofollow"><?php esc_html_e( 'Cancel', 'ti-woocommerce-wishlist-premium' ); ?></a>
				<a class="tinvwl-btn large"
				   href="<?php echo esc_url( $remove_url ) ?>"><?php esc_html_e( 'Delete', 'ti-woocommerce-wishlist-premium' ) ?></a>
			</div>
		</div>
	</div>
</div>
