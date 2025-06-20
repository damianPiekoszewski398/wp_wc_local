<?php
/**
 * The Template for displaying dialog for message created wishlist.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-create-message.php.
 *
 * @version            2.0.0
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinvwl_created_wishlist tinv-modal tinv-modal-open">
	<div class="tinv-overlay"></div>
	<div class="tinv-table">
		<div class="tinv-cell">
			<div class="tinv-modal-inner">
				<i class="<?php echo esc_attr( $icon ); ?>"></i>
				<div class="tinv-txt"><?php echo $msg; // WPCS: xss ok. ?></div>
				<div class="tinvwl-buttons-group tinv-wishlist-clear">
					<?php if ( ! empty( $wishlist_url ) ) { ?>
						<button class="button tinvwl_button_view tinvwl-btn-onclick"
								data-url="<?php echo esc_url( $wishlist_url ); ?>" type="button"><i
								class="ftinvwl ftinvwl-heart-o"></i><?php esc_html_e( 'View Wishlist', 'ti-woocommerce-wishlist-premium' ); ?>
						</button>
					<?php } ?>
					<button class="button tinvwl-button tinvwl_button_close" type="button"><i
							class="ftinvwl ftinvwl-times"></i><?php esc_html_e( 'Close', 'ti-woocommerce-wishlist-premium' ); ?>
					</button>
				</div>
				<div class="tinv-wishlist-clear"></div>
			</div>
		</div>
	</div>
</div>
<?php if ( $redirect ) { ?>
	<script type="text/javascript">
		setTimeout("window.location.href = window.location.href;", 5000);
	</script>
<?php } ?>
