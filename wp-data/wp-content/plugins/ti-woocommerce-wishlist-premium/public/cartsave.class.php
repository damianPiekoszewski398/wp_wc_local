<?php
/**
 * Cart Save action
 *
 * @since             1.4.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Cart Save action
 */
class TInvWL_Public_CartSave {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	static $_name;
	/**
	 * Global product
	 *
	 * @var object
	 */
	private $product;
	/**
	 * This wishlists and product
	 *
	 * @var array
	 */
	private $wishlist;
	/**
	 * This cart key product
	 *
	 * @var string
	 */
	private $cart_item_key;
	/**
	 * This class
	 *
	 * @var TInvWL_Public_CartSave
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return TInvWL_Public_CartSave
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
		self::$_name = $plugin_name;
		$this->define_hooks();
	}

	/**
	 * Define hooks
	 */
	function define_hooks() {
		if ( tinv_get_option( 'add_to_wishlist_cart', 'item_show_in_cart' ) ) {
			add_action( 'woocommerce_cart_item_subtotal', array( $this, 'save_cart_item' ), 20, 3 );
			add_action( 'wp_loaded', array( $this, 'save_cart_item_action' ), 0 );
		}
		if ( tinv_get_option( 'add_to_wishlist_cart', 'show_in_cart' ) ) {
			add_action( 'woocommerce_cart_actions', array( $this, 'save_cart_addtowishlist' ) );
			add_action( 'wp_loaded', array( $this, 'save_cart_action' ), 0 );
		}
	}

	/**
	 * Add button to content
	 *
	 * @param string $subtotal Subtotal product for cart.
	 * @param array $cart_item Woocommerce cart data for product.
	 * @param string $cart_item_key Woocommerce cart key product.
	 *
	 * @return string
	 */
	public function save_cart_item( $subtotal, $cart_item, $cart_item_key ) {
		if ( ! is_cart() ) {
			return $subtotal;
		}
		ob_start();
		$this->save_cart_item_addtowishlist( $cart_item, $cart_item_key );
		$button = ob_get_clean();

		return $subtotal . ' ' . $button;
	}

	/**
	 * Wishlist button
	 *
	 * @param array $cart_item Woocommerce cart data for product.
	 * @param string $cart_item_key Woocommerce cart key product.
	 */
	public function save_cart_item_addtowishlist( $cart_item, $cart_item_key ) {

		if ( ! is_user_logged_in() && ! tinv_get_option( 'general', 'guests' ) ) {
			return;
		}
		if ( isset( $cart_item['composite_parent'] ) ) {
			return;
		}

		if ( ! apply_filters( 'tinvwl_allow_addtowishlist_cart_product', true, $cart_item['data'] ) ) {
			return false;
		}

		$this->product       = $product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$this->wishlist      = $wishlists = TInvWL_Public_AddToWishlist::instance()->user_wishlist( $product );
		$this->cart_item_key = $cart_item_key;

		if ( tinv_get_option( 'general', 'multi' ) && is_user_logged_in() ) {
			add_action( 'tinvwl_wishlist_addtowishlist_dialogbox', array( $this, 'dialog_box' ) );
		}
		add_action( 'tinvwl_wishlist_addtowishlist_button', array( $this, 'save_cart_item_button' ) );

		$data = array(
			'class_position'     => sprintf( 'tinvwl-%s-add-to-cart', 'cart' ),
			'product'            => $product,
			'variation_id'       => $product->is_type( 'variation' ) ? $product->get_id() : 0,
			'TInvWishlist'       => $wishlists,
			'button_icon'        => tinv_get_option( 'add_to_wishlist_cart', 'item_icon' ),
			'add_to_wishlist'    => apply_filters( 'tinvwl_add_to_wishlist_text_cart_item', tinv_get_option( 'add_to_wishlist_cart', 'item_text' ) ),
			'browse_in_wishlist' => apply_filters( 'tinvwl_view_wishlist_text', tinv_get_option( 'general', 'text_browse' ) ),
			'loop'               => false,
			'quantity'           => false,
		);
		tinv_wishlist_template( 'ti-addtowishlist.php', $data );
		remove_action( 'tinvwl_wishlist_addtowishlist_button', array( $this, 'save_cart_item_button' ) );
	}

