<?php
/**
 * Wishlist shortcode
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Wishlist shortcode
 */
class TInvWL_Public_Wishlist_View {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $_name;

	/**
	 * List per page
	 *
	 * @var integer
	 */
	private $lists_per_page;

	/**
	 * Current wishlist
	 *
	 * @var array
	 */
	private $current_wishlist;

	/**
	 * Current products
	 *
	 * @var array
	 */
	public $current_products_query;

	/**
	 * Social image
	 *
	 * @var string
	 */
	public $social_image;

	/**
	 * Sorting feature
	 *
	 * @var bool
	 */
	public $sort;

	/**
	 * Total pages
	 *
	 * @var int
	 */
	public $pages;

	/**
	 * Wishlist full URL
	 *
	 * @var string
	 */
	public $wishlist_url;

	/**
	 * Wishlist product helper
	 *
	 * @var TInvWL_Product
	 */
	public $wishlist_products_helper;

	/**
	 * Current user wishlists
	 *
	 * @var array
	 */
	private $current_user_wishlists;

	/**
	 * This class
	 *
	 * @var \TInvWL_Public_Wishlist_View
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Public_Wishlist_View
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
		$this->sort  = tinv_get_option( 'table', 'sort' );
		$this->define_hooks();
	}

	/**
	 * Defined shortcode and hooks
	 */
	function define_hooks() {
		add_action( 'template_redirect', array( $this, 'login_redirect' ) );

		add_action( 'wp_loaded', array( $this, 'login_post_redirect' ), 19 );

		add_action( 'wp_head', array( $this, 'add_meta_tags' ), 1 );

		add_action( 'tinvwl_before_wishlist', array( $this, 'wishlist_header' ) );
		add_action( 'tinvwl_before_wishlist', array( 'TInvWL_Public_Wishlist_Login', 'init' ), 20 );

		if ( ! tinv_get_option( 'general', 'my_account_endpoint' ) ) {
			add_action( 'tinvwl_after_wishlist', array( 'TInvWL_Public_Wishlist_Social', 'init' ), 10, 3 );
			add_action( 'tinvwl_after_wishlist', array( 'TInvWL_Public_Wishlist_Subscribe', 'init' ), 5 );
		}
		add_action( 'tinvwl_after_wishlist', array( 'TInvWL_Public_Wishlist_Estimate', 'init' ), 5 );

		add_action( 'tinvwl_wishlist_item_move', array( $this, 'product_move' ), 10, 4 );

		add_filter( 'tinvwl_wishlist_item_url', array( $this, 'add_argument' ), 10, 3 );
		add_filter( 'tinvwl_wishlist_item_action_add_to_cart', array( $this, 'product_allow_add_to_cart' ), 10, 3 );
		add_filter( 'tinvwl_wishlist_item_add_to_cart', array( $this, 'external_text' ), 10, 3 );
		add_filter( 'tinvwl_wishlist_item_add_to_cart', array( $this, 'variable_text' ), 10, 3 );
		add_action( 'tinvwl_after_wishlist_table', array( $this, 'get_per_page' ) );

		if ( tinv_get_option( 'navigation', 'in_title' ) ) {
			add_action( 'tinvwl_in_title_wishlist', array( $this, 'wishlist_navigation' ) );
		}
		if ( tinv_get_option( 'navigation', 'after_table' ) ) {
			add_action( 'tinvwl_after_wishlist', array( $this, 'wishlist_navigation' ) );
		}

		add_action( 'tinvwl_before_wishlist_template', array( $this, 'refresh_wishlist_after_action' ) );
	}

	/**
	 * Redirect back after successful login.
	 */
	public function login_post_redirect() {
		$nonce_value = isset( $_REQUEST['woocommerce-login-nonce'] ) ? $_REQUEST['woocommerce-login-nonce'] : ( isset( $_REQUEST['_wpnonce'] ) ? $_REQUEST['_wpnonce'] : '' );
		if ( ! empty( $_POST['login'] ) && wp_verify_nonce( $nonce_value, 'woocommerce-login' ) && ! empty( $_GET['tinvwl_redirect'] ) ) {
			$_POST['redirect'] = $_GET['tinvwl_redirect']; // Force WC Login form handler to do redirect.
		}
	}

