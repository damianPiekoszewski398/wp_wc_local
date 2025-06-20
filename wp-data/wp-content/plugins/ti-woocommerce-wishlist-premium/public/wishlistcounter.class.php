<?php
/**
 * Wishlist counter
 *
 * @since             1.4.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Drop down widget
 */
class TInvWL_Public_WishlistCounter {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	static $_name;
	/**
	 * Name for GET attribute action
	 *
	 * @var string
	 */
	/**
	 * Counter
	 *
	 * @var floatval
	 */
	private $counter;
	/**
	 * User wishlists
	 *
	 * @var array
	 */
	private $user_wishlists;
	/**
	 * Guest wishlist
	 *
	 * @var array
	 */
	private $guest_wishlist;
	static $_get_atribute = 'tinv-miniwishlist-action';
	/**
	 * This class
	 *
	 * @var \TInvWL_Public_WishlistCounter
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Public_WishlistCounter
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
		add_filter( 'tinvwl_after_mini_wishlist', array( __CLASS__, 'button_view_wishlist' ) );
		if ( ! tinv_get_option( 'general', 'multi' ) ) {
			add_filter( 'tinvwl_after_mini_wishlist', array( __CLASS__, 'button_all_to_cart' ) );
		}
		add_action( 'wp_loaded', array( __CLASS__, 'apply_action' ), 0 );
		if ( tinv_get_option( 'topline', 'menu' ) && ! is_admin() ) {
			add_filter( 'wp_get_nav_menu_items', array( $this, 'add_to_menu' ), 999, 3 );
		}
	}

	/**
	 * Add to menu wishlist link
	 *
	 * @param array $items An array of menu item post objects.
	 * @param object $menu The menu object.
	 * @param array $args An array of arguments used to retrieve menu item objects.
	 *
	 * @return array
	 */
	public function add_to_menu( $items, $menu, $args ) {
		if ( ! is_user_logged_in() && ! tinv_get_option( 'general', 'guests' ) ) {
			return $items;
		}

		$menu_cnt = count( $items ) + 1;
		$menu_ids = tinv_get_option( 'topline', 'menu' );

		if ( ! is_array( $menu_ids ) ) {
			$menu_ids = array( $menu_ids );
			$menu_ids = array_filter( $menu_ids );
		}

		foreach ( $menu_ids as $menu_id ) {

			if ( $menu_id == $menu->term_id && apply_filters( 'tinvwl_add_to_menu', true, $menu_id ) ) {

				$menu_order = tinv_get_option( 'topline', 'menu_order' ) ? tinv_get_option( 'topline', 'menu_order' ) : 100;

				// Item title.

				$show_icon   = (bool) tinv_get_option( 'topline', 'icon' );
				$icon_type   = tinv_get_option( 'topline', 'icon' );
				$icon_class  = ( $show_icon && tinv_get_option( 'topline', 'icon' ) ) ? 'top_wishlist-' . tinv_get_option( 'topline', 'icon' ) : '';
				$icon_style  = ( $show_icon && tinv_get_option( 'topline', 'icon' ) ) ? esc_attr( 'top_wishlist-' . tinv_get_option( 'topline', 'icon_style' ) ) : '';
				$icon_upload = tinv_get_option( 'topline', 'icon_upload' );

				$counter = tinv_get_option( 'topline', 'show_counter' ) ? '<span class="wishlist_products_counter_number"></span>' : '';

				$text = tinv_get_option( 'topline', 'show_text' ) ? apply_filters( 'tinvwl_wishlist_products_counter_text', tinv_get_option( 'topline', 'text' ) ) : '';

				$icon = '<span class="wishlist_products_counter ' . $icon_class . ' ' . $icon_style . ( empty( $text ) ? ' no-txt' : '' ) . ( 0 < $this->get_counter() ? ' wishlist-counter-with-products' : '' ) . '" >';

				if ( $icon_class && 'custom' === $icon_type && ! empty( $icon_upload ) ) {
					$icon .= sprintf( '<img src="%s" />', esc_url( $icon_upload ) );
				}

				$icon .= '</span>';

				$menu_title = apply_filters( 'tinvwl_wishlist_products_counter_menu_html', $icon . ' ' . $text . ' ' . $counter, $icon, $text, $counter );

				if ( $menu_title ) {

					if ( tinv_get_option( 'general', 'multi' ) && is_user_logged_in() ) {
						$wl_url = esc_url( tinv_url_manage_wishlists() );
					} else {
						$wl_url = esc_url( tinv_url_wishlist_default() );
					}

					$wishlist_item = (object) array(
						'ID'               => $menu_cnt + 2147480000,
						'object_id'        => apply_filters( 'wpml_object_id', tinv_get_option( 'page', 'wishlist' ), 'page', true ),
						'db_id'            => $menu_cnt + 2147480000,
						'title'            => $menu_title,
						'url'              => $wl_url,
						'menu_order'       => $menu_order,
						'menu_item_parent' => 0,
						'type'             => 'post',
						'post_parent'      => 0,
						'filter'           => 'raw',
						'target'           => '',
						'attr_title'       => '',
						'object'           => get_post_type( get_post( apply_filters( 'wpml_object_id', tinv_get_option( 'page', 'wishlist' ), 'page', true ) ) ),
						'classes'          => array(),
						'description'      => '',
						'xfn'              => '',
						'status'           => '',
					);

					foreach ( array_keys( $items ) as $key ) {

						if ( $items[ $key ]->menu_order > ( $menu_order - 1 ) ) {
							$items[ $key ]->menu_order = $items[ $key ]->menu_order + 1;
						}
					}

					if ( $menu_order < $menu_cnt ) {
						array_splice( $items, $menu_order - 1, 0, array( $wishlist_item ) );
					} else {
						$items[] = $wishlist_item;
					}
				}
			}
		}

		return $items;
	}