	/**
	 * Create bulk button
	 *
	 * @param boolean $echo Return or output.
	 * @param string $class Name class.
	 */
	function save_cart_button( $echo = true, $class = 'tinvwl_all_cart_to_wishlist_button' ) {
		$content    = apply_filters( 'tinvwl_wishlist_button_before', '' );
		$text       = apply_filters( 'tinvwl_add_to_wishlist_text_cart', tinv_get_option( 'add_to_wishlist_cart', 'text' ) );
		$icon       = tinv_get_option( 'add_to_wishlist_cart', 'icon' );
		$icon_color = tinv_get_option( 'add_to_wishlist_cart', 'icon_style' );
		$icon_class = '';
		if ( empty( $text ) ) {
			$icon_class = ' no-txt';
		} else {
			$content .= '<div class="tinv-wishlist-clear"></div>';
		}
		if ( ! empty( $icon ) ) {
			$icon_upload = tinv_get_option( 'add_to_wishlist_cart', 'icon_upload' );
			if ( 'custom' === $icon && ! empty( $icon_upload ) ) {
				$text = sprintf( '<img src="%s" alt="%s" /> %s', esc_url( $icon_upload ), $text, $text );
			}
			$icon = 'tinvwl-icon-' . $icon;
			if ( 'custom' !== $icon && $icon_color ) {
				$icon .= ' icon-' . $icon_color;
			}
		}
		$icon .= $icon_class;
		foreach ( $this->wishlist as $value ) {
			if ( $value['in'] ) {
				$icon .= ' tinvwl-product-in-list';
				break;
			}
		}
		$icon .= ' ' . $class;

		$icon .= ' ' . tinv_get_option( 'add_to_wishlist_cart', 'class' );

		$icon .= ( tinv_get_option( 'add_to_wishlist_cart', 'show_preloader' ) ) ? ' ftinvwl-animated' : '';

		$content .= sprintf( '<a data-tinv-wl-cart-key="%s" class="%s" data-tinv-wl-list="%s" data-tinv-wl-product="%s" data-tinv-wl-productvariation="%s" data-tinv-wl-producttype="%s" href="javascript:void(0);" rel="nofollow">%s</a>', $this->cart_item_key, $icon, htmlspecialchars( wp_json_encode( $this->wishlist ) ), $this->product->is_type( 'variation' ) ? $this->product->get_parent_id() : $this->product->get_id(), $this->product->is_type( 'variation' ) ? $this->product->get_id() : 0, $this->product->get_type(), $text );

		$content .= apply_filters( 'tinvwl_wishlist_button_after', '' );

		if ( ! empty( $text ) ) {
			$content .= '<div class="tinv-wishlist-clear"></div>';
		}

		echo apply_filters( 'tinvwl_wishlist_button', $content );
	}

	/**
	 * Create button
	 *
	 * @param boolean $echo Return or output.
	 * @param string $class name class.
	 */
	function save_cart_item_button( $echo = true, $class = 'tinvwl_cart_to_wishlist_button' ) {
		$content    = apply_filters( 'tinvwl_wishlist_button_before', '' );
		$text       = apply_filters( 'tinvwl_add_to_wishlist_text_cart_item', tinv_get_option( 'add_to_wishlist_cart', 'item_text' ) );
		$icon       = tinv_get_option( 'add_to_wishlist_cart', 'item_icon' );
		$icon_color = tinv_get_option( 'add_to_wishlist_cart', 'item_icon_style' );
		$icon_class = '';
		if ( empty( $text ) ) {
			$icon_class = ' no-txt';
		} else {
			$content .= '<div class="tinv-wishlist-clear"></div>';
		}
		if ( ! empty( $icon ) ) {
			$icon_upload = tinv_get_option( 'add_to_wishlist_cart', 'item_icon_upload' );
			if ( 'custom' === $icon && ! empty( $icon_upload ) ) {
				$text = sprintf( '<img src="%s" alt="%s" /> %s', esc_url( $icon_upload ), $text, $text );
			}

			$icon = 'tinvwl-icon-' . $icon;
			if ( 'custom' !== $icon && $icon_color ) {
				$icon .= ' icon-' . $icon_color;
			}
		}
		$icon .= $icon_class;
		foreach ( $this->wishlist as $value ) {
			if ( $value['in'] ) {
				$icon .= ' tinvwl-product-in-list';
				break;
			}
		}
		$icon .= ' ' . $class;
		$icon .= ' ' . tinv_get_option( 'add_to_wishlist_cart', 'item_class' );
		$icon .= ( tinv_get_option( 'add_to_wishlist_cart', 'item_show_preloader' ) ) ? ' ftinvwl-animated' : '';

		$content .= sprintf( '<a data-tinv-wl-cart-key="%s" class="%s" data-tinv-wl-list="%s" data-tinv-wl-product="%s" data-tinv-wl-productvariation="%s" data-tinv-wl-producttype="%s" href="javascript:void(0);" rel="nofollow">%s</a>', $this->cart_item_key, $icon, htmlspecialchars( wp_json_encode( $this->wishlist ) ), $this->product->is_type( 'variation' ) ? $this->product->get_parent_id() : $this->product->get_id(), $this->product->is_type( 'variation' ) ? $this->product->get_id() : 0, $this->product->get_type(), $text );

		$content .= apply_filters( 'tinvwl_wishlist_button_after', '' );

		if ( ! empty( $text ) ) {
			$content .= '<div class="tinv-wishlist-clear"></div>';
		}

		echo apply_filters( 'tinvwl_wishlist_button', $content );
	}