	/**
	 * Redirect guests to login page.
	 */
	public function login_redirect() {
		if ( ! is_page( wc_get_page_id( 'myaccount' ) ) && is_page( apply_filters( 'wpml_object_id', tinv_get_option( 'page', 'wishlist' ), 'page', true ) ) && ! is_user_logged_in() && tinv_get_option( 'general', 'require_login' ) ) {
			$full_link = get_permalink();
			$share_key = get_query_var( 'tinvwlID', null );
			if ( ! empty( $share_key ) ) {
				if ( get_option( 'permalink_structure' ) ) {
					if ( ! preg_match( '/\/$/', $full_link ) ) {
						$full_link .= '/';
					}
					$full_link .= $share_key . '/';
				} else {
					$full_link = add_query_arg( 'tinvwlID', $share_key, $full_link );
				}
			}
			wp_safe_redirect( add_query_arg( 'tinvwl_redirect', rawurlencode( $full_link ), wc_get_page_permalink( 'myaccount' ) ) );
			exit;
		}
	}

	/**
	 * Change Text for external product
	 *
	 * @param string $text Text for button add to cart.
	 * @param array $wl_product Wishlist Product.
	 * @param WC_Product $_product Product.
	 *
	 * @return string
	 */
	function external_text( $text, $wl_product, $_product ) {
		global $product;
		// store global product data.
		$_product_tmp = $product;
		// override global product data.
		$product = $_product;

		if ( 'external' === $product->get_type() ) {
			$text = $product->single_add_to_cart_text();

			// restore global product data.
			$product = $_product_tmp;
		}

		return $text;
	}

	/**
	 * Change Text for variable product that requires to select some variations.
	 *
	 * @param string $text Text for button add to cart.
	 * @param array $wl_product Wishlist Product.
	 * @param WC_Product $_product Product.
	 *
	 * @return string
	 */
	function variable_text( $text, $wl_product, $_product ) {
		global $product;
		// store global product data.
		$_product_tmp = $product;
		// override global product data.
		$product = $_product;
		if ( apply_filters( 'tinvwl_product_add_to_cart_need_redirect', false, $product, $product->get_permalink(), $wl_product )
			 && in_array( $product->get_type(), array(
				'variable',
				'variable-subscription',
			) ) ) {

			$text = $product->add_to_cart_text();

			// restore global product data.
			$product = $_product_tmp;
		}

		return $text;
	}

	/**
	 * Add analytics argument for url
	 *
	 * @param string $url Product url.
	 * @param array $wl_product Wishlist product.
	 * @param WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function add_argument( $url, $wl_product, $product ) {
		return add_query_arg( 'tiwp', $wl_product['ID'], $url );
	}

	/**
	 * Get current wishlist
	 *
	 * @return array
	 */
	function get_current_wishlist() {
		if ( empty( $this->current_wishlist ) ) {
			$this->current_wishlist = apply_filters( 'tinvwl_get_current_wishlist', tinv_wishlist_get() );
		}

		return $this->current_wishlist;
	}

	/**
	 * Get current user wishlists
	 *
	 * @return array
	 */
	function get_current_user_wishlists() {
		if ( empty( $this->current_user_wishlists ) ) {
			$wishlists = array();
			$wl        = new TInvWL_Wishlist();
			if ( is_user_logged_in() ) {
				if ( tinv_get_option( 'general', 'multi' ) ) {
					$wishlists = $wl->get_by_user();
				} else {
					$wishlists = $wl->get_by_user_default();
				}
			} elseif ( tinv_get_option( 'general', 'guests' ) ) {
				$wishlists = $wl->get_by_sharekey_default();
			}
			$wishlists = array_filter( $wishlists );
			if ( ! empty( $wishlists ) ) {
				$_wishlists = array();

				foreach ( $wishlists as $key => $wishlist ) {
					if ( is_array( $wishlist ) && array_key_exists( 'ID', $wishlist ) ) {
						$_wishlists[ $key ]        = $wishlist;
						$_wishlists[ $key ]['url'] = tinv_url_wishlist_by_key( $wishlist['share_key'] );
					}
				}
				$wishlists = $_wishlists;
			}

			$this->current_user_wishlists = apply_filters( 'tinvwl_get_current_user_wishlists', $wishlists );
		}

		return $this->current_user_wishlists;
	}

	/**
	 * Get current products query
	 *
	 * @return array
	 */
	function get_current_products_query() {
		if ( ! is_array( $this->current_products_query ) ) {
			return false;
		}

		return $this->current_products_query;
	}

