<?php
/**
 * Widget "Popular product"
 *
 * @since             1.0.0
 * @package           TInvWishlist\Widget
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Widget "Popular product"
 */
class TInvWL_Public_Widget_Search extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'tinv-wishlist widget_wishlist_search';
		$this->widget_description = __( 'A Search box for wishlists only.', 'ti-woocommerce-wishlist-premium' );
		$this->widget_id          = 'tinvwl_wishlist_search';
		$this->widget_name        = __( 'TI Wishlists Search', 'ti-woocommerce-wishlist-premium' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'ti-woocommerce-wishlist-premium' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @param array $args Artguments.
	 * @param array $instance Instance.
	 */
	function widget( $args, $instance ) {
		$this->widget_start( $args, $instance );

		$search = get_query_var( 'tiws', '' );
		tinv_wishlist_template( 'ti-wishlist-widget-searchform.php', array( 'search' => $search ) );

		$this->widget_end( $args );
	}
}
