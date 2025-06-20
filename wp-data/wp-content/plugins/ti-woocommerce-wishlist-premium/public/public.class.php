<?php
/**
 * Piblic list wishlists shortcode
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Piblic list wishlists shortcode
 */
class TInvWL_Public_Public {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $_name;

	/**
	 * This class
	 *
	 * @var \TInvWL_Public_Public
	 */
	protected static $_instance = null;

	/**
	 * Get this class object
	 *
	 * @param string $plugin_name Plugin name.
	 *
	 * @return \TInvWL_Public_Public
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

		add_filter( 'tinvwl_wishlistpublic_item_author', array( $this, 'prepare_author' ) );
	}

	/**
	 * Prepare display author column
	 *
	 * @param integer $user_id Author id.
	 *
	 * @return string
	 */
	function prepare_author( $user_id ) {
		$user_id = absint( $user_id );
		$user    = get_user_by( 'id', $user_id );
		if ( $user ) {
			$user_name = esc_html( trim( sprintf( '%s %s', $user->user_firstname, $user->user_lastname ) ) );
			if ( $user_name && $user->exists() ) {
				if ( get_current_user_id() === $user_id && ! empty( $user_id ) ) {
					$user_name = sprintf( '<a href="%s">%s</a>', get_permalink( wc_get_page_id( 'myaccount' ) ), $user_name );
				}

				return $user_name;
			}
		}

		return '';
	}

	/**
	 * Output page
	 *
	 * @param array $atts Array parameter for shortcode.
	 */
	function htmloutput( $atts ) {
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

		if ( 'yes' === $atts['show_navigation'] ) {
			$pages = ceil( count( $wl->get( array(
					'count'  => 9999999,
					'status' => 'public',
				) ) ) / absint( $atts['lists_per_page'] ) );

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

		$wishlists = $wl->get( $data );

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
	 */
	function page( $paged, $text, $style = array() ) {
		$paged = absint( $paged );
		if ( is_array( $style ) ) {
			$style = TInvWL_Form::__atrtostr( $style );
		}
		$link = get_permalink();
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
		);
		$atts    = shortcode_atts( $default, $atts );

		ob_start();
		$this->htmloutput( $atts );

		return ob_get_clean();
	}
}