	/**
	 * Get current products from wishlist
	 *
	 * @param array $wishlist Wishlist object.
	 * @param boolean $external Get woocommerce product info.
	 * @param integer $lists_per_page Count per page.
	 *
	 * @return array
	 */
	function get_current_products( $wishlist = null, $external = true, $lists_per_page = null, $paged = 1 ) {
		if ( empty( $wishlist ) || $wishlist === $this->get_current_wishlist() ) {
			$wishlist = $this->get_current_wishlist();

			if ( ! $this->wishlist_products_helper ) {
				$wlp = null;
				if ( isset( $wishlist['ID'] ) && 0 === $wishlist['ID'] ) {
					$wlp = TInvWL_Product_Local::instance();
				} else {
					$wlp = new TInvWL_Product( $wishlist );
				}
				$this->wishlist_products_helper = $wlp;
			} else {
				$wlp = $this->wishlist_products_helper;
			}

		} else {
			$wlp = null;
			if ( isset( $wishlist['ID'] ) && 0 === $wishlist['ID'] ) {
				$wlp = TInvWL_Product_Local::instance();
			} else {
				$wlp = new TInvWL_Product( $wishlist );
			}
		}

		if ( empty( $wlp ) ) {
			return array();
		}
		if ( ! $lists_per_page ) {
			$lists_per_page = tinv_get_option( 'table', 'per_page' );
		}
		$paged        = absint( get_query_var( 'wl_paged' ) ? get_query_var( 'wl_paged' ) : $paged );
		$this->pages  = ceil( absint( $wlp->get_wishlist( array(
				'count'    => 9999999,
				'external' => false,
			), true ) ) / absint( $lists_per_page ) );
		$this->paged  = $this->pages < $paged ? $this->pages : $paged;
		$product_data = array(
			'count'    => absint( $lists_per_page ),
			'offset'   => absint( $lists_per_page ) * ( absint( $this->paged ) - 1 ),
			'external' => $external,
			'order_by' => 'date',
			'order'    => 'DESC',
		);

		if ( $this->sort ) {
			$product_data['order_by'] = 'order';
			$product_data['order']    = 'ASC';
		}

		$product_data = apply_filters( 'tinvwl_before_get_current_product', $product_data );
		$products     = $wlp->get_wishlist( $product_data );
		$products     = apply_filters( 'tinvwl_after_get_current_product', $products );
		if ( tinv_get_option( 'table', 'per_page' ) === absint( $lists_per_page ) ) {
			$this->current_products_query = $products;
		}

		return $products;
	}

	/**
	 * Allow show button add to cart
	 *
	 * @param boolean $allow Settings flag.
	 * @param array $wlproduct Wishlist Product.
	 * @param WC_Product $product Product.
	 *
	 * @return boolean
	 */
	function product_allow_add_to_cart( $allow, $wlproduct, $product ) {
		if ( ! $allow ) {
			return false;
		}

		return ( $product->is_purchasable() || 'external' === $product->get_type() ) && ( $product->is_in_stock() || $product->backorders_allowed() );
	}

	/**
	 * Preare product move
	 *
	 * @param string $html Button html.
	 * @param array $wl_product Product object.
	 * @param object $product Woocommerce product info.
	 * @param array $wishlist Wishlist object.
	 *
	 * @return string
	 */
	function product_move( $html, $wl_product, $product, $wishlist ) {
		if ( ! $wishlist['is_owner'] || ! tinv_get_option( 'general', 'multi' ) ) {
			return '';
		}
		$wishlist_id = $wishlist['ID'];
		$wishlists   = $this->get_current_user_wishlists();
		foreach ( $wishlists as $key => $wishlist ) {
			$wishlist          = array(
				'ID'    => $wishlist['ID'],
				'title' => $wishlist['title'],
				'url'   => $wishlist['url'],
				'hide'  => ( $wishlist_id === $wishlist['ID'] ),
			);
			$wishlists[ $key ] = $wishlist;
		}
		$html = preg_replace( '/^<button /i', sprintf( '<button data-tinv-wl="%d" data-tinv-wl-list="%s" data-tinv-wl-producttype="%s" data-tinv-wl-product="%d" data-tinv-wl-productvariation="%d" data-tinv-wl-product-wid="%d" ', $wishlist_id, htmlspecialchars( wp_json_encode( $wishlists ) ), $product->get_type(), ( ( $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id() ) ), ( $product->is_type( 'variation' ) ? $product->get_id() : 0 ), $wl_product['ID'] ), $html );
		$html .= tinv_wishlist_template_html( 'ti-wishlist-move-dialogbox.php', array() );

		return $html;
	}

