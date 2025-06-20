<?php
/**
 * The Template for displaying wizard button step.
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
		<h1><?php esc_html_e( 'Button options', 'ti-woocommerce-wishlist-premium' ); ?></h1>
		<div class="tinvwl-separator"></div>
	</div>

	<div class="tinvwl-desc">
		<?php esc_html_e( 'Choose where to place “Add to Wishlist” button on the product page: before or after “Add to Cart” button.', 'ti-woocommerce-wishlist-premium' ); ?>
		<br/>
		<?php printf( __( 'And set button text. You can add an icon, change button appearance and other settings in %s.', 'ti-woocommerce-wishlist-premium' ), sprintf( '<a target="_blank" href="%s">%s</a>', esc_url( self::admin_url( 'button-settings' ) ), __( 'TI Wishlist > Button Options', 'ti-woocommerce-wishlist-premium' ) ) ); // WPCS: xss ok. ?>
	</div>

	<div class="form-horizontal">
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'add_to_wishlist_position', __( 'Button position', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_select( 'add_to_wishlist_position', $add_to_wishlist_position_value, array( 'class' => 'form-control' ), $add_to_wishlist_position_options ); // WPCS: xss ok. ?>
			</div>
		</div>
	</div>

	<div class="form-horizontal">
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'add_to_wishlist_text', __( '"Add to Wishlist" Text', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_text( 'add_to_wishlist_text', $add_to_wishlist_text_value, array( 'class' => 'form-control' ) ); // WPCS: xss ok. ?>
			</div>
		</div>
	</div>

	<div class="tinvwl-separator"></div>

	<div class="tinvwl-desc">
		<?php printf( __( 'You can also show “Add to Wishlist” button in Product listing. More options in %s.', 'ti-woocommerce-wishlist-premium' ), sprintf( '<a target="_blank" href="%s">%s</a>', esc_url( self::admin_url( 'button-settings' ) ), __( 'TI Wishlist > Button Options', 'ti-woocommerce-wishlist-premium' ) ) ); // WPCS: xss ok. ?>
	</div>

	<div class="form-horizontal">
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'add_to_wishlist_catalog_show_in_loop', __( 'Show in Product listing', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_checkboxonoff( 'add_to_wishlist_catalog_show_in_loop', $add_to_wishlist_catalog_show_in_loop_value, array( 'tiwl-show' => '.tiwl-buttoncat-button' ) ); // WPCS: xss ok. ?>
			</div>
		</div>
	</div>

	<div class="form-horizontal tiwl-buttoncat-button">
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'add_to_wishlist_catalog_position', __( 'Button position in Product listing', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_select( 'add_to_wishlist_catalog_position', $add_to_wishlist_catalog_position_value, array( 'class' => 'form-control' ), $add_to_wishlist_catalog_position_options ); // WPCS: xss ok. ?>
			</div>
		</div>
	</div>

	<div class="form-horizontal tiwl-buttoncat-button">
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'add_to_wishlist_catalog_text', __( '"Add to Wishlist" Text in Product listing', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_text( 'add_to_wishlist_catalog_text', $add_to_wishlist_catalog_text_value, array( 'class' => 'form-control' ) ); // WPCS: xss ok. ?>
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
