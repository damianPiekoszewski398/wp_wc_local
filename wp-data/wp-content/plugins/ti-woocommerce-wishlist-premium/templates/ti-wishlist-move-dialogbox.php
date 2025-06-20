<?php
/**
 * The Template for displaying dialog box for move product this plugin.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-move-dialogbox.php.
 *
 * @version             1.9.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinvwl_wishlist_move tinv-modal">
	<div class="tinv-overlay"></div>
	<div class="tinv-table">
		<div class="tinv-cell">
			<div class="tinv-modal-inner">
				<a class="tinv-close-modal" href="javascript:void(0)"><i class="ftinvwl ftinvwl-times"></i></a>
				<i class="icon_big_heart_next"></i>
				<select class="tinvwl_wishlist"></select>
				<input class="tinvwl_new_input" style="display: none" type="text" name="wishlist_name" value=""/>
				<button class="tinvwl_button_move" type="button"><i
							class="ftinvwl ftinvwl-arrow-right"></i><?php esc_html_e( 'Move to', 'ti-woocommerce-wishlist-premium' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>
