<?php
/**
 * The Template for displaying empty search wishlists.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-searchform.php.
 *
 * @version             1.10.0
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinv-wishlist">
	<div class="tinv-search-list">
		<form role="search" method="get" class="tinv-search-form tinv-wrapped-block"
			  action="<?php echo esc_url( get_permalink( tinv_get_option( 'page', 'search' ) ) ); ?>">
			<div class="tinvwl-input-group">
				<input type="search" class="tinv-search-field form-control"
					   placeholder="<?php echo esc_attr( $placeholder_input_text, 'ti-woocommerce-wishlist-premium' ) ?>"
					   value="<?php echo esc_attr( $search ) ?>" name="tiws" required/>
				<span class="tinvwl-input-group-btn">
					<button type="submit" class="tinv-search-submit"><?php echo esc_html( $button_text ) ?></button>
				</span>
			</div>
		</form>
	</div>
</div>
