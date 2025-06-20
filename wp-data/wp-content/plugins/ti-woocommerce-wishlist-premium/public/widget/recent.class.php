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
class TInvWL_Public_Widget_Recent extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'tinvwl widget_recent_wishlists';
		$this->widget_description = __( 'Display a list of your most recent wishlists on your site.', 'ti-woocommerce-wishlist-premium' );
		$this->widget_id          = 'tinvwl_recent_wishlists';
		$this->widget_name        = __( 'TInv Recent/Popular Wishlists', 'ti-woocommerce-wishlist-premium' );
		$this->settings           = array(
			'title'       => array(
				'type'  => 'text',
				'std'   => __( 'Recent Wishlists', 'ti-woocommerce-wishlist-premium' ),
				'label' => __( 'Title', 'ti-woocommerce-wishlist-premium' ),
			),
			'number'      => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 10,
				'label' => __( 'Number of wishlists to show', 'ti-woocommerce-wishlist-premium' ),
			),
			'orderby'     => array(
				'type'    => 'select',
				'std'     => 'date',
				'label'   => __( 'Order by', 'ti-woocommerce-wishlist-premium' ),
				'options' => array(
					'date' => __( 'Recent', 'ti-woocommerce-wishlist-premium' ),
					'view' => __( 'Popular', 'ti-woocommerce-wishlist-premium' ),
				),
			),
			'hide_empty'  => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Hide Empty Wishlist', 'ti-woocommerce-wishlist-premium' ),
			),
			'hide_date'   => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Hide Date', 'ti-woocommerce-wishlist-premium' ),
			),
			'hide_author' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Hide Author', 'ti-woocommerce-wishlist-premium' ),
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
	public function widget( $args, $instance ) {
		global $wpdb;
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];
		$wl     = new TInvWL_Wishlist();

		$data = array(
			'order'    => 'DESC',
			'order_by' => 'view' === @$instance['orderby'] ? 'B`.`visite' : 'A`.`date',
			// @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
			'count'    => $number,
			'sql'      => 'SELECT `A`.*, `B`.`visite` FROM `{table}` AS `A` LEFT JOIN `' . $wpdb->prefix . TINVWL_PREFIX . '_analytics` AS `B` ON `A`.`ID` = `B`.`wishlist_id` AND `B`.`product_id` = 0 WHERE `A`.`status`="public" GROUP BY `A`.`ID` ORDER BY `{order_by}` {order} LIMIT {offset},{count}',
		);
		if ( ! empty( $instance['hide_empty'] ) ) {
			$data['sql'] = 'SELECT `A`.*, `B`.`visite` FROM `{table}` AS `A` INNER JOIN `' . $wpdb->prefix . TINVWL_PREFIX . '_items` AS `C` INNER JOIN `' . $wpdb->prefix . TINVWL_PREFIX . '_analytics` AS `B` ON `C`.`wishlist_id` = `A`.`ID` AND `B`.`wishlist_id` = `A`.`ID` WHERE `A`.`status`="public" AND `B`.`product_id` = 0 GROUP BY `A`.`ID` ORDER BY `{order_by}` {order} LIMIT {offset},{count}';
		}
		$wishlists = $wl->get( $data );

		ob_start();
		if ( ! empty( $wishlists ) ) {
			$this->widget_start( $args, $instance );
			echo '<ul class="wishlist_list_widget">';
			foreach ( (array) $wishlists as $wishlist ) {
				echo '<li><div class="tinvwl-widget-wrap"><a href="' . esc_url( tinv_url_wishlist( $wishlist['share_key'] ) ) . '">';
				echo esc_html( $wishlist['title'] ) . '</a> ';
				if ( empty( $instance['hide_author'] ) ) {
					$user = get_user_by( 'id', $wishlist['author'] );
					if ( $user && $user->exists() ) {
						$user_name = $user->display_name;
						if ( ! empty( $user->user_firstname ) && ! empty( $user->user_lastname ) ) {
							$user_name = sprintf( '%s %s', $user->user_firstname, $user->user_lastname );
						}
						printf( ' <span class="wishlister">by <span>%s</span></span>', esc_html( $user_name ) );
					}
				}
				if ( empty( $instance['hide_date'] ) ) {
					printf( ' <time class="entry-date" datetime="%1$s">%2$s</time> ', $wishlist['date'], mysql2date( get_option( 'date_format' ), $wishlist['date'] ) ); // WPCS: xss ok.
				}
				echo '</div></li>';
			}
			echo '</ul>';
			$this->widget_end( $args );
		}
		$content = ob_get_clean();
		echo $content; // WPCS: xss ok.
		$this->cache_widget( $args, $content );
	}
}
