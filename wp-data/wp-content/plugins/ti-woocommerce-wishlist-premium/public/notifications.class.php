<?php
/**
 * Wishlist notifications shortcode
 *
 * @since             1.10.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Wishlist notifications shortcode
 */
class TInvWL_Public_Notifications {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $_name;

	/**
	 * This class
	 *
	 * @var \TInvWL_Public_Notifications
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Public_Notifications
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
	 * Output page
	 *
	 *
	 * @return boolean
	 */
	public function htmloutput() {

		$meta = get_user_meta( get_current_user_id(), '_tinvwl_notifications', true );

		$data = array( 'notifications' => array() );

		if ( apply_filters( 'tinvwl_notifications_option_promotional', tinv_get_option( 'promotional_email', 'enabled' ) ) ) {
			$data['notifications']['promotional'] = array(
				'value'       => ( isset( $meta['promotional'] ) && ! empty( $meta['promotional'] ) ) ? $meta['promotional'] : apply_filters( 'tinvwl_notifications_default_value', false ),
				'description' => __( 'Product promoted', 'ti-woocommerce-wishlist-premium' ),
			);
		}

		if ( tinv_get_option( 'notification_price_email', 'enabled' ) ) {
			$data['notifications']['price'] = array(
				'value'       => ( isset( $meta['price'] ) && ! empty( $meta['price'] ) ) ? $meta['price'] : apply_filters( 'tinvwl_notifications_default_value', false ),
				'description' => __( 'Product price changed', 'ti-woocommerce-wishlist-premium' ),
			);
		}

		if ( tinv_get_option( 'notification_stock_email', 'enabled' ) ) {
			$data['notifications']['stock'] = array(
				'value'       => ( isset( $meta['stock'] ) && ! empty( $meta['stock'] ) ) ? $meta['stock'] : apply_filters( 'tinvwl_notifications_default_value', false ),
				'description' => __( 'Product stock status changed', 'ti-woocommerce-wishlist-premium' ),
			);
		}

		if ( tinv_get_option( 'notification_low_stock_email', 'enabled' ) ) {
			$data['notifications']['low_stock'] = array(
				'value'       => ( isset( $meta['low_stock'] ) && ! empty( $meta['low_stock'] ) ) ? $meta['low_stock'] : apply_filters( 'tinvwl_notifications_default_value', false ),
				'description' => __( 'Product low stock', 'ti-woocommerce-wishlist-premium' ),
			);
		}

		tinv_wishlist_template( 'ti-wishlist-notifications.php', apply_filters( 'tinvwl_user_notification_settings', $data, $meta, $this ) );
	}

	/**
	 * Action create wishlist
	 *
	 * @return boolean
	 */
	public function action() {
		$action = filter_input( INPUT_POST, 'tinvwl-action-notifications', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( empty( $action ) ) {
			return false;
		}
		$nonce = filter_input( INPUT_POST, 'tinv_wishlist_notifications_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'tinvwl_wishlistnotifications' ) ) {
			return false;
		}

		$post = filter_input_array( INPUT_POST, array(
			'tinvwl_notifications' => array(
				'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
				'flags'  => FILTER_FORCE_ARRAY,
			),
		) );

		$meta_default = self::get_default_meta();

		$meta = array();

		foreach ( $meta_default as $key => $value ) {
			$meta[ $key ] = ( isset( $post['tinvwl_notifications'][ $key ] ) && ! empty( $post['tinvwl_notifications'][ $key ] ) ) ? $post['tinvwl_notifications'][ $key ] : $value;
		}

		self::save( $meta );

		$data = array(
			'icon' => 'icon_big_heart_check',
			'msg'  => __( 'Notifications settings have been successfully updated', 'ti-woocommerce-wishlist-premium' ),
		);

		tinv_wishlist_template( 'ti-addedtowishlist-dialogbox.php', $data );
		die();

	}

	/**
	 * @param $meta
	 */
	public static function save( $meta ) {
		update_user_meta( get_current_user_id(), '_tinvwl_notifications', $meta );
	}

	/**
	 * @return array
	 */
	public static function get_default_meta() {

		$meta = array(
			'promotional' => apply_filters( 'tinvwl_notifications_default_value', 'unsubscribed' ),
			// subscribed or unsubscribed
			'price'       => apply_filters( 'tinvwl_notifications_default_value', 'unsubscribed' ),
			'stock'       => apply_filters( 'tinvwl_notifications_default_value', 'unsubscribed' ),
		);

		return $meta;
	}

	/**
	 * @return bool
	 */
	public static function show_option() {

		$show = true;

		if ( ! tinv_get_option( 'notification_price_email', 'enabled' ) && ! tinv_get_option( 'notification_stock_email', 'enabled' ) && ! apply_filters( 'tinvwl_notifications_option_promotional', tinv_get_option( 'promotional_email', 'enabled' ) ) ) {
			$show = false;
		}

		return (bool) apply_filters( 'tinvwl_notifications_option', $show );
	}

	/**
	 * Shortcode basic function
	 *
	 *
	 * @return string
	 */
	function shortcode() {

		ob_start();
		$this->htmloutput();

		return ob_get_clean();
	}

}