	/**
	 * Create social meta tags
	 */
	function add_meta_tags() {
		if ( is_page( apply_filters( 'wpml_object_id', tinv_get_option( 'page', 'wishlist' ), 'page', true ) ) ) {
			$wishlist = $this->get_current_wishlist();
			if ( $wishlist && 0 < $wishlist['ID'] ) {
				$this->wishlist_url = tinv_url_wishlist( $wishlist['share_key'] );
				if ( 'private' !== $wishlist['status'] && tinv_get_option( 'social', 'facebook' ) ) {
					if ( is_user_logged_in() ) {
						$user = get_user_by( 'id', $wishlist['author'] );
						if ( $user && $user->exists() ) {
							$user_name = trim( sprintf( '%s %s', $user->user_firstname, $user->user_lastname ) );
							$user      = @$user->display_name; // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
						} else {
							$user_name = '';
							$user      = '';
						}
					} else {
						$user_name = '';
						$user      = '';
					}

					if ( is_array( $this->get_current_products_query() ) ) {
						$products = $this->current_products_query;
					} else {
						$products = $this->get_current_products( $wishlist, true );
					}

					$products_title = array();
					foreach ( $products as $product ) {
						if ( ! empty( $product ) && ! empty( $product['data'] ) ) {
							$title = is_callable( array(
								$product['data'],
								'get_name'
							) ) ? $product['data']->get_name() : $product['data']->get_title();
							if ( ! in_array( $title, $products_title ) ) {
								$products_title[] = $title;
							}
						}
					}
					$product = array_shift( $products );
					$image   = '';
					if ( ! empty( $product ) && ! empty( $product['data'] ) ) {
						list( $image ) = wp_get_attachment_image_src( $product['data']->get_image_id(), 'full' );
					}

					$this->social_image = $image;

					$meta = apply_filters( 'tinvwl_social_header_meta', array(
						'url'         => $this->wishlist_url,
						'type'        => 'product.group',
						'title'       => sprintf( __( '%1$s by %2$s', 'ti-woocommerce-wishlist-premium' ), $wishlist['title'], ( empty( $user_name ) ? $user : $user_name ) ),
						'description' => implode( ', ', $products_title ),
						'image'       => $image,
					) );

					foreach ( $meta as $name => $content ) {
						echo sprintf( '<meta property="og:%s" content="%s" />', esc_attr( $name ), esc_attr( $content ) );
					}
					echo "\n";
				}
			} // End if().
		} // End if().
	}


