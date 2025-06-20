<?php
/**
 * The Template for displaying dialog created wishlist.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-create.php.
 *
 * @version             2.0.0
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<?php if ( $is_form_create ) : ?>
	<div class="tinv-wishlist woocommerce tinv-create-list">
		<form class="tinv_wishlist_create tinv-wrapped-block" method="POST" autocomplete="off">
			<div class="tinvwl-input-group">
				<input id="tinv_wishlist_create_field" class="form-control" type="text" name="tinv-name-wishlist"
					   value=""
					   placeholder="<?php echo esc_attr( __( 'Name your list', 'ti-woocommerce-wishlist-premium' ) ); ?>">
				<span class="tinvwl-input-group-btn">
					<button id="tinv_wishlist_create_button" type="button" class="" name="tinvwl-action-create"
							value="create_wishlist"><?php esc_html_e( 'Create List', 'ti-woocommerce-wishlist-premium' ) ?></button>
				</span>
			</div>
			<div id="tinv_wishlist_create_error" class="tinvwl-error" style="display: none;"></div>
			<ul>
				<?php
				$first = true;
				foreach ( $privacy as $key => $name ) {
					?>
					<li><label><input type="radio" name="tinv-privacy-wishlist"
									  value="<?php echo esc_attr( $key ) ?>" <?php echo $first ? 'checked="checked"' : '' ?>>
							<b><?php echo esc_html( $name ) ?></b>
							- <?php echo esc_html( $privacy_description[ $key ] ) ?></label></li>
					<?php
					$first = false;
				}
				?>
			</ul>
			<?php wp_nonce_field( 'tinvwl_wishlistcreate', 'tinv_wishlist_create_nonce' ); ?>
		</form>
	</div>
<?php else : ?>
	<div class="tinv-create-list">
		<?php echo apply_filters( 'tinvwl_button_create_wishlist', sprintf( '<a href="javascript:void(0)" class="button tinvwl-button tinv-modal-btn tinvwl-create-wishlist-button" name="tinvwl-action">%s</a>', __( 'Create Wishlist', 'ti-woocommerce-wishlist-premium' ) ) ); // WPCS: xss ok. ?>
		<div class="tinv_wishlist_create_modal tinv-modal">
			<div class="tinv-overlay"></div>
			<div class="tinv-table">
				<div class="tinv-cell">
					<div class="tinv-modal-inner tinv_wishlist_create">
						<a class="tinv-close-modal" href="javascript:void(0)"><i class="ftinvwl ftinvwl-times"></i></a>
						<h2><?php esc_html_e( 'Create Wishlist', 'ti-woocommerce-wishlist-premium' ) ?></h2>
						<form method="POST" autocomplete="off">
							<div class="tinvwl-input-group">
								<input id="tinv_wishlist_create_field" class="form-control" type="text"
									   name="tinv-name-wishlist" value=""
									   placeholder="<?php echo esc_attr( __( 'Name your list', 'ti-woocommerce-wishlist-premium' ) ); ?>">
								<span class="tinvwl-input-group-btn">
									<button id="tinv_wishlist_create_button" type="button" class=""
											name="tinvwl-action-create"
											value="create_wishlist"><?php esc_html_e( 'Create List', 'ti-woocommerce-wishlist-premium' ) ?></button>
								</span>
							</div>
							<div id="tinv_wishlist_create_error" class="tinvwl-error" style="display: none;"></div>
							<ul>
								<?php
								$first = true;
								foreach ( $privacy as $key => $name ) {
									?>
									<li><label><input type="radio" name="tinv-privacy-wishlist"
													  value="<?php echo esc_attr( $key ) ?>" <?php echo $first ? 'checked="checked"' : '' ?>>
											<b><?php echo esc_html( $name ) ?></b>
											- <?php echo esc_html( $privacy_description[ $key ] ) ?></label></li>
									<?php
									$first = false;
								}
								?>
							</ul>
							<?php wp_nonce_field( 'tinvwl_wishlistcreate', 'tinv_wishlist_create_nonce' ); ?>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
