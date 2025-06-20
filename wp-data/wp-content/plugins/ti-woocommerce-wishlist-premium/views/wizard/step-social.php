<?php
/**
 * The Template for displaying wizard social step.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Wizard\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinwl-inner tinvwl-social">
	<div class="row">
		<div class="col-md-6">
			<div class="tinvwl-title-wrap">
				<h1><?php esc_html_e( 'Share', 'ti-woocommerce-wishlist-premium' ); ?></h1>
				<div class="tinvwl-separator"></div>
			</div>

			<div class="tinvwl-desc"><?php esc_html_e( 'Allow people to share wishlists by adding social share buttons to Wishlist page.', 'ti-woocommerce-wishlist-premium' ); ?></div>

			<div class="form-horizontal">
				<div class="form-group">
					<?php echo TInvWL_Form::_label( 'social_facebook', __( 'Show "Facebook" Button', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
					<div class="col-md-6">
						<?php echo TInvWL_Form::_checkboxonoff( 'social_facebook', $social_facebook_value ); // WPCS: xss ok. ?>
					</div>
				</div>
			</div>

			<div class="form-horizontal">
				<div class="form-group">
					<?php echo TInvWL_Form::_label( 'social_twitter', __( 'Show "Twitter" Button', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
					<div class="col-md-6">
						<?php echo TInvWL_Form::_checkboxonoff( 'social_twitter', $social_twitter_value ); // WPCS: xss ok. ?>
					</div>
				</div>
			</div>

			<div class="form-horizontal">
				<div class="form-group">
					<?php echo TInvWL_Form::_label( 'social_pinterest', __( 'Show "Pinterest" Button', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
					<div class="col-md-6">
						<?php echo TInvWL_Form::_checkboxonoff( 'social_pinterest', $social_pinterest_value ); // WPCS: xss ok. ?>
					</div>
				</div>
			</div>

			<div class="form-horizontal">
				<div class="form-group">
					<?php echo TInvWL_Form::_label( 'social_whatsapp', __( 'Show "WhatsApp" Button', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
					<div class="col-md-6">
						<?php echo TInvWL_Form::_checkboxonoff( 'social_whatsapp', $social_whatsapp_value ); // WPCS: xss ok. ?>
					</div>
				</div>
			</div>

			<div class="form-horizontal">
				<div class="form-group">
					<?php echo TInvWL_Form::_label( 'social_clipboard', __( 'Show "Clipboard" Button', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
					<div class="col-md-6">
						<?php echo TInvWL_Form::_checkboxonoff( 'social_clipboard', $social_clipboard_value ); // WPCS: xss ok. ?>
					</div>
				</div>
			</div>

			<div class="form-horizontal">
				<div class="form-group">
					<?php echo TInvWL_Form::_label( 'social_email', __( 'Show "Share by Email" Button', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
					<div class="col-md-6">
						<?php echo TInvWL_Form::_checkboxonoff( 'social_email', $social_email_value ); // WPCS: xss ok. ?>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="tinvwl-title-wrap">
				<h1><?php esc_html_e( 'Follow', 'ti-woocommerce-wishlist-premium' ); ?></h1>
				<div class="tinvwl-separator"></div>
			</div>

			<div class="tinvwl-desc">
				<?php esc_html_e( 'This option adds Follow button to Wishlist page and allows users to receive email notifications every time, when Wishlist owner makes changes in his Wishlist.', 'ti-woocommerce-wishlist-premium' ); ?>
			</div>

			<div class="form-group">
				<?php
				echo TInvWL_Form::_label( 'subscribe_allow', __( 'Allow customers to follow Wishlists?', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'control-label' ) ); // WPCS: xss ok.
				echo TInvWL_Form::_select( 'subscribe_allow', $subscribe_allow_value, array( 'class' => 'col-md-12' ), $subscribe_allow_options ); // WPCS: xss ok.
				?>
			</div>

			<div class="tinvwl-desc">
				<?php printf( __( 'You can choose what kind of notifications followers can receive by email in %s settings page.', 'ti-woocommerce-wishlist-premium' ), sprintf( '<a target="_blank" href="%s">%s</a>', esc_url( self::admin_url( 'general-settings' ) ), __( 'TI Wishlist > General', 'ti-woocommerce-wishlist-premium' ) ) ); // WPCS: xss ok. ?>
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
