<?php
/**
 * The Template for displaying search widget this plugin.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-widget-searchform.php.
 *
 * @version             1.10.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<form role="search" method="get" class="tinvwl-search-form"
	  action="<?php echo esc_url( get_permalink( tinv_get_option( 'page', 'search' ) ) ); ?>">
	<div class="tinvwl-input-group tinvwl-no-full">
		<span
				class="screen-reader-text"><?php esc_html_e( 'Search for wishlists:', 'ti-woocommerce-wishlist-premium' ) ?></span>
		<input type="search" class="tinvwl-search-field form-control"
			   placeholder="<?php echo esc_attr( 'Search &hellip;', 'ti-woocommerce-wishlist-premium' ) ?>"
			   value="<?php echo esc_attr( $search ) ?>" name="tiws" required/>
		<span class="tinvwl-input-group-btn">
			<button type="submit" class="tinvwl-search-submit"><span
						class="screen-reader-text"><?php esc_html_e( 'Search', 'ti-woocommerce-wishlist-premium' ) ?></span></button>
		</span>
	</div>
</form>
