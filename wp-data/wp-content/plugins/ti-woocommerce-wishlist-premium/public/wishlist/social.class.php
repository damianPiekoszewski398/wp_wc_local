<?php
/**
 * Social actions buttons functional
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Social actions buttons functional
 */
class TInvWL_Public_Wishlist_Social {

	/**
	 * Share url this wishlist
	 *
	 * @var string
	 */
	static $url;

	/**
	 * Image url
	 *
	 * @var string
	 */
	static $image;

	/**
	 * First run method
	 *
	 * @param array $wishlist Set from action.
	 *
	 * @return boolean
	 */
	public static function init( $wishlist, $per_page = null, $paged = 1 ) {
		if ( empty( $wishlist ) || 'private' === $wishlist['status'] ) {
			return false;
		}

		if ( is_array( TInvWL_Public_Wishlist_View::instance()->get_current_products_query() ) ) {
			$products = TInvWL_Public_Wishlist_View::instance()->current_products_query;
		} else {
			$products = TInvWL_Public_Wishlist_View::instance()->get_current_products( $wishlist, true, $per_page, $paged );
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

		self::$image = $image;
		self::$url   = tinv_url_wishlist( $wishlist['share_key'] );

		self::htmloutput( $wishlist );
	}

	/**
	 * Output social buttons
	 *
	 * @param array $wishlist Set from action.
	 */
	public static function htmloutput( $wishlist ) {

		$social = tinv_get_option( 'social' );

		$share_on = apply_filters( 'tinvwl_share_on_text', tinv_get_option( 'social', 'share_on' ) );

		foreach ( $social as $name => $soc_network ) {
			if ( $soc_network && method_exists( __CLASS__, $name ) ) {
				$social[ $name ]        = self::$name();
				$social_titles[ $name ] = self::$name( true );
				if ( 'clipboard' === $name ) {
					wp_enqueue_script( 'tinvwl-clipboard' );
				}
			} else {
				$social[ $name ] = '';
			}
		}

		$social = apply_filters( 'tinvwl_view_social', $social, array(
			'wishlist' => $wishlist,
			'image'    => self::$image,
			'url'      => self::$url,
		) );
		$social = array_filter( $social );
		if ( empty( $social ) ) {
			return false;
		}
		$data = array(
			'social'        => $social,
			'social_titles' => $social_titles,
			'share_on'      => $share_on,
		);
		tinv_wishlist_template( 'ti-wishlist-social.php', $data );
	}

	/**
	 * Create facebook share url
	 *
	 * @param bool $title return title for translation.
	 *
	 * @return string
	 */
	public static function facebook( $title = false ) {
		if ( $title ) {
			return esc_html__( 'Facebook', 'ti-woocommerce-wishlist-premium' );
		}

		$data = array(
			'u' => self::$url,
		);

		$data = apply_filters( 'tinvwl_social_link_facebook', $data );

		return 'https://www.facebook.com/sharer/sharer.php?' . http_build_query( $data );
	}

	/**
	 * Create twitter share url
	 *
	 * @param bool $title return title for translation.
	 *
	 * @return string
	 */
	public static function twitter( $title = false ) {
		if ( $title ) {
			return esc_html__( 'Twitter', 'ti-woocommerce-wishlist-premium' );
		}

		$data = array(
			'url' => self::$url,
		);

		$data = apply_filters( 'tinvwl_social_link_twitter', $data );

		return 'https://twitter.com/share?' . http_build_query( $data );
	}

	/**
	 * Create pinterest share url
	 *
	 * @param bool $title return title for translation.
	 *
	 * @return string
	 */
	public static function pinterest( $title = false ) {
		if ( $title ) {
			return esc_html__( 'Pinterest', 'ti-woocommerce-wishlist-premium' );
		}

		$data = array(
			'url'   => self::$url,
			'media' => self::$image,
		);

		$data = apply_filters( 'tinvwl_social_link_pinterest', $data );

		return 'http://pinterest.com/pin/create/button/?' . http_build_query( $data );
	}

	/**
	 * Create email share url
	 *
	 * @param bool $title return title for translation.
	 *
	 * @return string
	 */
	public static function email( $title = false ) {
		if ( $title ) {
			return esc_html__( 'Email', 'ti-woocommerce-wishlist-premium' );
		}

		$data = array(
			'body' => self::$url,
		);

		$data = apply_filters( 'tinvwl_social_link_email', $data );

		return 'mailto:' . apply_filters( 'tinvwl_social_link_email_recepient', '' ) . '?' . http_build_query( $data );
	}

	/**
	 * Create copy to clipboard url
	 *
	 * @param bool $title return title for translation.
	 *
	 * @return string
	 */
	public static function clipboard( $title = false ) {
		if ( $title ) {
			return esc_html__( 'Clipboard', 'ti-woocommerce-wishlist-premium' );
		}

		return self::$url;
	}

	/**
	 * Create WhatsApp share url
	 *
	 * @param bool $title return title for translation.
	 *
	 * @return string
	 */
	public static function whatsapp( $title = false ) {
		if ( $title ) {
			return esc_html__( 'WhatsApp', 'ti-woocommerce-wishlist-premium' );
		}

		$data = array(
			'text' => self::$url,
		);

		$data = apply_filters( 'tinvwl_social_link_whatsapp', $data );

		return 'https://api.whatsapp.com/send?' . http_build_query( $data );
	}
}
