<?php
/**
 * The Template for displaying dialog for login in wishlist.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ti-wishlist-form-login.php.
 *
 * @version             2.3.3
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php if ( function_exists( 'wc_print_notices' ) && isset( WC()->session ) ) {
	wc_print_notices();
} ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<!-- <div class="col2-set" id="customer_login"><div class="col-1"> -->

<h2><?php esc_html_e( 'Login', 'ti-woocommerce-wishlist-premium' ); ?></h2>

<form method="post" class="login">

	<?php do_action( 'woocommerce_login_form_start' ); ?>

	<div class="tinvwl-input-group">

		<p class="form-row form-row-first">
			<input type="text" placeholder="<?php esc_html_e( 'Username', 'ti-woocommerce-wishlist-premium' ); ?>"
				   class="input-text" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) {
				echo esc_attr( $_POST['username'] );
			} // @codingStandardsIgnoreLine WordPress.VIP.SuperGlobalInputUsage.AccessDetected ?>"/>
			<span class="tinvwl-icon"><i class="ftinvwl ftinvwl-user"></i></span>
		</p>
		<p class="form-row form-row-last">
			<input placeholder="<?php esc_html_e( 'Password', 'ti-woocommerce-wishlist-premium' ); ?>"
				   class="input-text" type="password" name="password" id="password"/>
			<span class="tinvwl-icon"><i class="ftinvwl ftinvwl-lock"></i></span>
		</p>

		<span class="tinvwl-input-group-btn">
					<?php wp_nonce_field( 'woocommerce-login' ); ?>
					<input type="submit" class="" name="login"
						   value="<?php echo esc_attr_e( 'Login', 'ti-woocommerce-wishlist-premium' ); ?>"/>
				</span>

	</div>

	<?php do_action( 'woocommerce_login_form' ); ?>

	<div class="tinv-wishlist-clear"></div>

	<p class="form-row tinv-rememberme">
		<label for="rememberme" class="inline">
			<input name="rememberme" type="checkbox" id="rememberme" class="input-checkbox"
				   value="forever"/> <?php esc_html_e( 'Remember me', 'ti-woocommerce-wishlist-premium' ); ?>
		</label>
	</p>

	<p class="lost_password">
		<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot your password?', 'ti-woocommerce-wishlist-premium' ); ?></a>
	</p>

	<div class="tinv-wishlist-clear"></div>

	<?php do_action( 'woocommerce_login_form_end' ); ?>

</form>
<!--
	</div>

	<div class="col-2">

		<h2><?php esc_html_e( 'Register', 'ti-woocommerce-wishlist-premium' ); ?></h2>

		<form method="post" class="register">

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<p class="form-row form-row-wide">
					<label for="reg_username"><?php esc_html_e( 'Username', 'ti-woocommerce-wishlist-premium' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) {
	echo esc_attr( $_POST['username'] );
} // @codingStandardsIgnoreLine WordPress.VIP.SuperGlobalInputUsage.AccessDetected  ?>" />
                </p>

			<?php endif; ?>

			<p class="form-row form-row-wide">
				<label for="reg_email"><?php esc_html_e( 'Email address', 'ti-woocommerce-wishlist-premium' ); ?> <span class="required">*</span></label>
				<input type="email" class="input-text" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) {
	echo esc_attr( $_POST['email'] );
} // @codingStandardsIgnoreLine WordPress.VIP.SuperGlobalInputUsage.AccessDetected  ?>" />
            </p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<p class="form-row form-row-wide">
					<label for="reg_password"><?php esc_html_e( 'Password', 'ti-woocommerce-wishlist-premium' ); ?> <span class="required">*</span></label>
					<input type="password" class="input-text" name="password" id="reg_password" />
				</p>

			<?php endif; ?>

			<div style="<?php echo( ( is_rtl() ) ? 'right' : 'left' ); // WPCS: xss ok. ?>: -999em; position: absolute;"><label for="trap"><?php esc_html_e( 'Anti-spam', 'ti-woocommerce-wishlist-premium' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

			<?php do_action( 'woocommerce_register_form' ); ?>
			<?php do_action( 'register_form' ); ?>

			<p class="form-row">
				<?php wp_nonce_field( 'woocommerce-register' ); ?>
				<input type="submit" class="button" name="register" value="<?php echo esc_attr( 'Register', 'ti-woocommerce-wishlist-premium' ); ?>" />
			</p>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	</div>

</div>
-->

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
