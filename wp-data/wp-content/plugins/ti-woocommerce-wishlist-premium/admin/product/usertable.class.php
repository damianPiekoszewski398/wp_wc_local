<?php
/**
 * Admin users wishlists by products table class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Admin users wishlists by products table class
 */
class TInvWL_Admin_Product_UserTable extends WP_List_Table {

	/**
	 * Product info
	 *
	 * @var object
	 */
	static $product;
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
	 * @param object $product Prouct.
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	public function __construct( $product, $plugin_name, $version ) {
		self::$product  = $product;
		self::$_name    = $plugin_name;
		$this->_version = $version;

		parent::__construct( array(
				'singular' => __( 'Product', 'ti-woocommerce-wishlist-premium' ),
			// Singular name of the listed records.
				'plural'   => __( 'users', 'ti-woocommerce-wishlist-premium' ),
			// Plural name of the listed records.
				'ajax'     => false,
			// does this table support ajax?
		) );
	}

	/**
	 * Columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
				'cb'       => '<input type="checkbox"/>',
				'name'     => __( 'Name', 'ti-woocommerce-wishlist-premium' ),
				'wishlist' => __( 'Wishlists', 'ti-woocommerce-wishlist-premium' ),
		);
		if ( tinv_get_option( 'product_table', 'colm_date' ) ) {
			$columns['date'] = __( 'Date Added', 'ti-woocommerce-wishlist-premium' );
		}
		if ( tinv_get_option( 'general', 'quantity_func' ) ) {
			$columns['quantity'] = __( 'Quantity', 'ti-woocommerce-wishlist-premium' );
		}
		$columns['subscribed'] = __( 'Subscribed', 'ti-woocommerce-wishlist-premium' );
		$columns['preference'] = __( 'Actions', 'ti-woocommerce-wishlist-premium' );

		return $columns;
	}

	/**
	 * Columns description
	 *
	 * @return array
	 */
	function get_columns_description() {
		return array(
				'name'       => __( 'Name', 'ti-woocommerce-wishlist-premium' ),
				'wishlist'   => __( 'Wishlists', 'ti-woocommerce-wishlist-premium' ),
				'date'       => __( 'Date Added', 'ti-woocommerce-wishlist-premium' ),
				'subscribed' => __( 'Subscribed', 'ti-woocommerce-wishlist-premium' ),
				'quantity'   => __( 'Quantity', 'ti-woocommerce-wishlist-premium' ),
		);
	}

	/**
	 * Get products
	 *
	 * @param integer $per_page Per page items.
	 * @param integer $page_number Page.
	 *
	 * @return array
	 */
	public static function get_products( $per_page = 10, $page_number = 1 ) {
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
			$orderby = 'date';
		}
		$attr = array(
				'count'        => $per_page,
				'offset'       => $per_page * ( $page_number - 1 ),
				'product_id'   => self::$product->is_type( 'variation' ) ? self::$product->get_parent_id() : self::$product->get_id(),
				'variation_id' => self::$product->is_type( 'variation' ) ? self::$product->get_id() : 0,
				'type'         => tinv_get_option( 'general', 'multi' ) ? array( 'default', 'list' ) : 'default',
				'order_by'     => $orderby,
				'order'        => $order,
				'sql'          => 'SELECT `A`.*, `B`.`author` AS `author`, GROUP_CONCAT(`A`.`wishlist_id`) AS `wishlist_id`, SUM(`A`.`quantity`) AS `quantity`, MAX(`A`.`date`) AS `date` FROM `{table}` AS `A` INNER JOIN `' . sprintf( '%s%s_%s', $wpdb->prefix, self::$_name, 'lists' ) . '` AS `B` ON `A`.`wishlist_id` = `B`.`ID` WHERE {where} GROUP BY `A`.`product_id`, `A`.`variation_id`, `B`.`author` ORDER BY `{order_by}` {order} LIMIT {offset},{count};',
		);
		if ( ! empty( $search ) ) {
			$args       = array(
					'search'         => $search,
					'search_columns' => array( 'user_login', 'user_email', 'user_nicename' ),
			);
			$user_query = new WP_User_Query( $args );
			$users      = array();
			if ( ! empty( $user_query->results ) ) {
				foreach ( $user_query->results as $user ) {
					$users[] = $user->ID;
				}
			}

			$attr['B`.`author'] = $users;
		}

		$wlp      = new TInvWL_Product( array() );
		$products = $wlp->get( $attr );

