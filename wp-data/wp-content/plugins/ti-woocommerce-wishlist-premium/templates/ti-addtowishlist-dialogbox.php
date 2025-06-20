<?php
/**
 * The Template for displaying dialog for add to wishlist product.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-addtowishlist-dialogbox.php.
 *
 * @version             1.21.3
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinvwl_add_to_select_wishlist tinv-modal">
	<div class="tinv-overlay"></div>
	<div class="tinv-table">
		<div class="tinv-cell">
			<div class="tinv-modal-inner">
				<i class="icon_big_heart_plus"></i>
				<label>
					<?php esc_html_e( 'Choose Wishlist:', 'ti-woocommerce-wishlist-premium' ); ?>
					<select class="tinvwl_wishlist"></select>
				</label>
				<input class="tinvwl_new_input" style="display: none" type="text" value=""/>
				<button class="button tinvwl-button tinvwl_button_add" type="button"><i
							class="ftinvwl ftinvwl-heart-o"></i><?php echo wp_kses_post( tinv_get_option( 'add_to_wishlist' . ( $loop ? '_catalog' : '' ), 'text' ) ); ?>
				</button>
				<button class="button tinvwl-button tinvwl_button_close" type="button"><i
							class="ftinvwl ftinvwl-times"></i><?php esc_html_e( 'Close', 'ti-woocommerce-wishlist-premium' ); ?>
				</button>
				<div class="tinv-wishlist-clear"></div>
			</div>
		</div>
	</div>
</div>
