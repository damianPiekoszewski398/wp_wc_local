<?php
/**
 * The Template for displaying empty search wishlists.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-searchform-empty.php.
 *
 * @version             1.0.0
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<p><?php esc_html_e( 'Sorry, but nothing matched your search. Please try again with some different keywords.', 'ti-woocommerce-wishlist-premium' ); ?></p>