	/**
	 * Output popup block
	 *
	 * @param array $wishlists Wishlists.
	 */
	function dialog_box( $wishlists ) {
		$data = array(
			'loop' => false,
		);
		tinv_wishlist_template( 'ti-addtowishlist-dialogbox.php', $data );
	}

	/**
	 * Add bulk button
	 */
	public function save_cart_addtowishlist() {
		if ( WC()->cart->is_empty() || ! is_cart() ) {
			return false;
		}

		if ( ! is_user_logged_in() && ! tinv_get_option( 'general', 'guests' ) ) {
			return false;
		}
		$cart_items          = WC()->cart->get_cart();
		$cart_item           = array_shift( $cart_items );
		$this->product       = $product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item );
		$this->wishlist      = $wishlists = TInvWL_Public_AddToWishlist::instance()->user_wishlist( $product );
		$this->cart_item_key = '';

		if ( tinv_get_option( 'general', 'multi' ) && is_user_logged_in() ) {
			add_action( 'tinvwl_wishlist_addtowishlist_dialogbox', array( $this, 'dialog_box' ) );
		}
		remove_action( 'tinvwl_wishlist_addtowishlist_button', array( $this, 'button' ) );
		add_action( 'tinvwl_wishlist_addtowishlist_button', array( $this, 'save_cart_button' ) );

