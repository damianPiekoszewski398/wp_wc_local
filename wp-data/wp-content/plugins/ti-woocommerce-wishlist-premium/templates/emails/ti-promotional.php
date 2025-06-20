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

/**
 * Output the email header
 *
 * @hooked WC_Emails::email_header()
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
?>
	<p><?php echo $content; // WPCS: xss ok. ?></p>
<?php
/**
 * Output the email footer
 *
 * @hooked WC_Emails::email_footer()
 */
do_action( 'woocommerce_email_footer', $email );
