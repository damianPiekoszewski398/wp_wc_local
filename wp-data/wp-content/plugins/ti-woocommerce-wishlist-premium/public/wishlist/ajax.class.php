<?php
/**
 * Wishlist table AJAX actions
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
class TInvWL_Public_Wishlist_Ajax {

	/**
	 * Current wishlist
	 *
	 * @var array
	 */
	private $current_wishlist;

	/**
	 * This class
	 *
	 * @var \TInvWL_Public_Wishlist_Ajax
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Public_Wishlist_Ajax
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
			'tinvwl-security'    => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'tinvwl-action'      => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'tinvwl-product_id'  => FILTER_VALIDATE_INT,
			'tinvwl-product-wid' => FILTER_VALIDATE_INT,
			'tinvwl-to-wl'       => FILTER_VALIDATE_INT,
			'tinvwl-new-wl'      => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'tinvwl-paged'       => FILTER_VALIDATE_INT,
			'tinvwl-per-page'    => FILTER_VALIDATE_INT,
			'tinvwl-sharekey'    => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'tinvwl-products'    => array(
				'filter' => FILTER_VALIDATE_INT,
				'flags'  => FILTER_FORCE_ARRAY,
			),
			'tinvwl-qty'         => array(
				'filter'  => FILTER_SANITIZE_NUMBER_FLOAT,
				'flags'   => FILTER_FORCE_ARRAY | FILTER_FLAG_ALLOW_FRACTION,
				'options' => array( 'min_range' => 0, 'default' => 1 ),
			),
			'tinvwl-order'       => array(
				'filter' => FILTER_VALIDATE_INT,
				'flags'  => FILTER_FORCE_ARRAY,
			),
		) );

		if ( ! isset( $post['tinvwl-action'] ) || ! $post['tinvwl-action'] ) {
			return;
		}

		$wl       = new TInvWL_Wishlist( $this->_name );
		$wishlist = $wl->get_by_share_key( $post['tinvwl-sharekey'] );

		if ( ! $wishlist ) {
			$wishlist = $wl->get_by_user_default();
			$wishlist = array_shift( $wishlist );
		}

		$guest_wishlist = false;
		if ( ! is_user_logged_in() ) {
			$guest_wishlist = $wl->get_by_sharekey_default();
			$guest_wishlist = array_shift( $guest_wishlist );
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $post['tinvwl-security'] ) && wp_verify_nonce( $post['tinvwl-security'], 'wp_rest' ) && isset( $post['tinvwl-action'] ) ) {
			$this->wishlist_ajax_actions( $wishlist, $post, $guest_wishlist );
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

	function wishlist_ajax_actions( $wishlist, $post, $guest_wishlist = false ) {

		do_action( 'tinvwl_ajax_actions_before', $wishlist, $post, $guest_wishlist );

		if ( ! $wishlist && $guest_wishlist ) {
			$wishlist = $guest_wishlist;
		}

		$quantities         = isset( $post['tinvwl-qty'] ) ? $post['tinvwl-qty'] : array();
		$action             = $post['tinvwl-action'];
		$class              = TInvWL_Public_AddToWishlist::instance();
		$owner              = ( $wishlist && isset( $wishlist['is_owner'] ) ) ? (bool) $wishlist['is_owner'] : false;
		$response['status'] = false;
		$response['msg']    = array();

		switch ( $action ) {
			case 'remove':
				if ( ! $wishlist['is_owner'] ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}
				$product = $post['tinvwl-product_id'];
				if ( 0 === $wishlist['ID'] ) {
					$wlp = TInvWL_Product_Local::instance();
				} else {
					$wlp = new TInvWL_Product( $wishlist );
				}
				if ( empty( $wlp ) ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}
				$product_data = $wlp->get_wishlist( array( 'ID' => $product ) );
				$product_data = array_shift( $product_data );
				if ( empty( $product_data ) ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}

				$title = sprintf( __( '&ldquo;%s&rdquo;', 'ti-woocommerce-wishlist-premium' ), is_callable( array(
					$product_data['data'],
					'get_name'
				) ) ? $product_data['data']->get_name() : $product_data['data']->get_title() );

				if ( $wlp->remove( $product_data ) ) {
					$response['status'] = true;
					$response['msg'][]  = sprintf( __( '%s has been removed from {wishlist_title}.', 'ti-woocommerce-wishlist-premium' ), $title );
				} else {
					$response['status'] = false;
					$response['msg'][]  = sprintf( __( '%s has not been removed from {wishlist_title}.', 'ti-woocommerce-wishlist-premium' ), $title );
				}

				break;
			case 'add_to_cart_single':
				$product_id = $post['tinvwl-product_id'];
				if ( 0 === $wishlist['ID'] ) {
					$wlp = TInvWL_Product_Local::instance();
				} else {
					$wlp = new TInvWL_Product( $wishlist );
				}
				if ( empty( $wlp ) ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}
				$product_data = $wlp->get_wishlist( array( 'ID' => $product_id ) );
				$product_data = array_shift( $product_data );
				if ( empty( $product_data ) ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}

				$title = sprintf( __( '&ldquo;%s&rdquo;', 'ti-woocommerce-wishlist-premium' ), is_callable( array(
					$product_data['data'],
					'get_name'
				) ) ? $product_data['data']->get_name() : $product_data['data']->get_title() );

				global $product;
				// store global product data.
				$_product_tmp = $product;
				// override global product data.
				$product = $product_data['data'];

				add_filter( 'clean_url', 'tinvwl_clean_url', 10, 2 );
				$redirect_url = $product_data['data']->add_to_cart_url();
				remove_filter( 'clean_url', 'tinvwl_clean_url', 10 );

				// restore global product data.
				$product = $_product_tmp;

				$quantity = apply_filters( 'tinvwl_product_add_to_cart_quantity', array_key_exists( $product_id, (array) $quantities ) ? $quantities[ $product_id ] : 1, $product_data['data'] );

				if ( apply_filters( 'tinvwl_product_add_to_cart_need_redirect', false, $product_data['data'], $redirect_url, $product_data ) ) {
					$response['redirect'] = apply_filters( 'tinvwl_product_add_to_cart_redirect_url', $redirect_url, $product_data['data'], $product_data );

				} elseif ( apply_filters( 'tinvwl_allow_addtocart_in_wishlist', true, $wishlist, $owner ) ) {
					$add = TInvWL_Public_Cart::add( $wishlist, $product_id, $quantity );
					if ( $add ) {
						$response['status'] = true;
						$response['msg'][]  = sprintf( _n( '%s has been added to your cart.', '%s have been added to your cart.', 1, 'ti-woocommerce-wishlist-premium' ), $title );

						if ( tinv_get_option( 'processing', 'redirect_checkout' ) ) {
							$response['redirect'] = wc_get_checkout_url();
						}

						if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
							$response['redirect'] = wc_get_cart_url();
						}
					} else {
						$response['status'] = false;
						$response['msg'][]  = sprintf( __( 'Product %s could not be added to the cart because some requirements are not met.', 'ti-woocommerce-wishlist-premium' ), $title );
					}
				}

				break;
			case 'remove_selected':
				if ( ! $owner ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}
				$wlp = null;
				if ( 0 === $wishlist['ID'] ) {
					$wlp = TInvWL_Product_Local::instance();
				} else {
					$wlp = new TInvWL_Product( $wishlist );
				}
				if ( empty( $wlp ) ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}

				$products = $wlp->get_wishlist( array(
					'ID'    => $post['tinvwl-products'],
					'count' => 9999999,
				) );

				$titles = array();
				foreach ( $products as $product ) {
					if ( $wlp->remove_product_from_wl( $product['wishlist_id'], $product['product_id'], $product['variation_id'], $product['meta'] ) ) {
						$titles[] = sprintf( __( '&ldquo;%s&rdquo;', 'ti-woocommerce-wishlist-premium' ), is_callable( array(
							$product['data'],
							'get_name'
						) ) ? $product['data']->get_name() : $product['data']->get_title() );
					}
				}
				if ( ! empty( $titles ) ) {
					$response['status'] = true;
					$response['msg'][]  = sprintf( _n( '%s has been successfully removed from {wishlist_title}.', '%s have been successfully removed from {wishlist_title}.', count( $titles ), 'ti-woocommerce-wishlist-premium' ), wc_format_list_of_items( $titles ) );
				}

				break;
			case 'add_to_cart_selected':
				$products = $post['tinvwl-products'];

				$result = $errors = array();
				foreach ( $products as $id ) {
					$wlp = null;
					if ( 0 === $wishlist['ID'] ) {
						$wlp = TInvWL_Product_Local::instance();
					} else {
						$wlp = new TInvWL_Product( $wishlist );
					}
					$_product     = $wlp->get_wishlist( array( 'ID' => $id ) );
					$_product     = array_shift( $_product );
					$product_data = wc_get_product( $_product['variation_id'] ? $_product['variation_id'] : $_product['product_id'] );

					if ( ! $product_data || 'trash' === $product_data->get_status() ) {
						continue;
					}

					global $product;
					// store global product data.
					$_product_tmp = $product;
					// override global product data.
					$product = $product_data;

					add_filter( 'clean_url', 'tinvwl_clean_url', 10, 2 );
					$redirect_url = $product_data->add_to_cart_url();
					remove_filter( 'clean_url', 'tinvwl_clean_url', 10 );

					// restore global product data.
					$product = $_product_tmp;

					$quantity             = apply_filters( 'tinvwl_product_add_to_cart_quantity', array_key_exists( $_product['ID'], (array) $quantities ) ? $quantities[ $_product['ID'] ] : 1, $product_data );
					$_product['quantity'] = $quantity;
					if ( apply_filters( 'tinvwl_product_add_to_cart_need_redirect', false, $product_data, $redirect_url, $_product ) ) {
						$errors[] = $_product['product_id'];
						continue;
					}

					$_product = $_product['ID'];

					$add = TInvWL_Public_Cart::add( $wishlist, $_product, $quantity );

					if ( $add ) {
						$result = tinv_array_merge( $result, $add );
					} else {
						$errors[] = $product_data->get_id();
					}
				}

				if ( ! empty( $errors ) ) {
					$titles = array();
					foreach ( $errors as $product_id ) {
						$titles[] = sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'ti-woocommerce-wishlist-premium' ), strip_tags( get_the_title( $product_id ) ) );
					}
					$titles            = array_filter( $titles );
					$response['msg'][] = sprintf( _n( 'Product %s could not be added to the cart because some requirements are not met.', 'Products: %s could not be added to the cart because some requirements are not met.', count( $titles ), 'ti-woocommerce-wishlist-premium' ), wc_format_list_of_items( $titles ) );
				}
				if ( ! empty( $result ) ) {
					$response['status'] = true;

					$titles = array();
					$count  = 0;
					foreach ( $result as $product_id => $qty ) {
						/* translators: %s: product name */
						$titles[] = apply_filters( 'woocommerce_add_to_cart_qty_html', ( $qty > 1 ? floatval( $qty ) . ' &times; ' : '' ), $product_id ) . apply_filters( 'woocommerce_add_to_cart_item_name_in_quotes', sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'ti-woocommerce-wishlist-premium' ), strip_tags( get_the_title( $product_id ) ) ), $product_id );
						$count    += $qty;
					}

					$titles = array_filter( $titles );
					/* translators: %s: product name */
					$response['msg'][] = sprintf( _n( '%s has been added to your cart.', '%s have been added to your cart.', $count, 'ti-woocommerce-wishlist-premium' ), wc_format_list_of_items( $titles ) );

					if ( tinv_get_option( 'processing', 'redirect_checkout' ) ) {
						$response['redirect'] = wc_get_checkout_url();
					}

					if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
						$response['redirect'] = wc_get_cart_url();
					}
				}

				break;
			case 'add_to_cart_all':

				add_filter( 'tinvwl_before_get_current_product', array(
					'TInvWL_Public_Wishlist_Buttons',
					'get_all_products_fix_offset'
				) );
				$products = TInvWL_Public_Wishlist_Buttons::get_current_products( $wishlist, 9999999 );
				$result   = $errors = array();
				foreach ( $products as $_product ) {
					$product_data = wc_get_product( $_product['variation_id'] ? $_product['variation_id'] : $_product['product_id'] );

					if ( ! $product_data || 'trash' === $product_data->get_status() ) {
						continue;
					}

					global $product;
					// store global product data.
					$_product_tmp = $product;
					// override global product data.
					$product = $product_data;

					add_filter( 'clean_url', 'tinvwl_clean_url', 10, 2 );
					$redirect_url = $product_data->add_to_cart_url();
					remove_filter( 'clean_url', 'tinvwl_clean_url', 10 );

					// restore global product data.
					$product = $_product_tmp;

					$quantity             = apply_filters( 'tinvwl_product_add_to_cart_quantity', $_product['quantity'], $product_data );
					$_product['quantity'] = $quantity;
					if ( apply_filters( 'tinvwl_product_add_to_cart_need_redirect', false, $product_data, $redirect_url, $_product ) ) {
						$errors[] = $_product['product_id'];
						continue;
					}

					$_product = $_product['ID'];

					$add = TInvWL_Public_Cart::add( $wishlist, $_product, $quantity );

					if ( $add ) {
						$result = tinv_array_merge( $result, $add );
					} else {
						$errors[] = $product_data->get_id();
					}
				}

				if ( ! empty( $errors ) ) {
					$titles = array();
					foreach ( $errors as $product_id ) {
						$titles[] = sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'ti-woocommerce-wishlist-premium' ), strip_tags( get_the_title( $product_id ) ) );
					}
					$titles            = array_filter( $titles );
					$response['msg'][] = sprintf( _n( 'Product %s could not be added to the cart because some requirements are not met.', 'Products: %s could not be added to the cart because some requirements are not met.', count( $titles ), 'ti-woocommerce-wishlist-premium' ), wc_format_list_of_items( $titles ) );
				}
				if ( ! empty( $result ) ) {
					$response['status'] = true;

					$titles = array();
					$count  = 0;
					foreach ( $result as $product_id => $qty ) {
						/* translators: %s: product name */
						$titles[] = apply_filters( 'woocommerce_add_to_cart_qty_html', ( $qty > 1 ? floatval( $qty ) . ' &times; ' : '' ), $product_id ) . apply_filters( 'woocommerce_add_to_cart_item_name_in_quotes', sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'ti-woocommerce-wishlist-premium' ), strip_tags( get_the_title( $product_id ) ) ), $product_id );
						$count    += $qty;
					}

					$titles = array_filter( $titles );
					/* translators: %s: product name */
					$response['msg'][] = sprintf( _n( '%s has been added to your cart.', '%s have been added to your cart.', $count, 'ti-woocommerce-wishlist-premium' ), wc_format_list_of_items( $titles ) );

					if ( tinv_get_option( 'processing', 'redirect_checkout' ) ) {
						$response['redirect'] = wc_get_checkout_url();
					}

					if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
						$response['redirect'] = wc_get_cart_url();
					}
				}

				break;
			case (bool) preg_match( '/move_selected.*/', $action ):
				if ( ! $owner ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}
				if ( 0 === $wishlist['ID'] ) {
					$wlp_from = TInvWL_Product_Local::instance();
				} else {
					$wlp_from = new TInvWL_Product( $wishlist );
				}
				if ( empty( $wlp_from ) ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}
				if ( preg_match( '/move_selected\[(\d+)\]/i', $action, $match_action ) ) {
					$wishlist_to = tinv_wishlist_get( absint( $match_action[1] ), false );
					if ( empty( $wishlist_to ) ) {
						$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
						break;
					}
				}

				$wlp_to   = new TInvWL_Product( $wishlist_to );
				$products = $wlp_from->get_wishlist( array(
					'ID'    => $post['tinvwl-products'],
					'count' => 9999999,
				) );

				$titles         = array();
				$titles_error   = array();
				$titles_nremove = array();
				foreach ( $products as $product ) {
					if ( ! $product['data'] ) {
						continue;
					}
					unset( $product['wishlist_id'] );
					$title = sprintf( __( '&ldquo;%s&rdquo;', 'ti-woocommerce-wishlist-premium' ), is_callable( array(
						$product['data'],
						'get_name'
					) ) ? $product['data']->get_name() : $product['data']->get_title() );
					if ( $wlp_to->add_product( $product ) ) {
						if ( $wlp_from->remove_product_from_wl( 0, $product['product_id'], $product['variation_id'], $product['meta'] ) ) {
							$titles[] = $title;
						} else {
							$titles_nremove[] = $title;
						}
					} else {
						$titles_error[] = $title;
					}
				}
				if ( ! empty( $titles ) ) {
					$response['status'] = true;
					$response['msg'][]  = sprintf( _n( 'Product %1$s has been moved to "%2$s".', 'Products %1$s have been moved to "%2$s".', count( $titles ), 'ti-woocommerce-wishlist-premium' ),
						wc_format_list_of_items( $titles ),
						sprintf( '<a href="%s">%s</a>', esc_url( tinv_url_wishlist( $wishlist_to['ID'] ) ), esc_html( $wishlist_to['title'] ) )
					);

				}
				if ( ! empty( $titles_nremove ) ) {
					$response['status'] = false;
					$response['msg'][]  = sprintf( _n( '%1$s has been moved to "%2$s", but not removed from "%3$s".', '%1$s have been moved to "%2$s", but not removed from "%3$s".', count( $titles_nremove ), 'ti-woocommerce-wishlist-premium' ),
						wc_format_list_of_items( $titles_nremove ),
						sprintf( '<a href="%s">%s</a>', esc_url( tinv_url_wishlist( $wishlist_to['ID'] ) ), esc_html( $wishlist_to['title'] ) ),
						sprintf( '<a href="%s">%s</a>', esc_url( tinv_url_wishlist( $wishlist['ID'] ) ), esc_html( $wishlist['title'] ) )
					);


				}
				if ( ! empty( $titles_error ) ) {
					$response['status'] = false;
					$response['msg'][]  = sprintf( _n( '%s has not been moved.', '%s have not been moved.', count( $titles_nremove ), 'ti-woocommerce-wishlist-premium' ), wc_format_list_of_items( $titles_nremove ) );

				}

				break;
			case 'move_single':
				if ( ! $owner ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}
				if ( 0 === $wishlist['ID'] ) {
					$wlp_from = TInvWL_Product_Local::instance();
				} else {
					$wlp_from = new TInvWL_Product( $wishlist );
				}
				if ( empty( $wlp_from ) ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}

				if ( $post['tinvwl-to-wl'] ) {
					$wishlist_to = tinv_wishlist_get( $post['tinvwl-to-wl'], false );
				} else {
					$wishlist_name = $post['tinvwl-new-wl'];
					$_wishlist_to  = tinv_wishlist_create( $wishlist_name );
					if ( is_wp_error( $_wishlist_to ) ) {
						switch ( $_wishlist_to->get_error_code() ) {
							case 'wishlist_already_exists':
								$data['msg'][] = sprintf( __( 'Oops! It looks like wishlist with the name "%s" already exists. Please, try another name', 'ti-woocommerce-wishlist-premium' ), $wishlist_name );
								break;
							case 'wishlist_not_created':
							case 'wishlist_empty_name':
								$data['msg'][] = __( 'Wishlist is not added, and the product is added to the Default Wishlist!', 'ti-woocommerce-wishlist-premium' );
								break;
							default:
								$data['msg'][] = $_wishlist_to->get_error_message();
						}
						$wishlist_to = $_wishlist_to->get_error_data();
					} else {
						$wishlist_to = $_wishlist_to;
					}
				}

				if ( empty( $wishlist_to ) ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}

				$wlp_to   = new TInvWL_Product( $wishlist_to );
				$products = $wlp_from->get_wishlist( array(
					'ID'    => $post['tinvwl-product-wid'],
					'count' => 1,
				) );

				$titles         = array();
				$titles_error   = array();
				$titles_nremove = array();
				foreach ( $products as $product ) {
					if ( ! $product['data'] ) {
						continue;
					}
					unset( $product['wishlist_id'] );
					$title = sprintf( __( '&ldquo;%s&rdquo;', 'ti-woocommerce-wishlist-premium' ), is_callable( array(
						$product['data'],
						'get_name'
					) ) ? $product['data']->get_name() : $product['data']->get_title() );
					if ( $wlp_to->add_product( $product ) ) {
						if ( $wlp_from->remove_product_from_wl( 0, $product['product_id'], $product['variation_id'], $product['meta'] ) ) {
							$titles[] = $title;
						} else {
							$titles_nremove[] = $title;
						}
					} else {
						$titles_error[] = $title;
					}
				}
				if ( ! empty( $titles ) ) {
					$response['status'] = true;
					$response['msg'][]  = sprintf( _n( 'Product %1$s has been moved to "%2$s".', 'Products %1$s have been moved to "%2$s".', count( $titles ), 'ti-woocommerce-wishlist-premium' ),
						wc_format_list_of_items( $titles ),
						sprintf( '<a href="%s">%s</a>', esc_url( tinv_url_wishlist( $wishlist_to['ID'] ) ), esc_html( $wishlist_to['title'] ) )
					);

				}
				if ( ! empty( $titles_nremove ) ) {
					$response['msg'][] = sprintf( _n( '%1$s has been moved to "%2$s", but not removed from "%3$s".', '%1$s have been moved to "%2$s", but not removed from "%3$s".', count( $titles_nremove ), 'ti-woocommerce-wishlist-premium' ),
						wc_format_list_of_items( $titles_nremove ),
						sprintf( '<a href="%s">%s</a>', esc_url( tinv_url_wishlist( $wishlist_to['ID'] ) ), esc_html( $wishlist_to['title'] ) ),
						sprintf( '<a href="%s">%s</a>', esc_url( tinv_url_wishlist( $wishlist['ID'] ) ), esc_html( $wishlist['title'] ) )
					);


				}
				if ( ! empty( $titles_error ) ) {
					$response['msg'][] = sprintf( _n( '%s has not been moved.', '%s have not been moved.', count( $titles_nremove ), 'ti-woocommerce-wishlist-premium' ), wc_format_list_of_items( $titles_nremove ) );

				}

				break;
			case 'update':
				if ( ! $owner ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}
				if ( ( empty( $quantities ) || ! is_array( $quantities ) ) && ( empty( $post['tinvwl-order'] ) || ! is_array( $post['tinvwl-order'] ) ) ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}

				if ( 0 === $wishlist['ID'] ) {
					$wlp = TInvWL_Product_Local::instance();
				} else {
					$wlp = new TInvWL_Product( $wishlist );
				}
				if ( empty( $wlp ) ) {
					$response['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
					break;
				}

				$ids = $quantities ? array_keys( $quantities ) : $post['tinvwl-order'];

				$products = $wlp->get_wishlist( array(
					'ID'    => $ids,
					'count' => 9999999,
				) );

				$titles  = array();
				$titles2 = array();
				foreach ( $products as $product ) {
					if ( ! $product['data'] ) {
						continue;
					}
					if ( $post['tinvwl-order'] ) {
						foreach ( $post['tinvwl-order'] as $key => $pid ) {
							if ( $product['ID'] === $pid ) {
								$product['order'] = $key;
							}
						}
					}

					$product['order'] = isset( $product['order'] ) ? $product['order'] : 0;

					if ( ! $quantities ) {
						$wlp->update( $product );
					}

					if ( array_key_exists( $product['ID'], $quantities ) ) {
						$quantity = floatval( $quantities[ $product['ID'] ] );
						if ( 0 < $quantity || apply_filters( 'tinvwl_allow_zero_quantity', false ) ) {
							$original_qty        = $product['quantity'];
							$product['quantity'] = $quantity;
							if ( $wlp->update( $product ) ) {
								if ( $original_qty !== $quantity ) {
									$titles[] = sprintf( __( '&ldquo;%s&rdquo;', 'ti-woocommerce-wishlist-premium' ), is_callable( array(
										$product['data'],
										'get_name'
									) ) ? $product['data']->get_name() : $product['data']->get_title() );
									do_action( 'tinvwl_changed_wishlist', 8, $wishlist, $product['product_id'], $product['variation_id'], $quantity );
								}
							}

						} else {
							if ( $wlp->remove_product_from_wl( $product['wishlist_id'], $product['product_id'], $product['variation_id'], $product['meta'] ) ) {
								$titles2[] = sprintf( __( '&ldquo;%s&rdquo;', 'ti-woocommerce-wishlist-premium' ), is_callable( array(
									$product['data'],
									'get_name'
								) ) ? $product['data']->get_name() : $product['data']->get_title() );
								do_action( 'tinvwl_changed_wishlist', 2, $wishlist, $product['product_id'], $product['variation_id'] );
							}
						}
					}
				}
				if ( ! empty( $titles ) ) {
					$response['msg'][] = sprintf( _n( '%s has been successfully updated.', '%s have been successfully updated.', count( $titles ), 'ti-woocommerce-wishlist-premium' ), wc_format_list_of_items( $titles ) );
				}
				if ( ! empty( $titles2 ) ) {
					$response['msg'][] = sprintf( _n( '%s has been successfully removed from {wishlist_title}.', '%s have been successfully removed from {wishlist_title}.', count( $titles2 ), 'ti-woocommerce-wishlist-premium' ), wc_format_list_of_items( $titles2 ) );
				}
				if ( ! empty( $titles ) || ! empty( $titles2 ) ) {
					$response['status'] = true;
				} elseif ( $post['tinvwl-order'] ) {
					$response['status'] = true;
					$response['msg'][]  = __( 'Products order has been successfully updated.', 'ti-woocommerce-wishlist-premium' );
				}

				break;
			case 'get_data':
				$response['status'] = true;
				break;
		}
		$response['content'] = tinvwl_shortcode_view( array(
			'paged'          => $post['tinvwl-paged'],
			'sharekey'       => $post['tinvwl-sharekey'],
			'lists_per_page' => $post['tinvwl-per-page'],
		) );
		$response['action']  = $action;
		$response['icon']    = $response['status'] ? 'icon_big_heart_check' : 'icon_big_times';
		$response['msg']     = array_unique( $response['msg'] );
		$response['msg']     = implode( '<br>', $response['msg'] );
		if ( tinv_get_option( 'table', 'hide_popup' ) && array_key_exists( 'msg', $response ) ) {
			unset( $response['msg'] );
		}
		if ( ! empty( $response['msg'] ) ) {
			$response['msg']      = tinvwl_message_placeholders( $response['msg'], null, $wishlist );
			$response['wishlist'] = $wishlist;
			$response['msg']      = tinv_wishlist_template_html( 'ti-addedtowishlist-dialogbox.php', apply_filters( 'tinvwl_addtowishlist_dialog_box', $response, $post ) );
		}

		$share_key = false;

		if ( $guest_wishlist ) {
			$share_key = $guest_wishlist['share_key'];
		}
		$response['wishlists_data'] = $class->get_wishlists_data( $share_key );

		do_action( 'tinvwl_action_' . $action, $wishlist, $post['tinvwl-products'], $quantities, $owner ); // @codingStandardsIgnoreLine WordPress.NamingConventions.ValidHookName.UseUnderscores
		do_action( 'tinvwl_ajax_actions_after', $wishlist, $post, $guest_wishlist );
		wp_send_json( $response );
	}
}
