<?php
/**
 * The Template for displaying dialog for estimate wishlist this plugin.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-estimate.php.
 *
 * @version             1.23.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinvwl-estimate-wrap">
	<?php if ($estimate_note || ( ! is_user_logged_in() && $estimate_guests)) : ?>
		<a href="javascript:void(0)" class="button tinvwl-button tinv-modal-btn"><i
					class="ftinvwl ftinvwl-email"></i><?php esc_html_e('Ask For An Estimate', 'ti-woocommerce-wishlist-premium'); ?>
		</a>
		<div class="estimate-dialogbox tinv-modal">
			<div class="tinv-overlay"></div>
			<div class="tinv-table">
				<div class="tinv-cell">
					<div class="tinv-modal-inner">
						<a class="tinv-close-modal" href="javascript:void(0)"><i class="ftinvwl ftinvwl-times"></i></a>
						<form method="POST">
							<?php if ( ! is_user_logged_in() && $estimate_guests): ?>
								<h2><?php _e('Contact details', 'ti-woocommerce-wishlist-premium'); ?></h2>
								<input type="text"
									   placeholder="<?php esc_html_e('Your Name (required)', 'ti-woocommerce-wishlist-premium') ?>"
									   name="estimate_full_name" class="tinv-text-input tinv-first" required
									   value=""/>
								<input type="email"
									   placeholder="<?php esc_html_e('Your Email (required)', 'ti-woocommerce-wishlist-premium') ?>"
									   name="estimate_email" class="tinv-text-input" required
									   value=""/>
							<?php endif; ?>
							<?php if ($estimate_note): ?>
								<h2><?php echo esc_html($estimate_note_text); ?></h2>
								<textarea
										placeholder="<?php esc_html_e('Additional notes...', 'ti-woocommerce-wishlist-premium') ?>"
										name="estimate_note"></textarea>
							<?php endif; ?>
							<button type="submit" class="" name="tinvwl_estimate"
									value="<?php echo esc_attr($wishlist_id); ?>"><?php esc_html_e('Send a Request', 'ti-woocommerce-wishlist-premium') ?></button>
							<?php wp_nonce_field('tinvwl_check_estimate_' . $wishlist_id, 'tinvwl_estimate_nonce'); ?>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php else : ?>
		<form method="POST">
			<button type="submit" class="button tinvwl-button tinv-modal-btn" name="tinvwl_estimate"
					value="<?php echo esc_attr($wishlist_id); ?>"><i
						class="ftinvwl ftinvwl-email"></i><?php esc_html_e('Ask For An Estimate', 'ti-woocommerce-wishlist-premium'); ?>
			</button>
			<?php wp_nonce_field('tinvwl_check_estimate_' . $wishlist_id, 'tinvwl_estimate_nonce'); ?>
		</form>
	<?php endif; ?>
</div>