	/**
	 * Output page
	 *
	 * @param array $atts Array parameter for shortcode.
	 *
	 * @return mixed
	 */
	function htmloutput( $atts ) {
		if ( $atts['sharekey'] ) {
			$wl                     = new TInvWL_Wishlist( $this->_name );
			$wishlist               = $wl->get_by_share_key( $atts['sharekey'] );
			$this->current_wishlist = $wishlist;
		} else {
			$wishlist = $this->get_current_wishlist();
		}

		if ( empty( $wishlist ) ) {
			$id = get_query_var( 'tinvwlID', null );
			if ( empty( $id ) && ( is_user_logged_in() || ! tinv_get_option( 'general', 'require_login' ) ) ) {
				return $this->wishlist_empty( array(), array(
					'ID'        => '',
					'author'    => get_current_user_id(),
					'title'     => apply_filters( 'tinvwl_default_wishlist_title', tinv_get_option( 'general', 'default_title' ) ),
					'status'    => 'private',
					'type'      => 'default',
					'share_key' => '',
				) );
			}

			if ( tinv_get_option( 'general', 'require_login' ) ) {
				return TInvWL_Public_Wishlist_Login::init();
			}

			return $this->wishlist_null();
		}

		if ( 'private' === $wishlist['status'] && ! $wishlist['is_owner'] && ! current_user_can( 'tinvwl_general_settings' ) ) {
			if ( ! is_user_logged_in() && tinv_get_option( 'general', 'login_notice' ) ) {
				return TInvWL_Public_Wishlist_Login::init();
			} else {
				return $this->wishlist_null();
			}
		}
		if ( ! in_array( $wishlist['type'], apply_filters( 'tinvwl_wishlist_type_exclusion', array( 'default' ) ) ) && ! tinv_get_option( 'general', 'multi' ) ) {
			if ( $wishlist['is_owner'] ) {
				printf( '<p><a href="%s">%s</p><script type="text/javascript">window.location.href="%s"</script>', esc_attr( tinv_url_wishlist_default() ), esc_html( __( 'Return to Wishlist', 'ti-woocommerce-wishlist-premium' ) ), esc_attr( tinv_url_wishlist_default() ) );

				return false;
			} else {
				return $this->wishlist_null();
			}
		}

		$this->lists_per_page = absint( $atts['lists_per_page'] );
		$paged                = absint( get_query_var( 'wl_paged' ) ? get_query_var( 'wl_paged' ) : $atts['paged'] );

		if ( tinv_get_option( 'table', 'per_page' ) === $this->lists_per_page && is_array( $this->get_current_products_query() ) && ! $atts['sharekey'] ) {
			$products = $this->current_products_query;
		} else {
			$products = $this->get_current_products( $wishlist, true, $this->lists_per_page, $paged );
		}

		$wla = new TInvWL_Analytics( $wishlist, $this->_name );
		$wla->view_products( $wishlist, $wishlist['is_owner'] );

		foreach ( $products as $key => $product ) {

			if ( tinv_get_option( 'product_table', 'subtotal' ) ) {
				$products[ $key ] ['subtotal'] = $this->get_subtotal_price( $product );
			}

			if ( ! isset( $product['data'] ) ) {
				unset( $products[ $key ] );
			}
		}
		if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			do_action( 'tinvwl_before_wishlist_template', $wishlist );
		}
		if ( empty( $products ) ) {

			$this->pages = 0;

			return $this->wishlist_empty( $products, $wishlist );
		}

		$wishlist_table_row                     = tinv_get_option( 'product_table' );
		$wishlist_table_row['text_add_to_cart'] = apply_filters( 'tinvwl_add_to_cart_text', tinv_get_option( 'product_table', 'text_add_to_cart' ) );

		$data = array(
			'products'           => $products,
			'wishlist'           => $wishlist,
			'wishlist_table'     => tinv_get_option( 'table' ),
			'wishlist_table_row' => $wishlist_table_row,
			'wl_paged'           => $this->paged,
			'wl_per_page'        => $this->lists_per_page,
			'sort'               => $this->sort,
			'qty'                => tinv_get_option( 'general', 'quantity_func' ),
		);
		if ( tinv_get_option( 'product_table', 'total' ) ) {
			$data['total'] = $this->get_total_price( $wishlist['ID'] );
		}
		$data['wishlist_table_row']['move'] = isset( $data['wishlist_table_row']['move'] ) && $data['wishlist_table_row']['move'] && tinv_get_option( 'general', 'multi' ) && is_user_logged_in();
		TInvWL_Public_Wishlist_Buttons::init( $this->_name );
		if ( 1 < $this->paged ) {
			add_action( 'tinvwl_pagenation_wishlist', array( $this, 'page_prev' ) );
		}

