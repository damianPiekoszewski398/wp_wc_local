<?php
/**
 * The Template for displaying notification email for low stock of product.
 *
 * @version             2.0.14
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
