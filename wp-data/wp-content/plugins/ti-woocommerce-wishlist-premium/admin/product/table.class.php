<?php
/**
 * Admin products table class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Admin products table class
 */
class TInvWL_Admin_Product_Table extends WP_List_Table {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	static $_name;
	/**
	 * Plugin version
	 *
	 * @var string
	 */
	protected $_version;

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	public function __construct( $plugin_name, $version ) {
		self::$_name    = $plugin_name;
		$this->_version = $version;

		parent::__construct( array(
			'singular' => __( 'Product', 'ti-woocommerce-wishlist-premium' ),
			// Singular name of the listed records.
			'plural'   => __( 'Products', 'ti-woocommerce-wishlist-premium' ),
			// Plural name of the listed records.
			'ajax'     => false,
			// does this table support ajax?
		) );

		add_action( 'tinvwl_product_analytics_table_nav_top', array( $this, 'add_search_box' ) );

		add_action( 'tinvwl_product_analytics_table_nav_top', array( $this, 'tinvwl_analytics_csv' ) );
	}

	/**
	 * Columns description
	 *
	 * @return array
	 */
	function get_columns_description() {
		return array(
//			'name'				 => __( 'Product Name', 'ti-woocommerce-wishlist-premium' ),
//			'count'				 => __( 'Wishlists', 'ti-woocommerce-wishlist-premium' ),
//			'users'				 => __( 'Users', 'ti-woocommerce-wishlist-premium' ),
//			'quantity'			 => __( 'Quantity in Wishlists', 'ti-woocommerce-wishlist-premium' ),
//			'click'				 => __( 'Views', 'ti-woocommerce-wishlist-premium' ),
//			'sell_of_wishlist'	 => __( 'Purchases', 'ti-woocommerce-wishlist-premium' ),
//			'ctr'				 => __( 'CTR', 'ti-woocommerce-wishlist-premium' ),
//			'abandonment'		 => __( 'Abandonment', 'ti-woocommerce-wishlist-premium' ),
		);
	}

	/**
	 * Sortable columns
	 *
	 * @return array
	 */
	public static function get_sortable_columns_static() {
		return array(
			'count'            => array( 'count', true ),
			'users'            => array( 'users', true ),
			'quantity'         => array( 'quantity', true ),
			'click'            => array( 'click', false ),
			'sell_of_wishlist' => array( 'sell_of_wishlist', true ),
			'ctr'              => array( 'ctr', true ),
			'abandonment'      => array( 'abandonment', true ),
		);
	}

	/**
	 * Fix empty analytics
	 */
	public static function fix_analytics() {
		global $wpdb;

		$wlp      = new TInvWL_Product( array(), self::$_name );
		$products = $wlp->get( array(
			'sql'      => 'SELECT `A`.`wishlist_id`,`A`.`product_id`, `A`.`variation_id` FROM `{table}` AS `A` LEFT JOIN `' . sprintf( '%s%s_%s', $wpdb->prefix, self::$_name, 'analytics' ) . '` AS `B` ON `A`.`wishlist_id` = `B`.`wishlist_id` AND `A`.`product_id` = `B`.`product_id` AND `A`.`variation_id` = `B`.`variation_id` WHERE `B`.`wishlist_id` IS NULL',
			'external' => false,
		) );
		if ( empty( $products ) ) {
			return false;
		}
		$wla = new TInvWL_Analytics( array(), self::$_name );
		foreach ( $products as $product ) {
			$wla->add( 'author', $product['wishlist_id'], $product['product_id'], $product['variation_id'], 0 );
		}
	}

