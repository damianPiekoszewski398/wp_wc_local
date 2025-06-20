<?php
/**
 * Login notification actions button functional
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Login notification actions button functional
 */
class TInvWL_Public_Wishlist_Login {

	/**
	 * First run method
	 *
	 * @return boolean
	 */
	public static function init() {
		if ( is_user_logged_in() || ! tinv_get_option( 'general', 'login_notice' ) ) {
			return false;
		}
		self::htmloutput();
	}

	/**
	 * Output function
	 *
	 * @param string $login_link_behavior Type login link behavior.
	 */
	public static function htmloutput( $login_link_behavior = null ) {
		$wishlist_login = '';
		$is_form        = false;
		if ( empty( $login_link_behavior ) ) {
			$login_link_behavior = tinv_get_option( 'general', 'login_link_behavior' );
		}
		switch ( $login_link_behavior ) {
			case 'link':
				$wishlist_login = self::htmloutput_link();
				break;
			case 'form':
				$wishlist_login = self::htmloutput_form();
				$is_form        = true;
				break;
		}

		$data = array(
			'wishlist_text_login_anchor' => apply_filters( 'tinvwl_login_link_text', tinv_get_option( 'general', 'text_login_anchor' ) ),
			'wishlist_text_login_link'   => apply_filters( 'tinvwl_login_link', tinv_get_option( 'general', 'text_login_link' ) ),
			'wishlist_login'             => $wishlist_login,
			'wishlist_is_form'           => $is_form,
		);
		tinv_wishlist_template( 'ti-wishlist-login.php', $data );
	}

	/**
	 * Output link
	 *
	 * @return string
	 */
	public static function htmloutput_link() {
		$url = get_permalink( wc_get_page_id( 'myaccount' ) );
		$url .= get_option( 'permalink_structure' ) ? '?' : '&';
		$url .= 'tinvwl_redirect=' . esc_url( get_permalink() );

		return sprintf( '<a href="%s">%s</a>', apply_filters( 'tinvwl_addtowishlist_login_page', esc_url( $url ), array( 'redirect' => get_permalink() ) ), apply_filters( 'tinvwl_login_link', tinv_get_option( 'general', 'text_login_link' ) ) );
	}

	/**
	 * Output form
	 *
	 * @return string
	 */
	public static function htmloutput_form() {
		add_action( 'woocommerce_login_form_end', array( 'TInvWL_Public_Wishlist_Login', 'htmloutput_form_redirect' ) );
		add_action( 'woocommerce_register_form_end', array(
			'TInvWL_Public_Wishlist_Login',
			'htmloutput_form_redirect',
		) );

		return tinv_wishlist_template_html( 'ti-wishlist-form-login.php' );
	}

	/**
	 * Create redirect field
	 */
	public static function htmloutput_form_redirect() {
		TInvWL_Form::text( array(
			'type' => 'hidden',
			'name' => 'tinvwl_redirect',
		), get_permalink( apply_filters( 'wpml_object_id', tinv_get_option( 'page', 'wishlist' ), 'page', true ) ) );
	}
}
