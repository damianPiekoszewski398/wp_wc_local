<?php
/**
 * Ask for Estimate actions button functional
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Ask for Estimate actions button functional
 */
class TInvWL_Public_Wishlist_Estimate {

	/**
	 * This class
	 *
	 * @var \TInvWL_Public_Wishlist_Estimate
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Public_Wishlist_Estimate
	 */
	public static function instance( $plugin_name = TINVWL_PREFIX, $version = TINVWL_VERSION ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $plugin_name, $version );
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 */
	function __construct( $plugin_name ) {
		$this->_name = $plugin_name;
		$this->define_hooks();
	}

	/**
	 * Defined shortcode and hooks
	 */
	function define_hooks() {
		add_action( 'wp_loaded', array( $this, 'action' ), 0 );
	}


	/**
	 * First run method
	 *
	 * @param array $wishlist Set from action.
	 *
	 * @return boolean
	 */
	public static function init( $wishlist ) {

		if ( ! tinv_get_option( 'estimate_button', 'allow' ) ) {
			return false;
		}

		if ( ! is_user_logged_in() && apply_filters( 'tinvwl_estimate_guest_disable', true ) && ! tinv_get_option( 'estimate_button', 'guests' ) ) {
			return false;
		}

		if ( get_current_user_id() !== $wishlist['author'] ) {
			return false;
		}
		$nonce = filter_input( INPUT_POST, 'tinvwl_estimate_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( $nonce ) {
			return false;
		}

		self::htmloutput( $wishlist );
	}

	/**
	 * Output function
	 *
	 * @param array $wishlist Set from action.
	 */
	public static function htmloutput( $wishlist ) {

		$data = array(
			'wishlist'           => $wishlist,
			'wishlist_id'        => $wishlist['ID'],
			'estimate_guests'    => tinv_get_option( 'estimate_button', 'guests' ),
			'estimate_note'      => tinv_get_option( 'estimate_button', 'notes' ),
			'estimate_note_text' => apply_filters( 'tinvwl_estimate_notes', tinv_get_option( 'estimate_button', 'text_notes' ) ),
		);
		tinv_wishlist_template( 'ti-wishlist-estimate.php', $data );
	}

	/**
	 * Action send email
	 *
	 * @return boolean
	 */
	public static function action() {

		$wishlist_id = filter_input( INPUT_POST, 'tinvwl_estimate', FILTER_VALIDATE_INT );

		if ( empty( $wishlist_id ) ) {
			return false;
		}
		$nonce = filter_input( INPUT_POST, 'tinvwl_estimate_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, "tinvwl_check_estimate_{$wishlist_id}" ) ) {
			return false;
		}

		$wishlist = tinv_wishlist_get( $wishlist_id );

		$note = '';
		if ( tinv_get_option( 'estimate_button', 'notes' ) ) {
			$note = tinv_get_option( 'estimate_button', 'notes' ) ? filter_input( INPUT_POST, 'estimate_note', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : '';
		}

		$name = '';
		if ( tinv_get_option( 'estimate_button', 'guests' ) ) {
			$name = tinv_get_option( 'estimate_button', 'guests' ) ? filter_input( INPUT_POST, 'estimate_full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : '';
		}

		$email = '';
		if ( tinv_get_option( 'estimate_button', 'guests' ) ) {
			$email = tinv_get_option( 'estimate_button', 'guests' ) ? filter_input( INPUT_POST, 'estimate_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : '';
		}

		if ( function_exists( 'WC' ) ) {
			WC()->mailer();
		}

		$estimate = new TInvWL_Public_Email_Estimate( TINVWL_PREFIX, TINVWL_VERSION );
		$result   = $estimate->trigger( $wishlist, $note, apply_filters( 'tinvwl_send_ask_for_estimate_args', array(
			'email' => $email,
			'name'  => $name
		) ) );

		if ( is_wp_error( $result ) ) {
			$data = array(
				'icon' => 'icon_big_times',
				'msg'  => sprintf( __( 'Ask for Estimate for Wishlist "%1$s" is not sent! %2$s', 'ti-woocommerce-wishlist-premium' ), $wishlist['title'], $result->get_error_message() ),
			);
		} else {
			$data = array(
				'icon' => 'icon_big_heart_check',
				'msg'  => sprintf( __( 'Ask for Estimate for Wishlist "%s" is successfully sent!', 'ti-woocommerce-wishlist-premium' ), $wishlist['title'] ),
			);
		}

		tinv_wishlist_template( 'ti-addedtowishlist-dialogbox.php', $data );
		die();
	}
}
