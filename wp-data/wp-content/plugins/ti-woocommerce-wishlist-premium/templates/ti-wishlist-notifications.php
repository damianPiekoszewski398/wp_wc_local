<?php
/**
 * The Template for displaying dialog for wishlist notifications.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-notifications.php.
 *
 * @version             2.3.3
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<script>
	<?php
	global $notification_processed, $notification_show;

	$global_notifications = tinv_get_option( 'global_notifications', 'enable_notifications' );

	if ( ! $notification_processed ) {
		$user_id                = get_current_user_id();
		$notification_processed = get_user_meta( $user_id, '_tinvwl_notifications_processed', true );
		$notification_show      = $notification_processed ? true : false;
	}

	if ( ! $notification_processed ) {
		$notification_processed = update_user_meta( $user_id, '_tinvwl_notifications_processed', '1' );
	}?>
	jQuery(document).ready(function () {
		tinvwl_add_to_wishlist.notifications = <?php echo $notification_show ? 'true' : 'false';?>
	});
</script>

<?php echo apply_filters( 'tinvwl_button_notifications_wishlist', sprintf( '<a href="javascript:void(0)" class="button tinvwl-button tinv-modal-btn" name="tinvwl-action">%s</a>', __( 'Notifications', 'ti-woocommerce-wishlist-premium' ) ) ); // WPCS: xss ok. ?>
<div class="tinv_wishlist_notifications_modal tinv-modal">
	<div class="tinv-overlay"></div>
	<div class="tinv-table">
		<div class="tinv-cell">
			<div class="tinv-modal-inner tinv_wishlist_notifications">
				<a class="tinv-close-modal" href="javascript:void(0)"><i class="ftinvwl ftinvwl-times"></i></a>
				<h2><?php esc_html_e( 'Notification settings', 'ti-woocommerce-wishlist-premium' ) ?></h2>
				<form method="POST" autocomplete="off">
					<div
						class="tinvwl-txt"><?php esc_html_e( 'Receive email notifications when:', 'ti-woocommerce-wishlist-premium' ) ?></div>
					<ul>
						<?php foreach ( $notifications as $key => $data ) { ?>
							<li><label for="tinvwl_notifications_<?php echo esc_attr( $key ); ?>"><input
										type="checkbox"
										id="tinvwl_notifications_<?php echo esc_attr( $key ); ?>"
										class="input-checkbox"
										name="tinvwl_notifications[<?php echo esc_attr( $key ); ?>]"
										value="subscribed" <?php checked( ( 'subscribed' === $data['value'] ) || ( ! $data['value'] && $global_notifications ) ) ?>/><?php echo esc_html( $data['description'] ); ?>
								</label></li>
						<?php } ?>
					</ul>
					<div class="tinvwl-buttons-group tinv-wishlist-clear">
						<button type="submit" class="button tinvwl-button" name="tinvwl-action-notifications"
								value="notifications_update"><?php esc_html_e( 'Update settings', 'ti-woocommerce-wishlist-premium' ) ?></button>
					</div>
					<?php wp_nonce_field( 'tinvwl_wishlistnotifications', 'tinv_wishlist_notifications_nonce' ); ?>
				</form>
			</div>
		</div>
	</div>
</div>


