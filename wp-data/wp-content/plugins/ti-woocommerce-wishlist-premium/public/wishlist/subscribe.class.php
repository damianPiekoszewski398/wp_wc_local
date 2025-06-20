<?php
/**
 * Subscribe actions buttons functional
 *
 * @since             1.0.0
 * @package           TInvWishlist\Subscribers
 * @subpackage        Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Subscribe actions buttons functional
 */
class TInvWL_Public_Wishlist_Subscribe {

	/**
	 * First run method
	 *
	 * @param array $wishlist Set from action.
	 *
	 * @return boolean
	 */
	public static function init( $wishlist ) {
		if ( ! tinv_get_option( 'subscribe', 'allow' ) ) {
			return false;
		}
		if ( get_current_user_id() === $wishlist['author'] ) {
			return false;
		}
		$nonce = filter_input( INPUT_POST, 'tinvwl_subscribe_nonce' );
		if ( $nonce && wp_verify_nonce( $nonce, "tinvwl_check_subscribe_{$wishlist['ID']}" ) ) {
			self::save( $wishlist );
		}

		self::htmloutput( $wishlist );
	}

	/**
	 * Output function
	 *
	 * @param array $wishlist Set from action.
	 */
	public static function htmloutput( $wishlist ) {
		$wls          = new TInvWL_Subscribers( $wishlist );
		$events       = TInvWL_Subscribers::event_lists();
		$allow_events = tinv_get_option( 'subscribe', 'event' );

		if ( is_null( $allow_events ) ) {
			TInvWL_Admin_Settings_General::subscribe_run();
			$allow_events = tinv_get_option( 'subscribe', 'event' );
		}

		foreach ( array_keys( $events ) as $key ) {
			if ( ! in_array( $key, $allow_events ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
				unset( $events[ $key ] );
			}
		}
		$mask = $wls->get_current_mask();
		$data = array(
			'wishlist'         => $wishlist,
			'wishlist_id'      => $wishlist['ID'],
			'subscribe_events' => $events,
			'follow'           => empty( $mask ),
		);
		tinv_wishlist_template( 'ti-wishlist-subscribe.php', $data );
	}

	/**
	 * Save function
	 *
	 * @param array $wishlist Set from action.
	 */
	public static function save( $wishlist ) {
		$var = filter_input_array( INPUT_POST, array(
			'tinvwl_subscribe_email' => FILTER_VALIDATE_EMAIL,
			'tinvwl_subscribe'       => FILTER_VALIDATE_INT,
			'tinvwl_unsubscribe'     => FILTER_VALIDATE_INT,
			'tinvwl_subscribes'      => array(
				'filter' => FILTER_VALIDATE_INT,
				'flags'  => FILTER_FORCE_ARRAY,
			),
		) );

		$user_email   = $var['tinvwl_subscribe_email'];
		$mask         = array_sum( (array) $var['tinvwl_subscribes'] );
		$wishlist_id  = $var['tinvwl_subscribe'];
		$_wishlist_id = $var['tinvwl_unsubscribe'];

		$wls = new TInvWL_Subscribers( $wishlist );
		if ( $wishlist_id === $wishlist['ID'] ) {
			if ( empty( $mask ) ) {
				return;
			}
			if ( $wls->add( array(
				'user_email' => $user_email,
				'user_type'  => $mask,
			) ) ) {
				wc_add_notice( sprintf( __( 'You are now following wishlist "%s"!', 'ti-woocommerce-wishlist-premium' ), $wishlist['title'] ) );
				echo '<script type="text/javascript">window.location = window.location.href;</script>';
			}
		} elseif ( $_wishlist_id === $wishlist['ID'] ) {
			if ( $wls->delete() ) {
				wc_add_notice( sprintf( __( 'You are now unfollowing wishlist "%s"!', 'ti-woocommerce-wishlist-premium' ), $wishlist['title'] ) );
				echo '<script type="text/javascript">window.location = window.location.href;</script>';
			}
		}
	}
}