		if ( 1 < $this->pages ) {
			add_action( 'tinvwl_pagenation_wishlist', array( $this, 'pages' ) );
		}
		if ( $this->pages > $this->paged ) {
			add_action( 'tinvwl_pagenation_wishlist', array( $this, 'page_next' ) );
		}
		if ( $wishlist['is_owner'] ) {
			tinv_wishlist_template( 'ti-wishlist.php', $data );
		} else {
			if ( class_exists( 'WC_Catalog_Visibility_Options' ) ) {
				global $wc_cvo;
				if ( 'secured' === $wc_cvo->setting( 'wc_cvo_atc' && isset( $data['wishlist_table_row']['add_to_cart'] ) ) ) {
					unset( $data['wishlist_table_row']['add_to_cart'] );
				}
				if ( 'secured' === $wc_cvo->setting( 'wc_cvo_prices' && isset( $data['wishlist_table_row']['colm_price'] ) ) ) {
					unset( $data['wishlist_table_row']['colm_price'] );
				}
			}
			$data['wishlist_table_row']['colm_quantity'] = isset( $data['wishlist_table_row']['colm_quantity'] ) ? $data['wishlist_table_row']['colm_quantity'] : "";
			tinv_wishlist_template( 'ti-wishlist-user.php', $data );
		}
	}

	/**
	 * Not Found Wishlist
	 *
	 * @param array $wishlist Wishlist object.
	 */
	function wishlist_null( $wishlist = array() ) {
		$data = array(
			'wishlist' => $wishlist,
		);
		tinv_wishlist_template( 'ti-wishlist-null.php', $data );
	}

	/**
	 * Empty Wishlist
	 *
	 * @param array $products Products wishlist.
	 * @param array $wishlist Wishlist object.
	 */
	function wishlist_empty( $products = array(), $wishlist = array() ) {
		$data = array(
			'products'       => $products,
			'wishlist'       => $wishlist,
			'wishlist_table' => tinv_get_option( 'table' ),
		);
		tinv_wishlist_template( 'ti-wishlist-empty.php', $data );
	}

	/**
	 * Header Wishlist
	 *
	 * @param array $wishlist Wishlist object.
	 */
	function wishlist_header( $wishlist ) {

		$data = array(
			'wishlist' => $wishlist,
		);
		tinv_wishlist_template( 'ti-wishlist-header.php', $data );
	}

	/**
	 * Navigation Wishlist
	 *
	 * @param array $wishlist Wishlist object.
	 */
	function wishlist_navigation( $wishlist ) {
		$data = array(
			'wishlist'    => $wishlist,
			'button_type' => tinv_get_option( 'navigation', 'type' ),
			'button_icon' => tinv_get_option( 'navigation', 'icon' ),
		);
		add_filter( 'tinvwl_button_create_wishlist', array( $this, 'create_button' ) );
		add_filter( 'tinvwl_button_notifications_wishlist', array( $this, 'notifications_button' ) );
		tinv_wishlist_template( 'ti-wishlist-navigation.php', $data );
	}

	/**
	 * Change button "Create Wishlist"
	 *
	 * @return string
	 */
	function create_button() {
		return sprintf( '<a href="javascript:void(0)" class="%s %s tinv-modal-btn" name="tinvwl-action">%s</a>', ( 'button' === tinv_get_option( 'navigation', 'type' ) ? 'button' : '' ), ( tinv_get_option( 'navigation', 'icon' ) ? '' : 'tinvwl-no-icon' ), sprintf( '<i class="ftinvwl ftinvwl-plus"></i><span class="tinvwl-txt">%s</span>', __( 'Create', 'ti-woocommerce-wishlist-premium' ) )
		);
	}

	/**
	 * Change button "Wishlist Notifications"
	 *
	 * @return string
	 */
	function notifications_button() {
		return sprintf( '<a href="javascript:void(0)" class="%s %s tinv-modal-btn tinvwl-button-notifications" name="tinvwl-action">%s</a>', ( 'button' === tinv_get_option( 'navigation', 'type' ) ? 'button' : '' ), ( tinv_get_option( 'navigation', 'icon' ) ? '' : 'tinvwl-no-icon' ), sprintf( '<i class="ftinvwl ftinvwl-email"></i><span class="tinvwl-txt">%s</span>', __( 'Notifications', 'ti-woocommerce-wishlist-premium' ) )
		);
	}

	/**
	 * Prev page button
	 */
	function page_prev() {
		$paged = $this->paged;
		$paged = $this->pages < $paged ? $this->pages : $paged;
		$paged = 1 < $paged ? $paged - 1 : 0;
		$this->page( $paged, sprintf( '<i class="ftinvwl ftinvwl-chevron-left"></i><span>%s</span>', __( 'Previous Page', 'ti-woocommerce-wishlist-premium' ) ), array( 'class' => 'button tinv-prev' ) );
	}

	/**
	 * Pages
	 */
	function pages() {
		$paged = $this->paged;
		$paged = $this->pages < $paged ? $this->pages : $paged;
		if ( 1 === (int) $paged ) {
			echo '<span></span>';
		}

		echo '<span>' . $paged . '/' . $this->pages . '</span>';

		if ( (int) $this->pages === (int) $paged ) {
			echo '<span></span>';
		}
	}

	/**
	 * Next page button
	 */
	function page_next() {
		$paged = $this->paged;
		$paged = $this->pages < $paged ? $this->pages : $paged;
		$paged = 1 < $paged ? $paged + 1 : 2;
		$this->page( $paged, sprintf( '<span>%s</span><i class="ftinvwl ftinvwl-chevron-right"></i>', __( 'Next Page', 'ti-woocommerce-wishlist-premium' ) ), array( 'class' => 'button tinv-next' ) );
	}

	/**
	 * Page button
	 *
	 * @param integer $paged Index page.
	 * @param string $text Text button.
	 * @param style $style Style attribute.
	 */
	function page( $paged, $text, $style = array() ) {
		$paged    = absint( $paged );
		$wishlist = $this->get_current_wishlist();
		$link     = tinv_url_wishlist( $wishlist['share_key'], $paged, true );
		if ( is_array( $style ) ) {
			$style = TInvWL_Form::__atrtostr( $style );
		}
		printf( '<a href="%s" %s>%s</a>', esc_url( $link ), $style, $text ); // WPCS: xss ok.
	}

	/**
	 * Shortcode basic function
	 *
	 * @param array $atts Array parameter from shortcode.
	 *
	 * @return string
	 */
	function shortcode( $atts = array() ) {
		$default                = array(
			'lists_per_page' => tinv_get_option( 'table', 'per_page' ),
			'paged'          => 1,
			'sharekey'       => false,
		);
		$atts                   = shortcode_atts( $default, $atts );
		$atts['lists_per_page'] = $this->sort ? 999999 : $atts['lists_per_page'];

		ob_start();
		$this->htmloutput( $atts );

		return ob_get_clean();
	}

	/**
	 * Get per page items for buttons
	 */
	function get_per_page() {
		if ( ! empty( $this->lists_per_page ) ) {
			echo TInvWL_Form::_text( array( // WPCS: xss ok.
				'type' => 'hidden',
				'name' => 'lists_per_page',
			), $this->lists_per_page );
		}
	}

	/**
	 *  Get total price by wishlist ID.
	 *
	 * @param int $wishlist_id Wishlist ID.
	 *
	 * @return float
	 */
	public function get_total_price( $wishlist_id ) {
		global $wpdb;
		$sql     = 'SELECT *, price * quantity as total FROM ' . $wpdb->prefix . 'tinvwl_items WHERE wishlist_id = ' . $wishlist_id;
		$results = $wpdb->get_results( $sql, ARRAY_A );

		$total = 0;
		$wlp   = new TInvWL_Product();
		foreach ( $results as $_product ) {

			$_product['meta'] = array();
			if ( array_key_exists( 'formdata', $_product ) ) {
				$meta = $_product['formdata'];
				unset( $_product['formdata'] );

				$_product['meta'] = $wlp->prepare_retrun_meta( $meta, $_product['product_id'], $_product['variation_id'], $_product['quantity'] );
			}

			$id = ! empty( $_product['variation_id'] ) ? $_product['variation_id'] : $_product['product_id'];
			if ( $product = wc_get_product( $id ) ) {
				$total += (float) apply_filters( 'tinvwl_wishlist_item_price', $product->get_price(), $_product, $product, true ) * (float) $_product['quantity'];
			}
		}

		return $total;
	}

	/**
	 *  Get total price by wishlist ID.
	 *
	 * @param array $wl_product Wishlist product array.
	 *
	 * @return float
	 */
	public function get_subtotal_price( $wl_product ) {
		$id = ! empty( $wl_product['variation_id'] ) ? $wl_product['variation_id'] : $wl_product['product_id'];
		if ( $product = wc_get_product( $id ) ) {
			$subtotal = (float) apply_filters( 'tinvwl_wishlist_item_price', $product->get_price(), $wl_product, $product, true ) * (float) $wl_product['quantity'];
		}

		return $subtotal;
	}

	/**
	 * Outputs the script for refreshing wishlist.
	 */
	public function refresh_wishlist_after_action( $wishlist ) {

		if ( ! $wishlist['is_owner'] ) {
			return false;
		}

		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				// Generate a unique hash key for localStorage
				var hash_key = tinvwl_add_to_wishlist.hash_key + '_refresh';

				if (localStorage.getItem(hash_key) && '<?php echo $wishlist['share_key'] ?>' === localStorage.getItem(hash_key)) {
					localStorage.setItem(hash_key, '');
				}

				// Refresh the wishlist when storage changes in another tab
				$(window).on('storage', function (e) {
					if (
						e.originalEvent.key === hash_key &&
						'<?php echo $wishlist['share_key'] ?>' === e.originalEvent.newValue
					) {
						// Call the function to refresh the wishlist data
						$.fn.tinvwl_get_wishlist_data('refresh');
					}
				});
			});
		</script>
		<?php
	}

}
