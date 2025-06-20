<?php
/**
 * Search wishlist shortcode
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Search wishlist shortcode
 */
class TInvWL_Public_Search {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $_name;

	/**
	 * This class
	 *
	 * @var \TInvWL_Public_Search
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Public_Search
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
	 * Defined shortcode
	 */
	function define_hooks() {
	}

	/**
	 * Output page
	 *
	 * @param array $atts Array parameter for shortcode.
	 *
	 * @return boolean
	 */
	function htmloutput( $atts ) {
		$search = get_query_var( 'tiws' );
		tinv_wishlist_template( 'ti-wishlist-searchform.php', array(
			'search'                 => $search,
			'button_text'            => $atts['button_text'],
			'placeholder_input_text' => $atts['input_text'],
		) );

		if ( empty( $search ) ) {
			return '';
		}

		$default_wl = '';
		if ( false !== mb_strpos( mb_strtolower( apply_filters( 'tinvwl_default_wishlist_title', tinv_get_option( 'general', 'default_title' ) ) ), mb_strtolower( $search ) ) ) {
			$default_wl = " OR `title` = '' ";
		}

		$wl    = new TInvWL_Wishlist( $this->_name );
		$paged = get_query_var( 'paged', 1 );
		$paged = 1 < $paged ? $paged : 1;

		$data = array(
			'status'   => 'public',
			'order_by' => 'date',
			'order'    => 'DESC',
			'count'    => absint( $atts['lists_per_page'] ),
			'offset'   => absint( $atts['lists_per_page'] ) * ( $paged - 1 ),
		);
		global $wpdb;
		$sql_ids = "SELECT `A`.`ID` FROM `{$wpdb->users}` AS `A` INNER JOIN `{$wpdb->usermeta}` AS `B` ON `A`.`ID` = `B`.`user_id` WHERE `B`.`meta_key` IN ('first_name', 'last_name') AND (";
		$words   = explode( ' ', $search );
		$i       = 0;
		$sql     = '';
		foreach ( $words as $p ) {
			if ( $i == 0 ) {
				$sql_ids .= "CONCAT(`A`.`display_name`, ' ', `A`.`user_email`, ' ',`B`.`meta_value`) LIKE '%" . $p . "%' ";
				$sql     .= "CONCAT(`title`, ' ', `share_key`) LIKE '%" . $p . "%' ";
			} elseif ( $i > 0 ) {
				$sql_ids .= "OR CONCAT(`A`.`display_name`, ' ', `A`.`user_email`, ' ',`B`.`meta_value`) LIKE '%" . $p . "%' ";
				$sql     .= "OR CONCAT(`title`, ' ', `share_key`) LIKE '%" . $p . "%' ";
			}
			$i ++;
		}
		$sql_ids .= ") GROUP BY `A`.`ID`";

		$users_id = $wpdb->get_col( $sql_ids ); // @codingStandardsIgnoreLine WordPress.VIP.RestrictedVariables.user_meta
		if ( ! empty( $users_id ) ) {
			$default_wl .= ' OR `author` IN(' . implode( ',', $users_id ) . ') ';
		}
		if ( is_user_logged_in() ) {
			$data['sql'] = "SELECT * FROM `{table}` WHERE (`status`='public' OR `author`=" . get_current_user_id() . ") AND (" . $sql . "  {$default_wl})"; // @codingStandardsIgnoreLine WordPress.WP.PreparedSQL.NotPrepared
		} else {
			$data['sql'] = "SELECT * FROM `{table}` WHERE `status`='public' AND (" . $sql . " {$default_wl})"; // @codingStandardsIgnoreLine WordPress.WP.PreparedSQL.NotPrepared
		}
		if ( 'yes' === $atts['show_navigation'] ) {
			$pages = ceil( count( apply_filters( 'tinvwl_search_prepare_results', $wl->get( $data ) ) ) / absint( $atts['lists_per_page'] ) );

			if ( 1 < $paged ) {
				add_action( 'tinvwl_pagenation_wishlistpublic_table', array( $this, 'page_prev' ) );
			}

			if ( 1 < $pages ) {
				$this->pages = $pages;
				add_action( 'tinvwl_pagenation_wishlistpublic_table', array( $this, 'pages' ) );
			}

			if ( $pages > $paged ) {
				add_action( 'tinvwl_pagenation_wishlistpublic_table', array( $this, 'page_next' ) );
			}
		}

		$data['sql'] .= ' ORDER BY `{order_by}` {order} LIMIT {offset},{count};';
		$wishlists   = apply_filters( 'tinvwl_search_prepare_results', $wl->get( $data ) );

		if ( empty( $wishlists ) ) {
			return tinv_wishlist_template( 'ti-wishlist-searchform-empty.php' );
		}

		$data = array(
			'wishlists' => $wishlists,
		);
		tinv_wishlist_template( 'ti-wishlist-public.php', $data );
	}

	/**
	 * Prev page button
	 */
	function page_prev() {
		$paged = get_query_var( 'paged', 1 );
		$paged = 1 < $paged ? $paged - 1 : 0;
		$this->page( $paged, sprintf( '<i class="ftinvwl ftinvwl-chevron-left"></i><span>%s</span>', __( 'Previous Page', 'ti-woocommerce-wishlist-premium' ) ), array( 'class' => 'button tinv-prev' ) );
	}

	/**
	 * Pages
	 */
	function pages() {

		$paged = get_query_var( 'paged', 1 );

		$paged = 1 < $paged ? $paged : 1;

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
		$paged = get_query_var( 'paged', 1 );
		$paged = 1 < $paged ? $paged + 1 : 2;
		$this->page( $paged, sprintf( '<span>%s</span><i class="ftinvwl ftinvwl-chevron-right"></i>', __( 'Next Page', 'ti-woocommerce-wishlist-premium' ) ), array( 'class' => 'button tinv-next' ) );
	}

	/**
	 * Page button
	 *
	 * @param integer $paged Index page.
	 * @param string $text Text button.
	 * @param style $style Style attribute.
	 *
	 * @return boolean
	 */
	function page( $paged, $text, $style = array() ) {
		$paged = absint( $paged );
		$page  = tinv_get_option( 'page', 'search' );
		if ( empty( $page ) ) {
			return false;
		}
		if ( is_array( $style ) ) {
			$style = TInvWL_Form::__atrtostr( $style );
		}
		$link = get_permalink( $page );
		if ( get_option( 'permalink_structure' ) ) {
			$link .= 'page/' . $paged . '/?tiws=' . get_query_var( 'tiws', '' );
		} else {
			$link .= '&paged=' . $paged . '&tiws=' . get_query_var( 'tiws', '' );
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
			'button_text'     => __( 'Search for a Wishlist', 'ti-woocommerce-wishlist-premium' ),
			'input_text'      => __( 'Type a name or email', 'ti-woocommerce-wishlist-premium' ),
			'show_navigation' => 'yes',
		);
		$atts    = shortcode_atts( $default, $atts );

		if ( tinv_get_option( 'page', 'search' ) ) {
			ob_start();
			$this->htmloutput( $atts );

			return ob_get_clean();
		} else {
			return '';
		}
	}
}