	/**
	 * Apply actions for mini wishlist
	 */
	public static function apply_action() {
		$action = filter_input( INPUT_GET, self::$_get_atribute, FILTER_DEFAULT );
		switch ( $action ) {
			case 'add-all-to-cart':
				$wishlist = tinv_wishlist_get();
				TInvWL_Public_Wishlist_Buttons::add_all( $wishlist );
				wp_safe_redirect( remove_query_arg( self::$_get_atribute ) );
				die();
				break;
			default:
				if ( ! empty( $action ) ) {
					do_action( 'tinvwl_mini_wishlist_action_' . $action );
					wp_safe_redirect( remove_query_arg( self::$_get_atribute ) );
					die();
				}
				break;
		}
	}

	/**
	 * Show button for view wishlist after mini wishlist
	 */
	public static function button_view_wishlist( $products ) {
		if ( empty( $products ) ) {
			return;
		}
		if ( tinv_get_option( 'general', 'multi' ) && is_user_logged_in() ) {
			echo sprintf( '<a href="%1$s" class="button tinvwl-button tinv-view-wishlist">%2$s</a>', esc_url( tinv_url_manage_wishlists() ), esc_html__( 'View My Wishlists', 'ti-woocommerce-wishlist-premium' ) );
		} else {
			echo sprintf( '<a href="%1$s" class="button tinvwl-button tinv-view-wishlist">%2$s</a>', esc_url( tinv_url_wishlist_default() ), esc_html__( 'View Wishlist', 'ti-woocommerce-wishlist-premium' ) );
		}
	}

	/**
	 * Show button for add all product to cart after mini wishlist
	 */
	public static function button_all_to_cart( $products ) {
		if ( empty( $products ) ) {
			return;
		}
		echo sprintf( '<a href="%1$s" class="button tinvwl-button tinv-add-all-to-cart">%2$s</a>', esc_url( add_query_arg( self::$_get_atribute, 'add-all-to-cart', wc_get_cart_url() ) ), tinv_get_option( 'table', 'text_add_all_to_cart' ) ); // WPCS: xss ok.
	}

	/**
	 * Output shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 */
	function htmloutput( $atts ) {
		$data = array(
			'icon'         => tinv_get_option( 'topline', 'icon' ),
			'icon_class'   => ( $atts['show_icon'] && tinv_get_option( 'topline', 'icon' ) ) ? 'top_wishlist-' . tinv_get_option( 'topline', 'icon' ) : '',
			'icon_style'   => ( $atts['show_icon'] && tinv_get_option( 'topline', 'icon' ) ) ? esc_attr( 'top_wishlist-' . tinv_get_option( 'topline', 'icon_style' ) ) : '',
			'icon_upload'  => tinv_get_option( 'topline', 'icon_upload' ),
			'text'         => $atts['show_text'] ? $atts['text'] : '',
			'counter'      => $atts['show_counter'],
			'drop_down'    => $atts['drop_down'],
			'show_counter' => $atts['show_counter'],
			'use_link'     => $atts['link'],
			'link'         => ( tinv_get_option( 'general', 'multi' ) && is_user_logged_in() ) ? get_permalink( apply_filters( 'wpml_object_id', tinv_get_option( 'page', 'manage' ), 'page', true ) ) : ( ( tinv_get_option( 'general', 'require_login' ) && ! is_user_logged_in() ) ? wc_get_page_permalink( 'myaccount' ) : tinv_url_wishlist_default() ),
		);
		tinv_wishlist_template( 'ti-wishlist-product-counter.php', $data );
	}