	/**
	 * Get products
	 *
	 * @param integer $per_page Per page items.
	 * @param integer $page_number Page.
	 * @param boolean $external Get external data.
	 *
	 * @return array
	 */
	public static function get_products( $per_page = 10, $page_number = 1, $external = true ) {
		global $wpdb;

		$orderby = strtolower( filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
		$order   = strtoupper( filter_input( INPUT_GET, 'order', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
		$search  = filter_input( INPUT_POST, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! in_array( $order, array(
			'ASC',
			'DESC',
		) ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
			$order = 'DESC';
		}

		$_orderby = true;
		foreach ( self::get_sortable_columns_static() as $value ) {
			if ( $orderby === $value[0] ) {
				$_orderby = false;
				break;
			}
		}
		if ( $_orderby ) {
			$orderby = 'click';
		}

		$attr = array(
			'count'    => $per_page,
			'offset'   => $per_page * ( $page_number - 1 ),
			'order_by' => $orderby,
			'order'    => $order,
			'external' => $external,
			'sql'      => 'SELECT `A`.`product_id`, `A`.`variation_id`, COUNT(`B`.`ID`) AS `count`, COUNT(DISTINCT `B`.`author`) AS `users`,COUNT(DISTINCT `G`.`author`) AS `guests`,GROUP_CONCAT(DISTINCT `B`.`author` SEPARATOR \',\') AS `users_ids`, SUM(`C`.`quantity`) AS `quantity`, MAX(`C`.`date`) AS `date`, SUM(`A`.`visite_author`) AS `visite_author`, SUM(`A`.`visite`) AS `visite`, SUM(`A`.`click`) AS `click`, SUM(`A`.`cart`) AS `cart`, SUM(`A`.`sell_of_wishlist`) AS `sell_of_wishlist`, SUM(`A`.`sell_as_gift`) AS `sell_as_gift`, SUM(`A`.`click`)/SUM(`A`.`visite`) AS `ctr`, 1 - SUM(`A`.`sell_of_wishlist`)/SUM(`A`.`cart`) AS `abandonment` FROM `' . sprintf( '%s%s_%s', $wpdb->prefix, self::$_name, 'analytics' ) . '` AS `A` LEFT JOIN `{table}` AS `C` ON `C`.`wishlist_id` = `A`.`wishlist_id` AND `C`.`product_id` = `A`.`product_id` AND `C`.`variation_id` = `A`.`variation_id` LEFT JOIN `' . sprintf( '%s%s_%s', $wpdb->prefix, self::$_name, 'lists' ) . '` AS `B` ON `C`.`wishlist_id` = `B`.`ID` LEFT JOIN `' . sprintf( '%s%s_%s', $wpdb->prefix, self::$_name, 'lists' ) . '` AS `G` ON `C`.`wishlist_id` = `G`.`ID` AND `G`.`author` = 0 WHERE `A`.`product_id` > 0 AND ( {where} ) GROUP BY `A`.`product_id`, `A`.`variation_id` HAVING `count` > 0 OR `users` > 0 OR `quantity` > 0 OR `click` > 0 OR `sell_of_wishlist` > 0 OR `ctr` > 0 ORDER BY `{order_by}` {order}, `count` DESC, `click` DESC, `visite` DESC, `sell_of_wishlist` DESC LIMIT {offset},{count};',
		);
		if ( ! empty( $search ) ) {
			$args          = array(
				'post_type'      => 'product',
				's'              => $search,
				'orderby'        => 'ID',
				'fields'         => 'ids',
				'posts_per_page' => - 1,
			);
			$product_query = new WP_Query( $args );

			$product_query->posts   = empty( $product_query->posts ) ? array( '-1' ) : $product_query->posts;
			$attr['A`.`product_id'] = $product_query->posts;
		}
		if ( ! tinv_get_option( 'general', 'multi' ) ) {
			$attr['B`.`type'] = 'default';
		}
		$wla      = new TInvWL_Product( array() );
		$products = $wla->get( apply_filters( 'tinvwl_product_analytics_query', $attr ) );

		foreach ( $products as $key => $product ) {
			if ( empty( $product['data'] ) ) {
				unset( $products[ $key ] );
			}
		}

		return $products;
	}

	/**
	 * Get counts all products
	 *
	 * @return integer
	 */
	public static function record_count() {
		return count( self::get_products( 9999999, 1, true ) );
	}

	/**
	 * Display message empty result
	 */
	public function no_items() {
		esc_html_e( 'No Products available.', 'ti-woocommerce-wishlist-premium' );
	}

	/**
	 * Create checkbox item
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="products[]" value="%s" />', $item['ID'] );
	}

	/**
	 * Create preference item
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_preference( $item ) {
		$product = $item['data'];
		$action  = array(
			'email' => sprintf( '<a class="tinvwl-modal-btn tinvwl-btn tinvwl-w-mobile white small" href="%s"><i class="tinvwl-mobile ftinvwl ftinvwl-email"></i><span class="tinvwl-full">%s</span></a>', esc_url( self::admin_url( 'product', 'promotional', array_filter( array(
				'product_id'    => $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id(),
				'variation_id'  => $product->is_type( 'variation' ) ? $product->get_id() : 0,
				'_tinvwl_nonce' => wp_create_nonce( sprintf( '%s-%s', self::$_name, 'promotional' ) ),
			) ) ) ), __( 'Send promotion', 'ti-woocommerce-wishlist-premium' ) ),
			'users' => sprintf( '<a class="tinvwl-btn tinvwl-w-mobile white small" href="%s"><i class="tinvwl-mobile ftinvwl ftinvwl-user"></i><span class="tinvwl-full">%s</span></a>', esc_url( self::admin_url( 'product', 'users', array(
				'product_id'    => $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id(),
				'variation_id'  => $product->is_type( 'variation' ) ? $product->get_id() : 0,
				'_tinvwl_nonce' => wp_create_nonce( sprintf( '%s-%s', self::$_name, 'promotional' ) ),
			) ) ), __( 'Users', 'ti-woocommerce-wishlist-premium' ) ),
			'view'  => sprintf( '<a class="tinvwl-btn white small no-txt" href="%s"><i class="ftinvwl ftinvwl-eye"></i></a>', esc_url( $product->get_permalink() ) ),
			'edit'  => sprintf( '<a class="tinvwl-btn white small no-txt" href="%s"><i class="ftinvwl ftinvwl-pencil"></i></a>', esc_url( admin_url( 'post.php?post=' . $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id() . '&action=edit' ) ) ),
		);
		if ( ! apply_filters( 'tinvwl_notifications_option_promotional', tinv_get_option( 'promotional_email', 'enabled' ) ) || ( 0 === absint( $item['users'] ) - absint( $item['guests'] ) ) ) {
			unset( $action['email'] );
		}

		if ( 0 === absint( $item['users'] ) ) {
			unset( $action['users'] );
		}

		if ( $item['users_ids'] ) {
			$users_ids = false;

			foreach ( explode( ',', $item['users_ids'] ) as $user_id ) {
				if ( $user_id ) {
					$user                  = get_user_by( 'id', $user_id );
					$notification_settings = get_user_meta( $user_id, '_tinvwl_notifications', true );
					$global_notifications  = tinv_get_option( 'global_notifications', 'enable_notifications' );
					$notifications_allowed = ( isset( $notification_settings['promotional'] ) && ! empty( $notification_settings['promotional'] ) && 'unsubscribed' === $notification_settings['promotional'] ) || ( ! isset( $notification_settings['promotional'] ) && ! $global_notifications ) ? false : true;
					if ( $user && $user->exists() && $notifications_allowed ) {
						$users_ids = true;
						break;
					}
				}
			}

			if ( ! $users_ids ) {
				unset( $action['email'] );
			}


		}

		return implode( ' ', $action );
	}

	/**
	 * Default output item
	 *
	 * @param array $item Row array.
	 * @param string $column_name Name coumn.
	 *se
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
				$product = $item['data'];

				return sprintf( '<a href="%s"><span class="product-image">%s</span><span class="product-title">%s<br/><span class="product-attributes">%s</span></span></a>', esc_url( admin_url( 'post.php?post=' . ( $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id() ) . '&action=edit' ) ), $product->get_image( array(
					100,
					100,
				) ), is_callable( array(
					$product,
					'get_name'
				) ) ? $product->get_name() : $product->get_title(), tinv_wishlist_get_item_data( $product, true ) );
			case 'abandonment':
			case 'ctr':
				return number_format( $item[ $column_name ] * 100, 2, ',', ' ' ) . ' %';
			case 'count':
			case 'users':
			case 'quantity':
			case 'click':
			case 'sell_of_wishlist':
				return absint( $item[ $column_name ] );
			case 'date':
				return sprintf(
					'<time class="entry-date" datetime="%1$s">%2$s</time>', $item['date'], mysql2date( get_option( 'date_format' ), $item['date'] )
				);
			default:
				do_action( 'tinvwl_product_anayltics_column_' . $column_name, $item );
				break;
		}
	}

	/**
	 * Columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'name'  => __( 'Product Name', 'ti-woocommerce-wishlist-premium' ),
			'count' => __( 'Wishlists', 'ti-woocommerce-wishlist-premium' ),
		);
		if ( tinv_get_option( 'general', 'multi' ) ) {
			$columns['users'] = __( 'Users', 'ti-woocommerce-wishlist-premium' );
		}
		if ( tinv_get_option( 'general', 'quantity_func' ) ) {
			$columns['quantity'] = sprintf( '%s <span class="tinvwl-full">%s</span>', __( 'Qty', 'ti-woocommerce-wishlist-premium' ), __( ' in Wishlists', 'ti-woocommerce-wishlist-premium' ) );
		}
		$columns['click']            = __( 'Views', 'ti-woocommerce-wishlist-premium' );
		$columns['sell_of_wishlist'] = __( 'Purchases', 'ti-woocommerce-wishlist-premium' );
		$columns['ctr']              = __( 'CTR', 'ti-woocommerce-wishlist-premium' );
		$columns['abandonment']      = __( 'Abandonment', 'ti-woocommerce-wishlist-premium' );
		$columns['preference']       = __( 'Actions', 'ti-woocommerce-wishlist-premium' );

		return apply_filters( 'tinvwl_product_analytics_columns', $columns );
	}

	/**
	 * Pagination
	 *
	 * @param string $which Top or Bottom postion.
	 *
	 * @return type
	 */
	protected function pagination( $which ) {
		if ( empty( $this->_pagination_args ) || 'top' === $which ) {
			return;
		}

		$total_pages     = $this->_pagination_args['total_pages'];
		$infinite_scroll = false;
		if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
			$infinite_scroll = $this->_pagination_args['infinite_scroll'];
		}

		$current = $this->get_pagenum();

		$current_url = set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); // @codingStandardsIgnoreLine WordPress.VIP.SuperGlobalInputUsage.AccessDetected

		$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

		$page_links = array();

		if ( 1 < $current ) {
			$page_links[] = sprintf( "<a class='tinvwl-page-number prev-page' href='%s'><span class='screen-reader-text'>%s</span><span class='tinvwl-chevron' aria-hidden='true'>%s</span></a>", esc_url( add_query_arg( 'paged', max( 1, $current - 1 ), $current_url ) ), __( 'Previous page', 'ti-woocommerce-wishlist-premium' ), '' );
			if ( 1 < $current - 1 ) {
				$page_links[] = sprintf( "<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>", esc_url( remove_query_arg( 'paged', $current_url ) ), __( 'First page', 'ti-woocommerce-wishlist-premium' ), 1 );
			}
			if ( 1 < $current - 2 ) {
				$page_links[] = sprintf( '<span class="tinvwl-page-number space" aria-hidden="true">%s</span>', __( '...', 'ti-woocommerce-wishlist-premium' ) );
			}
			$page_links[] = sprintf( "<a class='last-page' href='%s'><span aria-hidden='true'>%s</span></a>", esc_url( add_query_arg( 'paged', max( 1, $current - 1 ), $current_url ) ), max( 1, $current - 1 ) );
		}

		$page_links[] = sprintf( '<span class="tinvwl-page-number current" aria-hidden="true">%s</span>', $current );

		if ( $total_pages > $current ) {
			$page_links[] = sprintf( "<a class='last-page' href='%s'><span aria-hidden='true'>%s</span></a>", esc_url( add_query_arg( 'paged', min( $total_pages, $current + 1 ), $current_url ) ), min( $total_pages, $current + 1 ) );
			if ( $total_pages > $current + 2 ) {
				$page_links[] = sprintf( '<span class="tinvwl-page-number space" aria-hidden="true">%s</span>', __( '...', 'ti-woocommerce-wishlist-premium' ) );
			}
			if ( $total_pages > $current + 1 ) {
				$page_links[] = sprintf( "<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>", esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ), __( 'Last page', 'ti-woocommerce-wishlist-premium' ), $total_pages
				);
			}

			$page_links[] = sprintf( "<a class='tinvwl-page-number next-page' href='%s'><span class='screen-reader-text'>%s</span><span class='tinvwl-chevron' aria-hidden='true'>%s</span></a>", esc_url( add_query_arg( 'paged', min( $total_pages, $current + 1 ), $current_url ) ), __( 'Next page', 'ti-woocommerce-wishlist-premium' ), '' );
		}

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) ) {
			$pagination_links_class = ' hide-if-js';
		}
		$output = "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages ) {
			$page_class = $total_pages < 2 ? ' one-page' : '';
		} else {
			$page_class = ' no-pages';
		}
		$this->_pagination = "<div class='alignright'><div class='tablenav-pages{$page_class}'>$output</div></div>";

		echo $this->_pagination; // WPCS: xss ok.
	}

	/**
	 * Create search form
	 *
	 * @param string $text Text button.
	 * @param string $input_id ID search field.
	 */
	public function search_box( $text, $input_id ) {
		$input_id = $input_id . '-search-input';
		?>
		<div class="alignright action-search">
			<input type="search" id="<?php echo esc_attr( $input_id ) ?>" name="s"
				   value="<?php _admin_search_query(); ?>"/>
			<button type="submit" id="search-submit"
					class="tinvwl-btn grey action"><?php echo $text; // WPCS: xss ok. ?></button>
		</div>
		<?php
	}

	/**
	 * Removed tfooter
	 */
	public function display() {
		$singular = $this->_args['singular'];

		$this->display_tablenav( 'top' );

		$this->screen->render_screen_reader_content( 'heading_list' );
		?>
		<table class="wp-list-table <?php echo esc_attr( implode( ' ', $this->get_table_classes() ) ); ?>">
			<thead>
			<tr>
				<?php $this->print_column_headers(); ?>
			</tr>
			</thead>

			<tbody id="the-list"<?php
			if ( $singular ) {
				echo " data-wp-lists='list:$singular'"; // WPCS: xss ok.
			}
			?>>
			<?php $this->display_rows_or_placeholder(); ?>
			</tbody>

		</table>
		<?php
		$this->display_tablenav( 'bottom' );
	}

	/**
	 * Hook table nav.
	 *
	 * @param string $position Top or Bottom postion.
	 *
	 * @return boolean
	 */
	protected function extra_tablenav( $position ) {
		do_action( 'tinvwl_product_analytics_table_nav_top', $position );

	}

	/**
	 * @param $position
	 */
	public function add_search_box( $position ) {
		if ( 'top' !== $position ) {
			return;
		}
		$this->search_box( sprintf( '<span class="tinvwl-mobile">%s</span> <span class="tinvwl-full">%s</span>', __( 'Search', 'ti-woocommerce-wishlist-premium' ), __( 'by Product', 'ti-woocommerce-wishlist-premium' ) ), 'search_by_product' );
	}

	/**
	 * Sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return self::get_sortable_columns_static();
	}

	/**
	 * Prepare table
	 *
	 * @return \TInvWL_Admin_Wishlist_Table
	 */
	public function prepare_items() {
		self::fix_analytics();

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/** Process bulk action */
		// @codingStandardsIgnoreLine $this->process_bulk_action();
		$per_page     = apply_filters( 'tinvwl_product_analytics_per_page', 10 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( array(
			'total_items' => $total_items, // WE have to calculate the total number of items.
			'per_page'    => $per_page, // WE have to determine how many items to show on a page.
		) );

		$this->items = self::get_products( $per_page, $current_page );

		return $this;
	}

	/**
	 * Formated admin url
	 *
	 * @param string $page Page title.
	 * @param string $cat Category title.
	 * @param array $arg Arguments array.
	 *
	 * @return string
	 */
	public static function admin_url( $page, $cat = '', $arg = array() ) {
		$protocol = is_ssl() ? 'https' : 'http';
		$glue     = '-';
		$params   = array(
			'page' => empty( $page ) ? self::$_name : self::$_name . $glue . $page,
			'cat'  => $cat,
		);
		if ( is_array( $arg ) ) {
			$params = array_merge( $params, $arg );
		}
		$params = array_filter( $params );
		$params = http_build_query( $params );
		if ( is_string( $arg ) ) {
			$params = $params . '&' . $arg;
		}

		return admin_url( sprintf( 'admin.php?%s', $params ), $protocol );
	}

	/**
	 * Print column headers, accounting for hidden and sortable columns.
	 *
	 * @param bool $with_id Whether to set the id attribute or not.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @staticvar int $cb_counter
	 *
	 */
	public function print_column_headers( $with_id = true ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		$current_url = set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); // @codingStandardsIgnoreLine WordPress.VIP.SuperGlobalInputUsage.AccessDetected
		$current_url = remove_query_arg( 'paged', $current_url );

		$_current_orderby = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( $_current_orderby ) {
			$current_orderby = $_current_orderby;
		} else {
			$current_orderby = '';
		}

		$_current_order = filter_input( INPUT_GET, 'order', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( $_current_order && 'desc' === $_current_order ) {
			$current_order = 'desc';
		} else {
			$current_order = 'asc';
		}

		if ( ! empty( $columns['cb'] ) ) {
			static $cb_counter = 1;
			$columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All', 'ti-woocommerce-wishlist-premium' ) . '</label>'
							 . sprintf( '<input id="cb-select-all-%s" type="checkbox" />', $cb_counter );
			$cb_counter ++;
		}
		$descriptions = (array) $this->get_columns_description();

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );

			if ( in_array( $column_key, $hidden ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
				$class[] = 'hidden';
			}

			if ( 'cb' === $column_key ) {
				$class[] = 'check-column';
			} elseif ( in_array( $column_key, array(
				'posts',
				'comments',
				'links',
			) ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
				$class[] = 'num';
			}

			if ( $column_key === $primary ) {
				$class[] = 'column-primary';
			}

			$_column_display_name = $column_display_name;

			$column_display_name = '';

			if ( array_key_exists( $column_key, $descriptions ) && ! empty( $descriptions[ $column_key ] ) ) {
				$class[] = 'tinvwl-has-info';
				ob_start();
				TInvWL_View::view( 'table-infoblock', array( 'desc' => $descriptions[ $column_key ] ) );
				$column_display_name = ob_get_clean();
			}

			if ( isset( $sortable[ $column_key ] ) ) {
				list( $orderby, $desc_first ) = $sortable[ $column_key ];

				if ( $current_orderby === $orderby ) {
					$order   = 'asc' === $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order   = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				$column_display_name .= '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $_column_display_name . '</span><span class="sorting-indicator"></span></a>';
			} else {
				$column_display_name .= $_column_display_name;
			}

			$tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
			$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
			$id    = $with_id ? "id='$column_key'" : '';

			if ( ! empty( $class ) ) {
				$class = "class='" . join( ' ', $class ) . "'";
			}
			echo "<$tag $scope $id $class>$column_display_name</$tag>"; // WPCS: xss ok.
		} // End foreach().
	}

	function tinvwl_analytics_csv( $position ) {
		if ( 'top' === $position ) {
			$current_url = set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); // @codingStandardsIgnoreLine WordPress.VIP.SuperGlobalInputUsage.AccessDetected
			$current_url = remove_query_arg( 'paged', $current_url );
			echo '<a href="' . esc_url( add_query_arg( 'tinvwl_analytics_csv', 1, $current_url ) ) . '" id="download-csv" class="tinvwl-btn grey action">' . __( 'Download CSV', 'ti-woocommerce-wishlist-premium' ) . '</a>';
		}
		if ( isset( $_GET['tinvwl_analytics_csv'] ) ) {
			$file = 'tinvwl_analytics_csv';

			$results = $this::get_products( 999999, 1 );

			if ( empty( $results ) ) {
				return;
			}

			foreach ( $results as $key => $product ) {
				unset( $results[ $key ]['meta'] );
				unset( $results[ $key ]['data'] );
				unset( $results[ $key ]['guests'] );
				unset( $results[ $key ]['visite_author'] );
				unset( $results[ $key ]['visite'] );
				unset( $results[ $key ]['cart'] );
				unset( $results[ $key ]['sell_as_gift'] );
				unset( $results[ $key ]['date'] );

				foreach ( $product as $k => $v ) {
					switch ( $k ) {
						case 'ctr':
						case 'abandonment':
							$results[ $key ][ $k ] = number_format( $v * 100, 2, ',', ' ' ) . ' %';
							break;
					}
				}
			}

			$headers = array();

			foreach ( array_keys( $results[0] ) as $title ) {

				switch ( $title ) {
					case 'count':
						$title = 'wishlists';
						break;
					case 'click':
						$title = 'views';
						break;
					case 'sell_of_wishlist':
						$title = 'purchases';
						break;
				}

				$headers[] = $title;
			}

			$filename = $file . "_" . date( "Y-m-d_H-i", time() );
			ob_end_clean();
			header( "Content-type: text/csv; charset=utf-8" );
			header( "Content-disposition: csv" . date( "Y-m-d" ) . ".csv" );
			header( "Content-disposition: filename=" . $filename . ".csv" );

			$output = fopen( 'php://output', 'w' );
			fputcsv( $output, $headers );
			foreach ( $results as $row ) {

				fputcsv( $output, $row );
			}
			fclose( $output );
			exit();
		}
	}
}
