<?php
/**
 * The Template for displaying dialog for login in wishlist this plugin.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-login.php.
 *
 * @version             1.9.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<?php if ( $wishlist_is_form ) : ?>
	<div class="tinv-wishlist woocommerce tinvwl-login-wrap">
		<div class="tinv-login tinv-wrapped-block">
			<div class="tinv-txt">
				<i class="ftinvwl ftinvwl-key"></i>
				<?php echo $wishlist_text_login_anchor; // WPCS: xss ok. ?>
				<a href="#" class="showlogin"><?php echo $wishlist_text_login_link; // WPCS: xss ok. ?></a>
			</div>
			<?php echo $wishlist_login; // WPCS: xss ok. ?>
		</div>
	</div>
<?php else : ?>
	<div class="tinv-wishlist woocommerce tinvwl-login-wrap">
		<div class="tinv-login tinv-wrapped-block">
			<div class="tinv-txt">
				<i class="ftinvwl ftinvwl-key"></i>
				<?php echo $wishlist_text_login_anchor; // WPCS: xss ok. ?>
				<?php echo $wishlist_login; // WPCS: xss ok. ?>
			</div>
		</div>
	</div>
<?php endif; ?>