	function get_counter() {
		return $this->counter ? $this->counter : $this->counter();
	}

	function get_user_wishlists() {
		return $this->user_wishlists ? $this->user_wishlists : $this->user_wishlists();
	}

	function get_guest_wishlist() {
		return $this->guest_wishlist ? $this->guest_wishlist : $this->guest_wishlist();
	}

	function guest_wishlist() {
		$wl                   = new TInvWL_Wishlist();
		$wishlist             = $wl->get_by_sharekey_default();
		$this->guest_wishlist = $wishlist;

		return $wishlist;
	}

	function user_wishlists() {
		$wl        = new TInvWL_Wishlist();
		$wishlists = array();
		if ( tinv_get_option( 'general', 'multi' ) ) {
			$wishlists = $wl->get_by_user();
		} else {
			$wishlists[] = $wl->add_user_default();
		}
		$_wishlists = array();
		foreach ( $wishlists as $key => $wishlist ) {
			$id                = $wishlist['ID'];
			$wishlist['url']   = tinv_url_wishlist_by_key( $wishlist['share_key'] );
			$_wishlists[ $id ] = $wishlist;
		}
		$this->user_wishlists = $_wishlists;

		return $_wishlists;
	}


	/**
	 * Get count product in all wishlist
	 *
	 * @return float
	 */
	public function counter() {
		global $wpdb;
		$count = 0;
		$wl    = new TInvWL_Wishlist();
		if ( is_user_logged_in() ) {
			$_wishlists = $this->get_user_wishlists();
			$wlp        = new TInvWL_Product();
			$counts     = $wlp->get( array(
				'external'    => false,
				'wishlist_id' => array_keys( $_wishlists ),
				'sql'         => sprintf( 'SELECT %s(`quantity`) AS `quantity` FROM {table} t1 INNER JOIN ' . $wpdb->prefix . 'posts t2 on t1.product_id = t2.ID AND t2.post_status IN ("publish","private") WHERE {where}', ( tinv_get_option( 'general', 'quantity_func' ) ? 'SUM' : 'COUNT' ) ),
			) );
			$counts     = array_shift( $counts );
			$count      = floatval( $counts['quantity'] );
		} else {
			$wishlist = $this->get_guest_wishlist();
			if ( ! empty( $wishlist ) ) {
				$wishlist = array_shift( $wishlist );
				$wlp      = new TInvWL_Product( $wishlist );
				$counts   = $wlp->get_wishlist( array(
					'external' => false,
					'sql'      => sprintf( 'SELECT %s(`quantity`) AS `quantity` FROM {table} t1 INNER JOIN ' . $wpdb->prefix . 'posts t2 on t1.product_id = t2.ID AND t2.post_status IN ("publish","private") WHERE {where}', ( tinv_get_option( 'general', 'quantity_func' ) ? 'SUM' : 'COUNT' ) ),
				) );
				$counts   = array_shift( $counts );
				$count    = floatval( $counts['quantity'] );
			}
		}

		$this->counter = $count ? round( $count, 2 ) : ( tinv_get_option( 'topline', 'hide_zero_counter' ) ? false : 0 );

		return $this->counter;
	}

