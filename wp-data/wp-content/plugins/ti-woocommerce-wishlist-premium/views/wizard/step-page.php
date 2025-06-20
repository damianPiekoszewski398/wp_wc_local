<?php
/**
 * The Template for displaying wizard page step.
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
		<h1><?php esc_html_e( 'Page Setup', 'ti-woocommerce-wishlist-premium' ); ?></h1>
		<div class="tinvwl-desc">
			<?php esc_html_e( 'The following pages need to be selected so the “Wishlist” know where they are.', 'ti-woocommerce-wishlist-premium' ); ?>
			<br/>
			<?php esc_html_e( 'Choose existing pages or leave fields empty and they will be created automatically:', 'ti-woocommerce-wishlist-premium' ); ?>
		</div>
	</div>

	<div class="tinvwl-separator"></div>

	<div class="form-horizontal">
		<div class="form-group">
			<?php echo TInvWL_Form::_label( 'general_default_title', __( 'Default Wishlist Name', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'col-md-6 control-label' ) ); // WPCS: xss ok. ?>
			<div class="col-md-6">
				<?php echo TInvWL_Form::_text( 'general_default_title', $general_default_title_value, array(
						'required' => 'required',
						'class'    => 'form-control'
				) ); // WPCS: xss ok. ?>
			</div>
		</div>
	</div>

	<?php
	foreach (
			array(
					'wishlist' => __( 'My Wishlist', 'ti-woocommerce-wishlist-premium' ),
					'manage'   => __( 'Manage Wishlists', 'ti-woocommerce-wishlist-premium' ),
					'search'   => __( 'Search for Wishlist Results', 'ti-woocommerce-wishlist-premium' ),
			) as $key => $label
	) {
		TInvWL_View::view( 'step-page-field', array(
				'key'        => $key,
				'label'      => $label,
				'page_field' => $page_pages[ $key ],
		), 'wizard' );
	} ?>

	<div class="tinvwl-desc">
		<?php esc_html_e( 'You can also set additional (not necessarily) pages to show in the quick menu on wishlist page. Wishlist search and "all wishlists" page, where you can search and browse public wishlists created by website customers.', 'ti-woocommerce-wishlist-premium' ); ?>
	</div>

	<?php
	foreach (
			array(
					'public'  => __( 'All Public Wishlists (Recent)', 'ti-woocommerce-wishlist-premium' ),
					'searchp' => __( 'Search for Wishlist', 'ti-woocommerce-wishlist-premium' ),
					'create'  => __( 'Create Wishlist', 'ti-woocommerce-wishlist-premium' ),
			) as $key => $label
	) {
		TInvWL_View::view( 'step-page-field', array(
				'key'        => $key,
				'label'      => $label,
				'page_field' => $page_pages[ $key ],
		), 'wizard' );
	}
	?>

	<div class="tinvwl-separator"></div>

	<div class="tinvwl-desc">
		<?php esc_html_e( 'Once created, these pages can be managed from your admin dashboard on the Pages screen.', 'ti-woocommerce-wishlist-premium' ); ?>
		<br/>
		<?php esc_html_e( 'You can control which pages are shown on your website via  Appearance > Menus.', 'ti-woocommerce-wishlist-premium' ); ?>
	</div>

	<div class="tinvwl-nav tinv-wishlist-clearfix">
		<div class="tinvwl-next">
			<a class="tinvwl-skip"
			   href="<?php echo esc_url( add_query_arg( 'step', absint( filter_input( INPUT_GET, 'step' ) ) + 1, set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ) ); // @codingStandardsIgnoreLine WordPress.VIP.SuperGlobalInputUsage.AccessDetected ?>"><?php esc_html_e( 'Skip this step', 'ti-woocommerce-wishlist-premium' ); ?></a>
			<?php echo TInvWL_Form::_button_submit( 'nextstep', __( 'continue', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'tinvwl-btn red w-icon round' ) ); // WPCS: xss ok. ?>
		</div>
	</div>
</div>
