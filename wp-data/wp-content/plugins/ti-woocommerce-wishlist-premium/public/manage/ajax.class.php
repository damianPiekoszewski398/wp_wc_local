<?php
/**
 * Manage wishlists table AJAX actions
 *
 * @since             2.0.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Wishlist shortcode
 */
class TInvWL_Public_Manage_Ajax {

	/**
	 * This class
	 *
	 * @var \TInvWL_Public_Wishlist_Ajax
	 */
	protected static $_instance = null;

	static $privacy;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Public_Manage_Ajax
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
		$this->_name = $plugin_name;
		$this->define_hooks();
	}

	/**
	 * Defined shortcode and hooks
	 */
	function define_hooks() {
		add_action( 'wc_ajax_tinvwl', array( $this, 'ajax_action' ) );
	}

	function ajax_action() {

		$post = filter_input_array( INPUT_POST, array(
			'tinvwl-security'            => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'tinvwl-manage-action'       => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'tinvwl-paged'               => FILTER_VALIDATE_INT,
			'tinvwl-wishlist-id'         => FILTER_VALIDATE_INT,
			'tinvwl-wishlists'           => array(
				'filter' => FILTER_VALIDATE_INT,
				'flags'  => FILTER_FORCE_ARRAY,
			),
			'tinvwl-wishlists-titles'    => array(
				'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
				'flags'  => FILTER_FORCE_ARRAY,
			),
			'tinvwl-wishlists-privacies' => array(
				'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
				'flags'  => FILTER_FORCE_ARRAY,
			),
		) );

		if ( ! $post['tinvwl-manage-action'] ) {
			return;
		}

		if ( is_user_logged_in() && defined( 'DOING_AJAX' ) && DOING_AJAX && $post['tinvwl-security'] && wp_verify_nonce( $post['tinvwl-security'], 'wp_rest' ) && $post['tinvwl-manage-action'] ) {
			$this->ajax_actions( $post );
		} else {
			$response['status'] = false;
			$response['msg'][]  = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
			$response['icon']   = $response['status'] ? 'icon_big_heart_check' : 'icon_big_times';
			$response['msg']    = array_unique( $response['msg'] );
			$response['msg']    = implode( '<br>', $response['msg'] );
			if ( tinv_get_option( 'table', 'hide_popup' ) && array_key_exists( 'msg', $response ) ) {
				unset( $response['msg'] );
			}
			if ( ! empty( $response['msg'] ) ) {
				$response['msg'] = tinv_wishlist_template_html( 'ti-addedtowishlist-dialogbox.php', apply_filters( 'tinvwl_addtowishlist_dialog_box', $response, $post ) );
			}
			wp_send_json( $response );
		}
	}

	function ajax_actions( $post ) {
		$action             = $post['tinvwl-manage-action'];
		$response['status'] = false;
		$response['msg']    = array();
		self::$privacy      = TInvWL_Public_Manage_Wishlist::get_wishlists_privacy();

		switch ( $action ) {
			case 'remove':
				$wl       = new TInvWL_Wishlist();
				$wishlist = $wl->get_by_id( $post['tinvwl-wishlist-id'] );
				if ( ! $wishlist['is_owner'] ) {
					$response['status'] = false;
					$response['msg'][]  = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}
				if ( ! in_array( $wishlist['type'], apply_filters( 'tinvwl_wishlist_type_exclusion', array( 'default' ) ) ) && $wl->remove( $post['tinvwl-wishlist-id'] ) ) {
					$response['status'] = true;
					$response['msg'][]  = sprintf( __( 'Successfully deleted wishlist "%s".', 'ti-woocommerce-wishlist-premium' ), $wishlist['title'] );
				} else {
					$response['status'] = false;
					$response['msg'][]  = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
				}

				break;
			case 'save':
				$wl               = new TInvWL_Wishlist();
				$result           = array();
				$wishlist_privacy = $post['tinvwl-wishlists-privacies'];
				$wishlist_name    = $post['tinvwl-wishlists-titles'];
				foreach ( $post['tinvwl-wishlists'] as $wishlist_id ) {
					$need_update       = false;
					$wishlist          = $wl->get_by_id( $wishlist_id );
					$privacy           = tinv_get_option( 'general', 'default_privacy' );
					$original_wishlist = $wishlist;

					if ( array_key_exists( $wishlist_id, (array) $wishlist_privacy ) ) {
						$_privacy = $wishlist_privacy[ $wishlist_id ];
						if ( array_key_exists( $_privacy, self::$privacy ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
							$privacy = $_privacy;
						}
						if ( $wishlist['status'] !== $privacy ) {
							$need_update = true;
						}
						$wishlist['status'] = $privacy;
					}
					if ( array_key_exists( $wishlist_id, (array) $wishlist_name ) ) {
						$name = trim( $wishlist_name[ $wishlist_id ] );
						if ( 'default' === $wishlist['type'] ) {
							if ( $wishlist['title'] !== $name ) {
								$need_update = true;
							}
							if ( apply_filters( 'tinvwl_default_wishlist_title', tinv_get_option( 'general', 'default_title' ) ) == $name ) { // WPCS: loose comparison ok.
								$name = '';
							}
							$wishlist['title'] = $name;
						} elseif ( ! empty( $name ) ) {
							if ( $wishlist['title'] !== $name ) {
								$need_update = true;
							}
							$wishlist['title'] = $name;
						}
					}
					$wishlist = apply_filters( 'tinvwl_manage_wishlist_update', $wishlist, $wishlist_id );
					if ( apply_filters( 'tinvwl_manage_wishlist_need_update', $need_update, $wishlist_id, $wishlist, $original_wishlist ) ) {
						if ( $wl->update( $wishlist_id, $wishlist ) ) {
							$result[ $wishlist_id ] = ( 'default' === $wishlist['type'] && empty( $wishlist['title'] ) ? apply_filters( 'tinvwl_default_wishlist_title', tinv_get_option( 'general', 'default_title' ) ) : $wishlist['title'] );
						}
					}
				}
				if ( ! empty( $result ) ) {
					$response['status'] = true;
					$response['msg'][]  = sprintf( __( 'Successfully updated wishlists: %s.', 'ti-woocommerce-wishlist-premium' ), implode( ', ', $result ) );
				} else {
					$response['status'] = false;
					$response['msg'][]  = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
				}

				break;

			case 'remove_selected':
				$wl     = new TInvWL_Wishlist();
				$result = array();

				foreach ( $post['tinvwl-wishlists'] as $wishlist_id ) {
					$wishlist = $wl->get_by_id( $wishlist_id );
					if ( ! $wishlist['is_owner'] ) {
						break;
					}

					if ( ! in_array( $wishlist['type'], apply_filters( 'tinvwl_wishlist_type_exclusion', array( 'default' ) ) )  && $wl->remove( $wishlist_id ) ) {
						$result[ $wishlist_id ] = ( 'default' === $wishlist['type'] && empty( $wishlist['title'] ) ? apply_filters( 'tinvwl_default_wishlist_title', tinv_get_option( 'general', 'default_title' ) ) : $wishlist['title'] );
					}
				} // End foreach().
				if ( ! empty( $result ) ) {
					$response['status'] = true;
					$response['msg'][]  = sprintf( __( 'Successfully deleted wishlist "%s".', 'ti-woocommerce-wishlist-premium' ), implode( ', ', $result ) );
				} else {
					$response['status'] = false;
					$response['msg'][]  = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
				}
				break;

			case array_key_exists( $action, self::$privacy ):
				$wl     = new TInvWL_Wishlist();
				$result = array();

				foreach ( $post['tinvwl-wishlists'] as $wishlist_id ) {
					$wishlist = $wl->get_by_id( $wishlist_id );
					if ( ! $wishlist['is_owner'] ) {
						break;
					}
					$wishlist['status'] = $action;
					if ( $wl->update( $wishlist_id, $wishlist ) ) {
						$result[ $wishlist_id ] = ( 'default' === $wishlist['type'] && empty( $wishlist['title'] ) ? apply_filters( 'tinvwl_default_wishlist_title', tinv_get_option( 'general', 'default_title' ) ) : $wishlist['title'] );
					}
				}
				if ( ! empty( $result ) ) {
					$response['status'] = true;
					$response['msg'][]  = sprintf( __( 'Successfully updated wishlists: %s.', 'ti-woocommerce-wishlist-premium' ), implode( ', ', $result ) );
				} else {
					$response['status'] = false;
					$response['msg'][]  = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
				}

				break;
		}
		$response['content'] = tinvwl_shortcode_manage( array(
			'paged' => $post['tinvwl-paged'],
		) );
		$response['icon']    = $response['status'] ? 'icon_big_heart_check' : 'icon_big_times';
		$response['msg']     = array_unique( $response['msg'] );
		$response['msg']     = implode( '<br>', $response['msg'] );
		if ( tinv_get_option( 'table', 'hide_popup' ) && array_key_exists( 'msg', $response ) ) {
			unset( $response['msg'] );
		}
		if ( ! empty( $response['msg'] ) ) {
			$response['msg'] = tinv_wishlist_template_html( 'ti-addedtowishlist-dialogbox.php', apply_filters( 'tinvwl_addtowishlist_dialog_box', $response, $post ) );
		}

		$class                      = TInvWL_Public_AddToWishlist::instance();
		$response['wishlists_data'] = $class->get_wishlists_data( false );

		wp_send_json( $response );
	}
}