	/**
	 * Show mini wishlist.
	 *
	 * @param array $args arguments for show mini wishlist.
	 */
	public function mini_wishlist( $args = array() ) {
		$defaults              = array(
			'list_class'    => '',
			'show_wishlist' => tinv_get_option( 'topline', 'show_wishlist' ),
			'count_product' => tinv_get_option( 'topline', 'drop_down_count_product' ),
			'remove'        => tinv_get_option( 'topline', 'remove' ),
			'add_to_cart'   => tinv_get_option( 'topline', 'add_to_cart' ),
		);
		$args                  = wp_parse_args( $args, $defaults );
		$args['count_product'] = floatval( $args['count_product'] );
		if ( ! $args['count_product'] ) {
			$args['count_product'] = 10;
		}
		if ( ! tinv_get_option( 'general', 'multi' ) ) {
			$args['show_wishlist'] = 'off';
		}
		$products_count = $this->get_counter();
		if ( is_user_logged_in() ) {
			$wishlists = $this->get_user_wishlists();
			$wlp       = new TInvWL_Product();
			$data      = array(
				'count'       => $args['count_product'],
				'order'       => 'DESC',
				'order_by'    => 'date',
				'wishlist_id' => array_keys( $wishlists ),
				'sql'         => 'SELECT {field}, GROUP_CONCAT(`wishlist_id`) AS `wishlist_id`, COUNT(`ID`) AS `counts`, SUM(`quantity`) AS `quantity_new` FROM `{table}` WHERE {where} GROUP BY `product_id`,`variation_id`,`formdata` ORDER BY `{order_by}` {order} LIMIT {offset},{count}',
			);
			$products  = $wlp->get( $data );
			foreach ( $products as $key => $product ) {
				$product['wishlist_id']  = explode( ',', $product['wishlist_id'] );
				$product['wishlists']    = array();
				$product['quantity_new'] = round( $product['quantity_new'], 2 );
				foreach ( $product['wishlist_id'] as $wishlist_id ) {
					if ( array_key_exists( $wishlist_id, $wishlists ) ) {
						$product['wishlists'][ $wishlist_id ] = $wishlists[ $wishlist_id ];
					}
				}
				$products[ $key ] = $product;
			}
		} else {
			$args['show_wishlist'] = 'off';
			$wishlist              = $this->get_guest_wishlist();
			$products              = array();
			if ( ! empty( $wishlist ) ) {
				$wishlist = array_shift( $wishlist );
				$wlp      = new TInvWL_Product( $wishlist );
				$products = $wlp->get_wishlist( array(
					'count'    => $args['count_product'],
					'order'    => 'DESC',
					'order_by' => 'date',
				) );
			}
			foreach ( $products as $key => $product ) {
				$product['quantity_new'] = $product['quantity'];
				$product['counts']       = 1;
				$products[ $key ]        = $product;
			}
		} // End if().

		$default_wishlist = array(
			'title' => apply_filters( 'tinvwl_default_wishlist_title', tinv_get_option( 'general', 'default_title' ) ),
			'url'   => tinv_url_wishlist_default(),
		);

		foreach ( $products as $key => $product ) {
			if ( ! array_key_exists( 'wishlists', $product ) && empty( $product['wishlists'] ) ) {
				$product['wishlists'][0] = $default_wishlist;
			}
			if ( ! tinv_get_option( 'general', 'quantity_func' ) ) {
				$product['quantity'] = $product['counts'];
			} else {
				$product['quantity'] = $product['quantity_new'];
			}
			$products[ $key ] = $product;
		}

		tinv_wishlist_template( 'ti-miniwishlist.php', array(
			'args'           => $args,
			'products'       => $products,
			'subtotal_count' => $products_count,
		) );
	}

	/**
	 * Shortcode basic function
	 *
	 * @param array $atts Array parameter from shortcode.
	 *
	 * @return string
	 */
	function shortcode( $atts = array() ) {
		if ( ! is_user_logged_in() && ! tinv_get_option( 'general', 'guests' ) ) {
			return;
		}

		$default = array(
			'show_icon'    => (bool) tinv_get_option( 'topline', 'icon' ),
			'show_text'    => tinv_get_option( 'topline', 'show_text' ),
			'text'         => apply_filters( 'tinvwl_wishlist_products_counter_text', tinv_get_option( 'topline', 'text' ) ),
			'show_counter' => tinv_get_option( 'topline', 'show_counter' ),
			'drop_down'    => tinv_get_option( 'topline', 'drop_down' ),
			'link'         => tinv_get_option( 'topline', 'link' ),
		);
		$atts    = filter_var_array( shortcode_atts( $default, $atts ), array(
			'show_icon'    => FILTER_VALIDATE_BOOLEAN,
			'show_text'    => FILTER_VALIDATE_BOOLEAN,
			'show_counter' => FILTER_VALIDATE_BOOLEAN,
			'drop_down'    => FILTER_VALIDATE_BOOLEAN,
			'link'         => FILTER_VALIDATE_BOOLEAN,
			'text'         => FILTER_DEFAULT,
		) );
		ob_start();
		$this->htmloutput( $atts );

		return ob_get_clean();
	}
}