		return $products;
	}

	/**
	 * Get counts all products
	 *
	 * @return integer
	 */
	public static function record_count() {
		global $wpdb;

		$attr = array(
				'count'        => 9999999,
				'offset'       => 0,
				'product_id'   => self::$product->is_type( 'variation' ) ? self::$product->get_parent_id() : self::$product->get_id(),
				'variation_id' => self::$product->is_type( 'variation' ) ? self::$product->get_id() : 0,
				'type'         => tinv_get_option( 'general', 'multi' ) ? array( 'default', 'list' ) : 'default',
				'external'     => false,
				'sql'          => 'SELECT `A`.*, `B`.`author` AS `author`, GROUP_CONCAT(`A`.`wishlist_id`) AS `wishlist_id`, SUM(`A`.`quantity`) AS `quantity`, MAX(`A`.`date`) AS `date` FROM `{table}` AS `A` INNER JOIN `' . sprintf( '%s%s_%s', $wpdb->prefix, self::$_name, 'lists' ) . '` AS `B` ON `A`.`wishlist_id` = `B`.`ID` WHERE {where} GROUP BY `A`.`product_id`, `A`.`variation_id`, `B`.`author` ORDER BY `{order_by}` {order} LIMIT {offset},{count};',
		);
		if ( ! empty( $search ) ) {
			$args       = array(
					'search'         => $search,
					'search_columns' => array( 'user_login', 'user_email', 'user_nicename' ),
			);
			$user_query = new WP_User_Query( $args );
			$users      = array();
			if ( ! empty( $user_query->results ) ) {
				foreach ( $user_query->results as $user ) {
					$users[] = $user->ID;
				}
			}

			$attr['B`.`author'] = $users;
		}
		$wlp = new TInvWL_Product( array() );

		return count( $wlp->get( $attr ) );
	}

	/**
	 * Display message empty result
	 */
	public function no_items() {
		esc_html_e( 'No Users available.', 'ti-woocommerce-wishlist-premium' );
	}

	/**
	 * Create checkbox item
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_cb( $item ) {

		if ( 0 === absint( $item['author'] ) ) {
			return false;
		}

		$user                  = get_user_by( 'id', $item['author'] );
		$notification_settings = get_user_meta( $item['author'], '_tinvwl_notifications', true );
		$global_notifications  = tinv_get_option( 'global_notifications', 'enable_notifications' );
		$notifications_allowed = ( isset( $notification_settings['promotional'] ) && ! empty( $notification_settings['promotional'] ) && 'unsubscribed' === $notification_settings['promotional'] ) || ( ! isset( $notification_settings['promotional'] ) && ! $global_notifications ) ? false : true;
		if ( ! $user || ! $user->exists() || ! $notifications_allowed ) {
			return false;
		}

		return sprintf(
				'<input type="checkbox" name="users[]" value="%s" />', $item['author']
		);
	}

	/**
	 * Create name author item
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_name( $item ) {
		$user = get_user_by( 'id', $item['author'] );
		if ( $user && $user->exists() ) {
			return sprintf( '<a href="%s">%s</a>', admin_url( 'profile.php?user_id=' . $user->ID ), $user->display_name );
		} else {
			return __( 'Guest', 'ti-woocommerce-wishlist-premium' );
		}
	}

	/**
	 * Create name wishlist item
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_wishlist( $item ) {
		$wl        = new TInvWL_Wishlist( self::$_name );
		$wishlists = explode( ',', $item['wishlist_id'] );
		$wishlists = $wl->get( array(
				'ID' => $wishlists,
		) );
		foreach ( $wishlists as &$wishlist ) {
			$wishlist = sprintf(
					'<a href="%s">%s</a>', esc_url( tinv_url_wishlist( $wishlist['ID'] ) ), esc_html( $wishlist['title'] )
			);
		}

		return $this->row_actions( $wishlists, true );
	}

	/**
	 * Subscribed column
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_subscribed( $item ) {

		if ( 0 === absint( $item['author'] ) ) {
			return '<i class="ftinvwl ftinvwl-times" style="font-size: 1.6em;color:#ff5739"></i>';
		}

		$user                  = get_user_by( 'id', $item['author'] );
		$notification_settings = get_user_meta( $item['author'], '_tinvwl_notifications', true );
		$global_notifications  = tinv_get_option( 'global_notifications', 'enable_notifications' );
		$notifications_allowed = ( isset( $notification_settings['promotional'] ) && ! empty( $notification_settings['promotional'] ) && 'unsubscribed' === $notification_settings['promotional'] ) || ( ! isset( $notification_settings['promotional'] ) && ! $global_notifications ) ? false : true;
		if ( ! $user || ! $user->exists() || ! $notifications_allowed ) {
			return '<i class="ftinvwl ftinvwl-times" style="font-size: 1.6em;color:#ff5739"></i>';
		}

		return '<i class="ftinvwl ftinvwl-check" style="font-size: 1em;color:#96b100"></i>';
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


		if ( 0 === absint( $item['author'] ) ) {
			return false;
		}

		$user                  = get_user_by( 'id', $item['author'] );
		$notification_settings = get_user_meta( $item['author'], '_tinvwl_notifications', true );
		$global_notifications  = tinv_get_option( 'global_notifications', 'enable_notifications' );
		$notifications_allowed = ( isset( $notification_settings['promotional'] ) && ! empty( $notification_settings['promotional'] ) && 'unsubscribed' === $notification_settings['promotional'] ) || ( ! isset( $notification_settings['promotional'] ) && ! $global_notifications ) ? false : true;
		if ( ! $user || ! $user->exists() || ! $notifications_allowed ) {
			return false;
		}

		return $this->row_actions( array(
				'email' => sprintf( '<a class="tinvwl-modal-btn tinvwl-btn tinvwl-w-mobile white small" href="%s"><i class="tinvwl-mobile ftinvwl ftinvwl-email"></i><span class="tinvwl-full">%s</span></a>', esc_url( self::admin_url( 'product', 'promotional', array_filter( array(
						'product_id'    => $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id(),
						'variation_id'  => $product->is_type( 'variation' ) ? $product->get_id() : 0,
						'user_id'       => $item['author'],
						'redirect_to'   => 'users',
						'_tinvwl_nonce' => wp_create_nonce( sprintf( '%s-%s', self::$_name, 'promotional' ) ),
				) ) ) ), __( 'Send promotional', 'ti-woocommerce-wishlist-premium' ) ),
		), true );
	}

	/**
	 * Default output item
	 *
	 * @param array $item Row array.
	 * @param string $column_name Name coumn.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'date':
				return sprintf(
						'<time class="entry-date" datetime="%1$s">%2$s</time>', $item['date'], mysql2date( get_option( 'date_format' ), $item['date'] )
				);
			default:
				return $item[ $column_name ]; // Show the whole array for troubleshooting purposes.
		}
	}

	/**
	 * Sortable columns
	 *
	 * @return array
	 */
	public static function get_sortable_columns_static() {
		return array(
				'date'     => array( 'date', false ),
				'quantity' => array( 'quantity', true ),
		);
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
	 * Add search form
	 *
	 * @param string $which Top or Bottom postion.
	 *
	 * @return boolean
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}
		$this->search_box( sprintf( '<span class="tinvwl-mobile">%s</span> <span class="tinvwl-full">%s</span>', __( 'Search', 'ti-woocommerce-wishlist-premium' ), __( 'by User', 'ti-woocommerce-wishlist-premium' ) ), 'search_by_user' );
	}

	/**
	 * Create Bulk actions
	 *
	 * @return array
	 */
	function get_bulk_actions() {
		$actions = array(
				'promo' => __( 'Send promotional', 'ti-woocommerce-wishlist-premium' ),
		);

		return $actions;
	}

	/**
	 * Apply Bulk actions
	 */
	public function process_bulk_action() {
		$nonce  = filter_input( INPUT_POST, '_tinvwl_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$action = 'bulk-' . $this->_args['plural'];
		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			return false;
		}
		$action = $this->current_action();
		if ( 'promo' !== $action ) {
			return false;
		}
		$data            = filter_input_array( INPUT_GET, array(
				'product_id'   => FILTER_VALIDATE_INT,
				'variation_id' => FILTER_VALIDATE_INT,
		) );
		$data['user_id'] = array_filter( filter_input( INPUT_POST, 'users', FILTER_VALIDATE_INT, array(
				'flags' => FILTER_FORCE_ARRAY,
		) ) );
		if ( empty( $data['user_id'] ) ) {
			return false;
		}
		$data['_tinvwl_nonce'] = wp_create_nonce( sprintf( '%s-%s', self::$_name, 'promotional' ) );
		$data['redirect_to']   = 'users';
		echo '<script language = "javascript">document.location.href="' . self::admin_url( 'product', 'promotional', array_filter( $data ) ) . '";</script>'; // WPCS: xss ok.
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
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = 10;
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
}
