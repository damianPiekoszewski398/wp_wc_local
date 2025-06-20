<?php
/**
 * The Template for promotional email this plugin.
 *
 * @version             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '= ' . $email_heading . " =\n\n"; // WPCS: xss ok.
echo $content; // WPCS: xss ok.
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ); // WPCS: xss ok.
