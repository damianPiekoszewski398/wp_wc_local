<?php
/**
 * The Template for displaying wizard finish step.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Wizard\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinwl-inner tinwl-finish">
	<h2 class="tinvwl-sub-title"><?php esc_html_e( 'Congratulations', 'ti-woocommerce-wishlist-premium' ); ?></h2>
	<h1 class="tinvwl-title"><?php esc_html_e( 'Your Wishlist is ready!', 'ti-woocommerce-wishlist-premium' ); ?></h1>
	<div class="tinvwl-desc">
		<?php printf( __( 'You have set basic Wishlist settings. If you want to make more in-depth plugin setup, such as: styles, table settings, emails setup and much more, you can find them in %s.', 'ti-woocommerce-wishlist-premium' ), sprintf( '<a target="_blank" href="%s">%s</a>', esc_url( self::admin_url( 'general-settings' ) ), __( 'TI Wishlist Settings', 'ti-woocommerce-wishlist-premium' ) ) ); // WPCS: xss ok. ?>
		<br/>
		<?php printf( __( 'Details about TI WooCommerce Wishlist options can be found in our %s.', 'ti-woocommerce-wishlist-premium' ), sprintf( '<a target="_blank" href="%s">%s</a>', 'https://templateinvaders.com/documentation/ti-woocommerce-wishlist/?utm_source=wishlist_plugin_premium&utm_campaign=online_documentation&utm_medium=wizard', __( 'Online Documentation', 'ti-woocommerce-wishlist-premium' ) ) ); // WPCS: xss ok.
		?><br/><br/>
		<?php esc_html_e( 'Don’t forget to activate your license code in order to receive support and automatic updates directly to your dashboard.', 'ti-woocommerce-wishlist-premium' ); ?>
	</div>
	<a class="tinvwl-btn grey w-icon xl-icon round"
	   href="<?php echo 'https://templateinvaders.com/documentation/ti-woocommerce-wishlist/?utm_source=wishlist_plugin_premium&utm_campaign=documentation&utm_medium=wizard';
	   ?>"><i class="ftinvwl ftinvwl-graduation-cap"></i><?php esc_html_e( 'Documentation', 'ti-woocommerce-wishlist-premium' ); ?>
	</a>
	<a class="tinvwl-btn red w-icon xl-icon round" href="<?php echo esc_url( self::admin_url( 'license' ) ); ?>"><i
				class="ftinvwl ftinvwl-key"></i><?php esc_html_e( 'Activate License', 'ti-woocommerce-wishlist-premium' ); ?>
	</a>
	<a class="tinvwl-btn grey w-icon xl-icon round"
	   href="<?php echo esc_url( self::admin_url( 'general-settings' ) ); ?>"><i
				class="ftinvwl ftinvwl-wrench"></i><?php esc_html_e( 'Wishlist Settings', 'ti-woocommerce-wishlist-premium' ); ?>
	</a>
	<div class="tinv-wishlist-clear"></div>
</div>
