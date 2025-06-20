<?php
/**
 * The Template for displaying dialog box for follow wishlists this plugin.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-subscribe.php.
 *
 * @version             2.3.3
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<?php if ( $follow ) { ?>
	<div class="tinvwl-subscribe-wrap">
		<button type="button" class="button tinvwl-button tinv-modal-btn" name="tinvwl-action"><i
				class="ftinvwl ftinvwl-star"></i><?php esc_html_e( 'Follow Wishlist', 'ti-woocommerce-wishlist-premium' ); ?>
		</button>
		<div class="tinv-modal tinvwl-subscribe">
			<div class="tinv-overlay"></div>
			<div class="tinv-table">
				<div class="tinv-cell">
					<div class="tinv-modal-inner">
						<i class="icon_big_heart_subscribe"></i>
						<?php if ( is_user_logged_in() ) { ?>
							<form method="POST" class="tinvwl_subscribe_form">
								<div
									class="tinvwl-txt"><?php esc_html_e( 'Receive email notifications when:', 'ti-woocommerce-wishlist-premium' ) ?></div>
								<ul>
									<?php foreach ( $subscribe_events as $key => $name ) { ?>
										<li><label for="tinvwl_subscribes_<?php echo esc_attr( $key ); ?>"><input
													type="checkbox"
													id="tinvwl_subscribes_<?php echo esc_attr( $key ); ?>"
													class="input-checkbox"
													name="tinvwl_subscribes[]"
													value="<?php echo esc_attr( $key ); ?>"/><?php echo esc_html( $name ); ?>
											</label></li>
									<?php } ?>
								</ul>
								<?php if ( ! is_user_logged_in() ) { ?>
									<label for="tinvwl_subscribe_email">Email:
										<input type="email" name="tinvwl_subscribe_email" value=""/>
									</label>
								<?php } ?>
								<div id="tinv_wishlist_follow_error" class="tinvwl-error"
									 style="display: none;"><?php esc_html_e( 'At least one condition should be selected to follow wishlist.', 'ti-woocommerce-wishlist-premium' ); ?></div>
								<div class="tinvwl-buttons-group tinv-wishlist-clear">
									<button class="button tinvwl-button tinvwl-select-all"><i
											class="ftinvwl ftinvwl-check-square-o"></i><?php esc_html_e( 'Select all', 'ti-woocommerce-wishlist-premium' ); ?>
									</button>
									<button class="button tinvwl-button tinvwl-select-none"><i
											class="ftinvwl ftinvwl-square-o"></i><?php esc_html_e( 'Select none', 'ti-woocommerce-wishlist-premium' ); ?>
									</button>
								</div>
								<button type="submit" class="" name="tinvwl_subscribe"
										value="<?php echo esc_attr( $wishlist_id ); ?>"><?php esc_html_e( 'Follow this Wishlist', 'ti-woocommerce-wishlist-premium' ) ?></button>
								<?php wp_nonce_field( 'tinvwl_check_subscribe_' . $wishlist_id, 'tinvwl_subscribe_nonce' ); ?>
							</form>
						<?php } else { ?>
							<div class="tinv-txt">
								<?php esc_html_e( 'Please login to follow this wishlist', 'ti-woocommerce-wishlist-premium' );
								echo sprintf( ' <a href="%s" class="button tinvwl-button">%s</a>', apply_filters( 'tinvwl_addtowishlist_login_page', esc_url( add_query_arg( 'tinvwl_redirect', tinv_url_wishlist(), get_permalink( wc_get_page_id( 'myaccount' ) ) ) ), array( 'redirect' => get_permalink() ) ), __( 'Login', 'ti-woocommerce-wishlist-premium' ) ); // WPCS: xss ok. ?>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } else { ?>
	<div class="tinvwl-subscribe-wrap">
		<form method="POST">
			<button type="submit" class="button tinvwl-button" name="tinvwl_unsubscribe"
					value="<?php echo esc_attr( $wishlist_id ); ?>"><i
					class="ftinvwl ftinvwl-star"></i><?php esc_html_e( 'Unfollow Wishlist', 'ti-woocommerce-wishlist-premium' ); ?>
			</button>
			<?php wp_nonce_field( 'tinvwl_check_subscribe_' . $wishlist_id, 'tinvwl_subscribe_nonce' ); ?>
		</form>
	</div>
<?php } // End if(). ?>
