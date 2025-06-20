<?php
/**
 * Admin wishlists page class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Admin wishlists page class
 */
class TInvWL_Admin_Wishlist extends TInvWL_Admin_BaseSection {

	/**
	 * Menu array
	 *
	 * @return array
	 */
	function menu() {
		return array(
			'title'      => __( 'Wishlists', 'ti-woocommerce-wishlist-premium' ),
			'method'     => array( $this, '_print_' ),
			'slug'       => '',
			'capability' => 'tinvwl_wishlists',
			'roles'      => array( 'administrator', 'shop_manager' ),
		);
	}

	/**
	 * General page wishlists
	 *
	 * @param integer $id Id parameter.
	 * @param string $cat Category parameter.
	 */
	function _print_general( $id = 0, $cat = '' ) {
		$data = array(
			'_header' => __( 'Wishlists', 'ti-woocommerce-wishlist-premium' ),
			'table'   => new TInvWL_Admin_Wishlist_Table( $this->_name, $this->_version ),
		);
		$data = apply_filters( 'tinvwl_wishlist_general', $data );
		TInvWL_View::render( 'wishlists', $data );
	}

	/**
	 * General page subscribers wishlist
	 *
	 * @param integer $id Id parameter.
	 * @param string $cat Category parameter.
	 */
	function _print_subscribers( $id = 0, $cat = '' ) {
		$wl       = new TInvWL_Wishlist( $this->_name );
		$wishlist = $wl->get_by_id( $id );
		if ( empty( $wishlist ) ) {
			TInvWL_View::set_redirect( $this->admin_url( '' ) );
		}
		if ( 'private' === $wishlist['status'] ) {
			TInvWL_View::set_redirect( $this->admin_url( '' ) );
		}
		$data = array(
			'_header' => sprintf( __( 'Wishlist "%s" Subscribers', 'ti-woocommerce-wishlist-premium' ), esc_html( $wishlist['title'] ) ),
			'table'   => new TInvWL_Admin_Wishlist_Subscribers( $wishlist, $this->_name, $this->_version ),
		);
		$data = apply_filters( 'tinvwl_wishlist_subscribers', $data );
		TInvWL_View::render( 'wishlists', $data );
	}
}
