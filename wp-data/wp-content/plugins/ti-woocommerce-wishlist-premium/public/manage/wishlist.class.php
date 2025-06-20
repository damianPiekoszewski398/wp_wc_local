<?php
/**
 * Manage wishlists shortcode
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Manage wishlists shortcode
 */
class TInvWL_Public_Manage_Wishlist {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $_name;
	/**
	 * This class
	 *
	 * @var \TInvWL_Public_Manage_Wishlist
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Public_Manage_Wishlist
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
		add_filter( 'tinvwl_wishlistmanage_item_name', array( $this, 'rename_filter' ), 10, 2 );
		add_action( 'tinvwl_wishlistmanage_item_privacy', array( $this, 'privacy_sel' ), 10, 2 );
		if ( tinv_get_option( 'general', 'my_account_endpoint' ) ) {
			add_filter( 'tinvwl_wishlist_privacy_types', '__return_empty_array', 9999 );
		}
	}


	/**
	 * Basic actions
	 *
	 * @param array $wishlists Wishlist object.
	 *
	 * @return boolean
	 */
	function wishlists_actions( $wishlists ) {
		$post = filter_input_array( INPUT_POST, array(
			'wishlist_pr'            => array(
				'filter' => FILTER_VALIDATE_INT,
				'flags'  => FILTER_FORCE_ARRAY,
			),
			'wishlist_privacy'       => array(
				'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
				'flags'  => FILTER_FORCE_ARRAY,
			),
			'wishlist_name'          => array(
				'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
				'flags'  => FILTER_FORCE_ARRAY,
			),
			'tinvwl-wishlist-remove' => FILTER_VALIDATE_INT,
			'tinvwl-action'          => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		) );
		if ( ! empty( $post['tinvwl-wishlist-remove'] ) ) {
			$remove = $post['tinvwl-wishlist-remove'];
			$wl     = new TInvWL_Wishlist( $this->_name );
			foreach ( $wishlists as $wishlist ) {
				if ( $remove === $wishlist['ID'] ) {
					if ( $wl->remove( $remove ) ) {
						wc_add_notice( sprintf( __( 'Successfully deleted wishlist "%s".', 'ti-woocommerce-wishlist-premium' ), $wishlist['title'] ) );
					}
					break;
				}
			}

			return false;
		}
		$action = filter_input( INPUT_POST, 'tinvwl-action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( empty( $action ) ) {
			return false;
		}
		do_action( 'tinvwl_action_' . $post['tinvwl-action'], $wishlists, $post['wishlist_pr'], $post['wishlist_privacy'], $post['wishlist_name'] ); // @codingStandardsIgnoreLine WordPress.NamingConventions.ValidHookName.UseUnderscores
	}


	/**
	 * Get wishlists privacy
	 *
	 * @return array
	 */

	public static function get_wishlists_privacy() {
		return apply_filters( 'tinvwl_wishlist_privacy_types', array(
			'public'  => __( 'Public', 'ti-woocommerce-wishlist-premium' ),
			'share'   => __( 'Share', 'ti-woocommerce-wishlist-premium' ),
			'private' => __( 'Private', 'ti-woocommerce-wishlist-premium' ),
		) );
	}


	/**
	 * Create privacy select for list wishlists
	 *
	 * @param string $name Name privacy.
	 * @param array $wishlist Wishlist object.
	 *
	 * @return string
	 */
	function privacy_sel( $name, $wishlist ) {
		$privacy = $this::get_wishlists_privacy();

		return TInvWL_Form::_select( sprintf( 'wishlist_privacy[%d]', $wishlist['ID'] ), $wishlist['status'], array( 'data-tinvwl-current-privacy' => $wishlist['status'] ), $privacy );
	}

	/**
	 * Create rename block for list wishlists
	 *
	 * @param string $name Name privacy.
	 * @param array $wishlist Wishlist object.
	 *
	 * @return string
	 */
	function rename_filter( $name, $wishlist ) {
		$data = array(
			'wishlist_name'  => $name,
			'wishlist_value' => $wishlist['title'],
			'wishlist_id'    => $wishlist['ID'],
		);

		return tinv_wishlist_template_html( 'ti-wishlist-manage-rename.php', $data );
	}

