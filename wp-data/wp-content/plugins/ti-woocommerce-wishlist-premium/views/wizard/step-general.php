<?php
/**
 * The Template for displaying wizard general step.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Wizard\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinwl-inner">
	<div class="tinvwl-title-wrap">
		<h1><?php esc_html_e( 'General Settings', 'ti-woocommerce-wishlist-premium' ); ?></h1>
		<div class="tinvwl-separator"></div>
	</div>

	<div class="form-horizontal">
		<div class="tinvwl-desc"><?php esc_html_e( 'You can allow customers to create Wishlists so they can create an event specific wishlists i.e. “Christmas Wishlist” or “Birthday gifts Wishlist” and so on. Or you can limit customers to use only one Wishlist.', 'ti-woocommerce-wishlist-premium' ); ?></div>
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'general_multi', __( 'How many wishlists your customers can have?', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_select( 'general_multi', $general_multi_value, array( 'class' => 'form-control' ), $general_multi_options ); // WPCS: xss ok. ?>
			</div>
		</div>
	</div>

	<div class="tinvwl-separator"></div>

	<div class="tinvwl-desc"><?php esc_html_e( 'You can allow guests to add products to Wishlist to keep them on your website and force to create an account. Or you can make Wishlist as privileges for registered users.', 'ti-woocommerce-wishlist-premium' ); ?></div>

	<div class="form-horizontal">
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'general_guests', __( 'Who can use wishlist?', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_select( 'general_guests', $general_guests_value, array( 'class' => 'form-control' ), $general_guests_options ); // WPCS: xss ok. ?>
			</div>
		</div>
	</div>

	<div class="tinvwl-separator"></div>

	<div class="tinvwl-nav tinv-wishlist-clearfix">
		<div class="tinvwl-next">
			<a class="tinvwl-skip"
			   href="<?php echo esc_url( add_query_arg( 'step', absint( filter_input( INPUT_GET, 'step' ) ) + 1, set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ) ); // @codingStandardsIgnoreLine WordPress.VIP.SuperGlobalInputUsage.AccessDetected ?>"><?php esc_html_e( 'Skip this step', 'ti-woocommerce-wishlist-premium' ); ?></a>
			<?php echo TInvWL_Form::_button_submit( 'nextstep', __( 'continue', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'tinvwl-btn red w-icon round' ) ); // WPCS: xss ok. ?>
		</div>
	</div>
</div>