		$data = array(
			'class_position'     => sprintf( 'tinvwl-%s-add-to-cart tinvwl-bulk-save-cart', 'cart' ),
			'product'            => $product,
			'variation_id'       => $product->is_type( 'variation' ) ? $product->get_id() : 0,
			'TInvWishlist'       => $wishlists,
			'button_icon'        => tinv_get_option( 'add_to_wishlist_cart', 'icon' ),
			'add_to_wishlist'    => apply_filters( 'tinvwl_add_to_wishlist_text_cart', tinv_get_option( 'add_to_wishlist_cart', 'text' ) ),
			'browse_in_wishlist' => apply_filters( 'tinvwl_view_wishlist_text', tinv_get_option( 'general', 'text_browse' ) ),
			'loop'               => false,
			'quantity'           => false,
		);
		tinv_wishlist_template( 'ti-addtowishlist.php', $data );
		remove_action( 'tinvwl_wishlist_addtowishlist_button', array( $this, 'save_cart_button' ) );
	}

	/**
	 * Action add product to wishlist
	 *
	 * @return boolean
	 */
	function save_cart_item_action() {
		if ( is_null( filter_input( INPUT_POST, 'cart_item_to_wishlist_id' ) ) ) {
			return false;
		} else {
			remove_action( 'init', 'woocommerce_add_to_cart_action' );
			remove_action( 'wp_loaded', 'WC_Form_Handler::add_to_cart_action', 20 );
		}
		ob_start();
		$data          = array(
			'msg'        => array(),
			'status'     => false,
			'updatepage' => false,
		);
		$products      = WC()->cart->get_cart();
		$cart_item_key = filter_input( INPUT_POST, 'tinv_product_key', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$post = filter_input_array( INPUT_POST, array(
			'cart_item_to_wishlist_id' => FILTER_VALIDATE_INT,
			'tinv_wishlist_name'       => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'redirect'                 => FILTER_SANITIZE_URL,
		) );

		if ( array_key_exists( $cart_item_key, $products ) ) {
			$product                   = apply_filters( 'woocommerce_cart_item_product', $products[ $cart_item_key ]['data'], $products[ $cart_item_key ], $cart_item_key );
			$post['product_id']        = $products[ $cart_item_key ]['product_id'];
			$post['product_variation'] = $products[ $cart_item_key ]['variation_id'];
			$post['quantity']          = $products[ $cart_item_key ]['quantity'];
			$post['product_type']      = $product->get_type();

			$form        = array();
			$inline_form = filter_input( INPUT_POST, 'form', FILTER_DEFAULT, FILTER_FORCE_ARRAY );
			if ( $inline_form ) {
				$form = array_merge( $form, $inline_form );
			}
			if ( array_key_exists( 'tinvwl_formdata', $products[ $cart_item_key ] ) ) {
				$form = array_merge( $form, $products[ $cart_item_key ]['tinvwl_formdata'] );
			}
			$form = apply_filters( 'tinvwl_addtowishlist_prepare_form', $form, $_POST, $_FILES );
			$form = apply_filters( 'tinvwl_addtowishlist_prepare_form_cart', $form, $cart_item_key, $products, $product );
			wp_recursive_ksort( $form );
		} else {
			$data['msg'][]      = __( 'Product not found in cart!', 'ti-woocommerce-wishlist-premium' );
			$data['updatepage'] = true;
		}

		$post['original_product_id'] = $post['product_id'];

		$wishlist_name = trim( $post['tinv_wishlist_name'] );
		$wishlist_id   = absint( $post['cart_item_to_wishlist_id'] );
		$wishlist      = null;
		if ( is_user_logged_in() && tinv_get_option( 'general', 'multi' ) && $wishlist_id ) {
			$wishlist = tinv_wishlist_get( $wishlist_id );
		}
		if ( empty( $wishlist ) ) {
			$wishlist = tinv_wishlist_create( $wishlist_name );
			if ( is_wp_error( $wishlist ) ) {
				switch ( $wishlist->get_error_code() ) {
					case 'wishlist_empty_name':
						break;
					case 'wishlist_forced_authorization_redirection':
						$data['force_redirect'] = apply_filters( 'tinvwl_addtowishlist_login_page', add_query_arg( 'tinvwl_redirect', rawurlencode( $post['redirect'] ), wc_get_page_permalink( 'myaccount' ) ), $post );
						break;
					case 'wishlist_authorization_redirection':
						$data['dialog_custom_url']  = apply_filters( 'tinvwl_addtowishlist_login_page', add_query_arg( 'tinvwl_redirect', rawurlencode( $post['redirect'] ), wc_get_page_permalink( 'myaccount' ) ), $post );
						$data['dialog_custom_html'] = esc_html( __( 'Login', 'ti-woocommerce-wishlist-premium' ) );
						break;
					default:
						$data['msg'][] = $wishlist->get_error_message();
				}
				$wishlist = $wishlist->get_error_data();
			} else {
				$data['wishlist_created'][ $wishlist['ID'] ] = $wishlist['title'];
			}
		}

		$wishlist = apply_filters( 'tinvwl_addtowishlist_wishlist', $wishlist );
		if ( empty( $wishlist ) ) {
			$data['icon'] = 'icon_big_times';
			$data['msg']  = array_unique( $data['msg'] );
			$data['msg']  = implode( '<br>', $data['msg'] );
			if ( ! empty( $data['msg'] ) ) {
				$data['wishlist'] = $wishlist;
				$data['msg']      = tinv_wishlist_template_html( 'ti-addedtowishlist-dialogbox.php', apply_filters( 'tinvwl_addtowishlist_dialog_box', $data, $post ) );
			}
			$data = apply_filters( 'tinvwl_addtowishlist_return_ajax', $data, $post );
			ob_clean();
			wp_send_json( $data );
		}

		$wlp = new TInvWL_Product( $wishlist );

		$status = true;
		if ( empty( $post['product_id'] ) || apply_filters( 'tinvwl_addtowishlist_not_allowed', false, $post ) ) {
			$status        = false;
			$data['msg'][] = __( 'Something went wrong', 'ti-woocommerce-wishlist-premium' );
		} else {
			$single_product       = ! tinv_get_option( 'general', 'quantity_func' );
			$post['product_type'] = apply_filters( 'tinvwl_addtowishlist_modify_type', $post['product_type'], $post );
			$post                 = apply_filters( 'tinvwl_addtowishlist_prepare', $post );
			switch ( $post['product_type'] ) {
				case 'group':
				case 'grouped' :
					$product = $wlp->product_data( $post['product_id'] );
					if ( empty( $product ) ) {
						$status = false;
					} else {
						$variations = $product->get_children();
						foreach ( $variations as $variation_id ) {
							$quantity       = ( array_key_exists( 'quantity', $post ) ) ? floatval( $post['quantity'] ) : 1;
							$quantity       = ( 0 < $quantity ) ? $quantity : 1;
							$check_adding   = false;
							$allowed_adding = false;
							if ( $single_product ) {
								$quantity     = 1;
								$check_adding = true;
							}
							if ( $check_adding ) {
								$allowed_adding = ! count( $wlp->get_wishlist( array(
									'product_id'   => $post['product_id'],
									'variation_id' => $variation_id,
									'external'     => false,
									'meta'         => $form,
								) ) );
							}
							if ( $check_adding && ! $allowed_adding ) {
								$data['msg'][] = apply_filters( 'tinvwl_already_in_wishlist_text', tinv_get_option( 'general', 'text_already_in' ) );
								$status        = false;
							} elseif ( $wlp->add_product( apply_filters( 'tinvwl_addtowishlist_add', array(
								'product_id'   => $post['product_id'],
								'variation_id' => $variation_id,
								'quantity'     => $quantity,
							) ) ) ) {
								$data['msg'][] = apply_filters( 'tinvwl_added_to_wishlist_text', tinv_get_option( 'general', 'text_added_to' ) );
								do_action( 'tinvwl_changed_wishlist', 1, $wishlist, $post['product_id'], $variation_id, $quantity );
							} else {
								$status = false;
							}
						}
					}
					break;
				case 'variable' :
				case 'variation' :
				case 'variable-subscription' :
					$variation_id = 0;
					if ( $post['product_variation'] ) {
						$variation_id = $post['product_variation'];
					} else {
						$variation_id = absint( filter_input( INPUT_POST, 'formvariation_id', FILTER_VALIDATE_INT ) );
					}

					$post['original_product_id'] = $variation_id;

					$quantity       = ( array_key_exists( 'quantity', $post ) ) ? floatval( $post['quantity'] ) : 1;
					$quantity       = ( 0 < $quantity ) ? $quantity : 1;
					$check_adding   = false;
					$allowed_adding = false;
					if ( $single_product ) {
						$quantity     = 1;
						$check_adding = true;
					}
					if ( $check_adding ) {
						$allowed_adding = ! count( $wlp->get_wishlist( array(
							'product_id'   => $post['product_id'],
							'variation_id' => $variation_id,
							'external'     => false,
							'meta'         => $form,
						) ) );
					}
					if ( $check_adding && ! $allowed_adding ) {
						$data['msg'][] = apply_filters( 'tinvwl_already_in_wishlist_text', tinv_get_option( 'general', 'text_already_in' ) );
						$status        = false;
					} elseif ( $wlp->add_product( apply_filters( 'tinvwl_addtowishlist_add', array(
						'product_id'   => $post['product_id'],
						'quantity'     => $quantity,
						'variation_id' => $variation_id,
					) ), apply_filters( 'tinvwl_addtowishlist_add_form', $form ) ) ) {
						$data['msg'][] = apply_filters( 'tinvwl_added_to_wishlist_text', tinv_get_option( 'general', 'text_added_to' ) );
						do_action( 'tinvwl_changed_wishlist', 1, $wishlist, $post['product_id'], $variation_id, $quantity );
					} else {
						$status = false;
					}
					break;
				case 'simple' :
				default:
					$quantity       = ( array_key_exists( 'quantity', $post ) ) ? floatval( $post['quantity'] ) : 1;
					$quantity       = ( 0 < $quantity ) ? $quantity : 1;
					$check_adding   = false;
					$allowed_adding = false;
					if ( $single_product ) {
						$quantity     = 1;
						$check_adding = true;
					}
					if ( $check_adding ) {
						$allowed_adding = ! count( $wlp->get_wishlist( array(
							'product_id' => $post['product_id'],
							'external'   => false,
							'meta'       => $form,
						) ) );
					}
					if ( $check_adding && ! $allowed_adding ) {
						$data['msg'][] = apply_filters( 'tinvwl_already_in_wishlist_text', tinv_get_option( 'general', 'text_already_in' ) );
						$status        = false;
					} elseif ( $wlp->add_product( apply_filters( 'tinvwl_addtowishlist_add', array(
						'product_id' => $post['product_id'],
						'quantity'   => $quantity,
					) ), apply_filters( 'tinvwl_addtowishlist_add_form', $form ) ) ) {
						$data['msg'][] = apply_filters( 'tinvwl_added_to_wishlist_text', tinv_get_option( 'general', 'text_added_to' ) );
						do_action( 'tinvwl_changed_wishlist', 1, $wishlist, $post['product_id'], 0, $quantity );
					} else {
						$status = false;
					}
					break;
			} // End switch().
		} // End if().
		$data['status'] = $status;
		if ( $status && tinv_get_option( 'add_to_wishlist_cart', 'item_remove_from_cart' ) ) {
			WC()->cart->remove_cart_item( $cart_item_key );
			$data['updatepage'] = true;
		}
		$data['wishlist_url'] = tinv_url_wishlist_default();
		if ( ! empty( $wishlist ) ) {
			$data['wishlist_url'] = tinv_url_wishlist( $wishlist['ID'] );
		}

		if ( $status && tinv_get_option( 'general', 'redirect' ) && tinv_get_option( 'page', 'wishlist' ) && tinv_get_option( 'general', 'show_notice' ) ) {
			$data['redirect'] = $data['wishlist_url'];
		}

		if ( ! empty( $post['product_id'] ) ) {
			$product = $original_product = wc_get_product( $post['product_id'] );
		}
		$data['icon'] = $data['status'] ? 'icon_big_heart_check' : 'icon_big_times';
		$data['msg']  = array_unique( $data['msg'] );
		$data['msg']  = implode( '<br>', $data['msg'] );

		if ( $post['original_product_id'] && $post['product_id'] !== $post['original_product_id'] ) {
			$original_product = wc_get_product( $post['original_product_id'] );
		}


		if ( ! empty( $data['msg'] ) ) {
			if ( $original_product ) {
				$data['msg'] = tinvwl_message_placeholders( $data['msg'], $original_product, $wishlist );
			}
			$data['msg']      = apply_filters( 'tinvwl_addtowishlist_message_after', $data['msg'], $data, $post, $form, $product );
			$data['wishlist'] = $wishlist;
			$data['msg']      = tinv_wishlist_template_html( 'ti-addedtowishlist-dialogbox.php', apply_filters( 'tinvwl_addtowishlist_dialog_box', $data, $post ) );
		}
		$share_key      = false;
		$guest_wishlist = false;
		if ( ! is_user_logged_in() ) {
			$wl             = new TInvWL_Wishlist();
			$guest_wishlist = $wl->get_by_user_default();
		}
		if ( $guest_wishlist ) {
			$share_key = $guest_wishlist['share_key'];
		}

		$class                  = TInvWL_Public_AddToWishlist::instance();
		$data['wishlists_data'] = $class->get_wishlists_data( $share_key );
		$data                   = apply_filters( 'tinvwl_addtowishlist_return_ajax', $data, $post, $form, $product );

		wp_send_json( $data );
	}

	/**
	 * Apply action bulk button
	 */
	public function save_cart_action() {
		if ( is_null( filter_input( INPUT_POST, 'cart_to_wishlist_id' ) ) ) {
			return;
		} else {
			remove_action( 'init', 'woocommerce_add_to_cart_action' );
			remove_action( 'wp_loaded', 'WC_Form_Handler::add_to_cart_action', 20 );
		}
		ob_start();

		$data = array(
			'msg'        => array(),
			'status'     => false,
			'updatepage' => false,
		);

		$post          = filter_input_array( INPUT_POST, array(
			'cart_to_wishlist_id' => FILTER_VALIDATE_INT,
			'tinv_wishlist_name'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		) );
		$wishlist_name = trim( $post['tinv_wishlist_name'] );
		$wishlist_id   = absint( $post['cart_to_wishlist_id'] );
		$wishlist      = null;
		if ( is_user_logged_in() && tinv_get_option( 'general', 'multi' ) && $wishlist_id ) {
			$wishlist = tinv_wishlist_get( $wishlist_id );
		}
		if ( empty( $wishlist ) ) {
			$wishlist = tinv_wishlist_create( $wishlist_name );
			if ( is_wp_error( $wishlist ) ) {
				switch ( $wishlist->get_error_code() ) {
					case 'wishlist_empty_name':
						break;
					case 'wishlist_forced_authorization_redirection':
						$data['force_redirect'] = apply_filters( 'tinvwl_addtowishlist_login_page', wc_get_page_permalink( 'myaccount' ), $post );
						break;
					case 'wishlist_authorization_redirection':
						$data['dialog_custom_url']  = apply_filters( 'tinvwl_addtowishlist_login_page', wc_get_page_permalink( 'myaccount' ), $post );
						$data['dialog_custom_html'] = esc_html( __( 'Login', 'ti-woocommerce-wishlist-premium' ) );
						break;
					default:
						$data['msg'][] = $wishlist->get_error_message();
				}
				$wishlist = $wishlist->get_error_data();
			}
		}

		$wishlist = apply_filters( 'tinvwl_addtowishlist_wishlist', $wishlist );
		if ( empty( $wishlist ) ) {
			$data['icon'] = 'icon_big_times';
			$data['msg']  = array_unique( $data['msg'] );
			$data['msg']  = implode( '<br>', $data['msg'] );
			if ( ! empty( $data['msg'] ) ) {
				$data['wishlist'] = $wishlist;
				$data['msg']      = tinv_wishlist_template_html( 'ti-addedtowishlist-dialogbox.php', apply_filters( 'tinvwl_addtowishlist_dialog_box', $data, $post ) );
			}
			$data = apply_filters( 'tinvwl_addtowishlist_return_ajax', $data, $post );
			ob_clean();
			wp_send_json( $data );
		}

		$result = array();
		$failed = array();
		$exists = array();
		if ( ! WC()->cart->is_empty() ) {
			$cart = WC()->cart->get_cart();
			foreach ( $cart as $cart_item_key => $cart_item ) {

				if ( isset( $cart_item['composite_parent'] ) ) {
					continue;
				}

				$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

				$form = array();
				if ( array_key_exists( 'tinvwl_formdata', $cart_item ) ) {
					$form = array_merge( $form, $cart_item['tinvwl_formdata'] );
				}
				$form = apply_filters( 'tinvwl_addtowishlist_prepare_form_cart', $form, $cart_item_key, $cart, $_product );
				wp_recursive_ksort( $form );
				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$added_results = self::save_cart_action_by_product( $_product, $cart_item['quantity'], $wishlist, $form );
					if ( 'exists' === $added_results ) {
						$exists[] = is_callable( array(
							$_product,
							'get_name'
						) ) ? $_product->get_name() : $_product->get_title();
					} elseif ( $added_results ) {
						$result[] = is_callable( array(
							$_product,
							'get_name'
						) ) ? $_product->get_name() : $_product->get_title();
						if ( tinv_get_option( 'add_to_wishlist_cart', 'remove_from_cart' ) ) {
							WC()->cart->remove_cart_item( $cart_item_key );
							$data['updatepage'] = true;
						}
					} else {
						$failed[] = is_callable( array(
							$_product,
							'get_name'
						) ) ? $_product->get_name() : $_product->get_title();
					}
				}
			}
		}
		if ( ! empty( $result ) ) {
			$data['msg'][]  = sprintf( _n( '%1$s has been successfully added to {wishlist_title}.', '%1$s have been successfully added to {wishlist_title}.', count( $result ), 'ti-woocommerce-wishlist-premium' ), wc_format_list_of_items( $result ) );
			$data['status'] = true;
		} elseif ( ! empty( $failed ) ) {
			$data['msg'][]  = sprintf( _n( '%s has not added to the {wishlist_title}.', '%s have not been added to the {wishlist_title}.', count( $failed ), 'ti-woocommerce-wishlist-premium' ), wc_format_list_of_items( $failed ) );
			$data['status'] = false;
		} elseif ( ! empty( $exists ) ) {
			$data['msg'][]  = sprintf( _n( '%s is already in {wishlist_title}.', '%s are already in {wishlist_title}.', count( $exists ), 'ti-woocommerce-wishlist-premium' ), wc_format_list_of_items( $exists ) );
			$data['status'] = false;
		}
		$data['wishlist_url'] = tinv_url_wishlist_default();
		if ( ! empty( $wishlist ) ) {
			$data['wishlist_url'] = tinv_url_wishlist( $wishlist['ID'] );
		}

		if ( $data['status'] && tinv_get_option( 'general', 'redirect' ) && tinv_get_option( 'page', 'wishlist' ) ) {
			$data['redirect'] = $data['wishlist_url'];
		}

		$data['icon'] = $data['status'] ? 'icon_big_heart_check' : 'icon_big_times';
		$data['msg']  = array_unique( $data['msg'] );
		$data['msg']  = implode( '<br>', $data['msg'] );
		if ( ! empty( $data['msg'] ) ) {
			$data['msg']      = tinvwl_message_placeholders( $data['msg'], null, $wishlist );
			$data['wishlist'] = $wishlist;
			$data['msg']      = tinv_wishlist_template_html( 'ti-addedtowishlist-dialogbox.php', apply_filters( 'tinvwl_addtowishlist_dialog_box', $data, $post ) );
		}
		$share_key      = false;
		$guest_wishlist = false;
		if ( ! is_user_logged_in() ) {
			$wl             = new TInvWL_Wishlist();
			$guest_wishlist = $wl->get_by_user_default();
		}
		if ( $guest_wishlist ) {
			$share_key = $guest_wishlist['share_key'];
		}

		$class                  = TInvWL_Public_AddToWishlist::instance();
		$data['wishlists_data'] = $class->get_wishlists_data( $share_key );

		$data = apply_filters( 'tinvwl_addtowishlist_return_ajax', $data, $post, ( array_key_exists( 'tinvwl_formdata', $cart_item ) ? $cart_item['tinvwl_formdata'] : array() ), $_product );

		wp_send_json( $data );
	}

	/**
	 * Add Product in Wishlist
	 *
	 * @param \WC_Product $product Product.
	 * @param integer $quantity Quantity Product.
	 * @param array|null $wishlist Wishlist Object.
	 * @param array $form POST data from form add to cart.
	 *
	 * @return mixed
	 */
	public static function save_cart_action_by_product( $product, $quantity = 1, $wishlist = null, $form = array() ) {
		if ( empty( $wishlist ) ) {
			return false;
		}

		if ( ! is_array( $form ) ) {
			$form = array();
		}
		$wlp = null;
		if ( 0 === $wishlist['ID'] ) {
			if ( tinv_get_option( 'general', 'guests' ) ) {
				$wl       = new TInvWL_Wishlist();
				$wishlist = $wl->add_sharekey_default();
				$wlp      = new TInvWL_Product( $wishlist );
			} else {
				return false;
			}
		} else {
			$wlp = new TInvWL_Product( $wishlist );
		}
		$product  = array(
			'product_id'        => $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id(),
			'product_variation' => $product->is_type( 'variation' ) ? $product->get_id() : 0,
			'product_type'      => $product->get_type(),
		);
		$quantity = ( 0 < $quantity ) ? $quantity : 1;

		$single_product          = ! tinv_get_option( 'general', 'quantity_func' );
		$product['product_type'] = apply_filters( 'tinvwl_addtowishlist_modify_type', $product['product_type'], $product );
		$product                 = apply_filters( 'tinvwl_addtowishlist_prepare', $product );
		switch ( $product['product_type'] ) {
			case 'variable' :
			case 'variation' :
				$variation_id = 0;
				if ( $product['product_variation'] ) {
					$variation_id = $product['product_variation'];
				}
				$check_adding   = false;
				$allowed_adding = false;
				if ( $single_product ) {
					$quantity     = 1;
					$check_adding = true;
				}
				if ( $check_adding ) {
					$allowed_adding = ! count( $wlp->get_wishlist( array(
						'product_id'   => $product['product_id'],
						'variation_id' => $variation_id,
						'external'     => false,
						'meta'         => $form,
					) ) );
				}
				if ( empty( $variation_id ) ) {
					return false;
				} else {
					if ( $check_adding && ! $allowed_adding ) {
						return 'exists';
					} elseif ( $wlp->add_product( apply_filters( 'tinvwl_addtowishlist_add', array(
						'product_id'   => $product['product_id'],
						'quantity'     => $quantity,
						'variation_id' => $variation_id,
					) ), apply_filters( 'tinvwl_addtowishlist_add_form', $form ) ) ) {
						do_action( 'tinvwl_changed_wishlist', 1, $wishlist, $product['product_id'], $variation_id, $quantity );

						return true;
					} else {
						return false;
					}
				}
			case 'simple' :
			default:
				$check_adding   = false;
				$allowed_adding = false;
				if ( $single_product ) {
					$quantity     = 1;
					$check_adding = true;
				}
				if ( $check_adding ) {
					$allowed_adding = ! count( $wlp->get_wishlist( array(
						'product_id' => $product['product_id'],
						'external'   => false,
						'meta'       => $form,
					) ) );
				}
				if ( $check_adding && ! $allowed_adding ) {
					return 'exists';
				} elseif ( $wlp->add_product( apply_filters( 'tinvwl_addtowishlist_add', array(
					'product_id' => $product['product_id'],
					'quantity'   => $quantity,
				) ), apply_filters( 'tinvwl_addtowishlist_add_form', $form ) ) ) {
					do_action( 'tinvwl_changed_wishlist', 1, $wishlist, $product['product_id'], 0, $quantity );

					return true;
				} else {
					return false;
				}
		}
	}
}