	/**
	 * Output page
	 *
	 * @param array $atts Array parameter for shortcode.
	 */
	function htmloutput( $atts ) {
		global $wpdb;

		if ( ! tinv_get_option( 'general', 'multi' ) ) {
			printf( '<p><a href="%s">%s</p><script type="text/javascript">window.location.href="%s"</script>', esc_attr( tinv_url_wishlist_default() ), esc_html( __( 'Return to Wishlist', 'ti-woocommerce-wishlist-premium' ) ), esc_attr( tinv_url_wishlist_default() ) );

			return false;
		}
		if ( ! is_user_logged_in() ) {
			return TInvWL_Public_Wishlist_Login::init();
		}
		TInvWL_Public_Manage_Buttons::init( $this->_name );

		$paged       = absint( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : $atts['paged'] );
		$this->paged = 1 < $paged ? $paged : 1;

		if ( 'yes' === $atts['show_navigation'] ) {
			$this->pages = ceil( count( TInvWL_Public_Wishlist_View::instance()->get_current_user_wishlists() ) / absint( $atts['lists_per_page'] ) );

			if ( 1 < $this->paged ) {
				add_action( 'tinvwl_pagenation_wishlistmanage', array( $this, 'page_prev' ) );
			}

			if ( 1 < $this->pages ) {
				add_action( 'tinvwl_pagenation_wishlistmanage', array( $this, 'pages' ) );
			}

			if ( $this->pages > $this->paged ) {
				add_action( 'tinvwl_pagenation_wishlistmanage', array( $this, 'page_next' ) );
			}
		}

		$wishlists = tinv_list_wishlist_user( apply_filters( 'tinvwl_wishlistmanage_query', array(
			'author' => get_current_user_id(),
			'count'  => absint( $atts['lists_per_page'] ),
			'offset' => absint( $atts['lists_per_page'] ) * ( $paged - 1 ),
		) ) );


		$_wishlists = $_counts = array();
		foreach ( $wishlists as $wishlist ) {
			$_wishlists[] = $wishlist['ID'];
		}

		$wlp    = new TInvWL_Product();
		$counts = $wlp->get( array(
			'external'    => false,
			'wishlist_id' => $_wishlists,
			'sql'         => 'SELECT SUM(`quantity`) AS `quantity`, `wishlist_id` FROM {table} t1 INNER JOIN ' . $wpdb->prefix . 'posts t2 on t1.product_id = t2.ID AND t2.post_status = "publish" WHERE {where} GROUP BY t1.wishlist_id',
		) );

		foreach ( $counts as $count ) {
			$_counts[ $count['wishlist_id'] ] = round( $count['quantity'], 2 );
		}

		foreach ( $wishlists as $key => $wishlist ) {
			$wishlists[ $key ]['count'] = array_key_exists( $wishlist['ID'], $_counts ) ? $_counts[ $wishlist['ID'] ] : 0;
		}

		$data = array(
			'wishlists' => $wishlists,
			'wl_paged'  => $this->paged,
		);
		tinv_wishlist_template( 'ti-wishlist-manage.php', $data );
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
		$paged = absint( $paged );
		$page  = apply_filters( 'wpml_object_id', tinv_get_option( 'page', 'manage' ), 'page', true );
		if ( empty( $page ) ) {
			return false;
		}
		if ( is_array( $style ) ) {
			$style = TInvWL_Form::__atrtostr( $style );
		}
		$link = get_permalink( $page );
		if ( get_option( 'permalink_structure' ) ) {
			$link .= 'page/' . $paged;
		} else {
			$link .= '&paged=' . $paged;
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

		$default = array(
			'lists_per_page'  => 10,
			'show_navigation' => 'yes',
			'paged'           => 1,
		);
		$atts    = shortcode_atts( $default, $atts );

		ob_start();
		$this->htmloutput( $atts );

		return ob_get_clean();
	}
}
