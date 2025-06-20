<?php
/**
 * Create wishlist shortcode
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Create wishlist shortcode
 */
class TInvWL_Public_Create {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $_name;
	/**
	 * Default setting for shortcode
	 *
	 * @var array
	 */
	private $default;
	/**
	 * This class
	 *
	 * @var \TInvWL_Public_Create
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Public_Create
	 */
	public static function instance( $plugin_name = TINVWL_PREFIX ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $plugin_name );
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 */
	function __construct( $plugin_name ) {
		$this->_name   = $plugin_name;
		$this->default = array(
			'show' => 'form',
		);
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
	 * @param array $atts Array parameter for shortcode.
	 *
	 * @return boolean
	 */
	public function htmloutput( $atts ) {
		if ( ! tinv_get_option( 'general', 'multi' ) ) {
			return false;
		}
		if ( ! is_user_logged_in() ) {
			return TInvWL_Public_Wishlist_Login::init();
		}

		$privacy      = TInvWL_Public_Manage_Wishlist::get_wishlists_privacy();
		$privacy_desc = array(
			'public'  => __( 'Anyone can search for and see this list. You can also share using a link.', 'ti-woocommerce-wishlist-premium' ),
			'share'   => __( 'Only people with the link can see this list. It will not appear in public search results.', 'ti-woocommerce-wishlist-premium' ),
			'private' => __( 'Only you can see this list.', 'ti-woocommerce-wishlist-premium' ),
		);
		$privacy_desc = array_merge( $privacy, $privacy_desc );
		$data         = array(
			'is_form_create'      => ( 'form' === $atts['show'] ),
			'privacy'             => $privacy,
			'privacy_description' => $privacy_desc,
		);
		tinv_wishlist_template( 'ti-wishlist-create.php', $data );
	}

	/**
	 * Action create wishlist
	 *
	 * @return boolean
	 */
	public function action() {
		$action = filter_input( INPUT_POST, 'tinvwl-action-create', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( empty( $action ) ) {
			return false;
		}
		$nonce = filter_input( INPUT_POST, 'tinv_wishlist_create_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'tinvwl_wishlistcreate' ) ) {
			return false;
		}
		if ( ! is_user_logged_in() || ! tinv_get_option( 'general', 'multi' ) ) {
			return false;
		}
		$post             = filter_input_array( INPUT_POST, array(
			'tinv-name-wishlist'    => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'tinv-privacy-wishlist' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'redirect'              => FILTER_VALIDATE_BOOLEAN,
		) );
		$name             = $post['tinv-name-wishlist'];
		$privacy          = $post['tinv-privacy-wishlist'];
		$wishlist         = tinv_wishlist_create( $name, $privacy );
		$data             = array(
			'icon'         => 'icon_big_heart_check',
			'wishlist_url' => '',
			'msg'          => __( 'Wishlist successfully created', 'ti-woocommerce-wishlist-premium' ),
		);
		$data['redirect'] = ( $post['redirect'] ) ? $post['redirect'] : false;
		if ( is_wp_error( $wishlist ) ) {
			if ( 'wishlist_already_exists' === $wishlist->get_error_code() ) {
				$_wishlist            = $wishlist->get_error_data();
				$data['wishlist_url'] = tinv_url_wishlist_by_key( $_wishlist['share_key'] );
			}
			$data['icon']     = 'icon_big_times';
			$data['msg']      = $wishlist->get_error_message();
			$data['redirect'] = false;
		} else {
			$data['wishlist_url'] = tinv_url_wishlist_by_key( $wishlist['share_key'] );
		}

		tinv_wishlist_template( 'ti-wishlist-create-message.php', $data );
		die();
	}

	/**
	 * Shortcode basic function
	 *
	 * @param array $atts Array parameter from shortcode.
	 *
	 * @return string
	 */
	function shortcode( $atts = array() ) {
		$atts = shortcode_atts( $this->default, $atts );
		if ( ! in_array( $atts['show'], array(
			'button',
			'form',
		) ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
			$atts['show'] = $this->default['show'];
		}
		ob_start();
		$this->htmloutput( $atts );

		return ob_get_clean();
	}

	/**
	 * View for Wishlist Manager
	 *
	 * @see \TInvWL_Public_Manage_Wishlist
	 */
	function view() {
		$atts         = $this->default;
		$atts['show'] = 'button';
		$this->htmloutput( $atts );
	}
}
