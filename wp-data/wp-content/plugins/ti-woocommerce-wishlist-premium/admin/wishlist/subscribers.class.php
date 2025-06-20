<?php
/**
 * Admin subscribers wishlist table class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin
 * @subpackage        Subscribers
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Admin subscribers wishlist table class
 */
class TInvWL_Admin_Wishlist_Subscribers extends WP_List_Table {

	/**
	 * Product info
	 *
	 * @var object
	 */
	static $wishlist;
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
	 * @param array $wishlist Wishlist.
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	public function __construct( $wishlist, $plugin_name, $version ) {
		self::$wishlist = $wishlist;
		self::$_name    = $plugin_name;
		$this->_version = $version;

		parent::__construct( array(
				'singular' => __( 'Subscriber', 'ti-woocommerce-wishlist-premium' ),
			// Singular name of the listed records.
				'plural'   => __( 'Subscribers', 'ti-woocommerce-wishlist-premium' ),
			// Plural name of the listed records.
				'ajax'     => false,
			// Does this table support ajax?
		) );
	}

	/**
	 * Columns
	 *
	 * @return array
	 */
	function get_columns() {
		return array(
				'name'       => __( 'Name', 'ti-woocommerce-wishlist-premium' ),
				'permisions' => __( 'Event Notifications', 'ti-woocommerce-wishlist-premium' ),
		);
	}

	/**
	 * Columns description
	 *
	 * @return array
	 */
	function get_columns_description() {
		return array();
	}

	/**
	 * Get wishlists
	 *
	 * @param integer $per_page Per page items.
	 * @param integer $page_number Page.
	 *
	 * @return array
	 */
	public static function get_subscribers( $per_page = 10, $page_number = 1 ) {
		$wls     = new TInvWL_Subscribers( self::$wishlist, self::$_name );
		$orderby = strtolower( filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
		$order   = strtoupper( filter_input( INPUT_GET, 'order', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
		if ( ! in_array( $order, array(
				'ASC',
				'DESC',
		) ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
			$order = 'ASC';
		}

		$_orderby = true;
		foreach ( self::get_sortable_columns_static() as $value ) {
			if ( $orderby === $value[0] ) {
				$_orderby = false;
				break;
			}
		}
		if ( $_orderby ) {
			$orderby = 'user_type';
		}

		$attr      = array(
				'count'    => $per_page,
				'offset'   => $per_page * ( $page_number - 1 ),
				'order'    => $order,
				'order_by' => $orderby,
		);
		$wishlists = $wls->get_by_wishlist( $attr );

		return $wishlists;
	}

	/**
	 * Get counts all wishlists
	 *
	 * @return integer
	 */
	public static function record_count() {
		$wls  = new TInvWL_Subscribers( self::$wishlist, self::$_name );
		$attr = array(
				'count'  => 9999999,
				'offset' => 0,
		);

		return count( $wls->get_by_wishlist( $attr ) );
	}

	/**
	 * Display message empty result
	 */
	public function no_items() {
		esc_html_e( 'No Subscribers available.', 'ti-woocommerce-wishlist-premium' );
	}

	/**
	 * Create name author item
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_name( $item ) {
		$user = get_user_by( 'id', $item['user_id'] );
		if ( $user && $user->exists() ) {
			return sprintf(
					'<a href="%s">%s</a>', admin_url( 'user-edit.php?user_id=' . $user->ID ), $user->display_name
			);
		}

		return $item['user_email'];
	}

	/**
	 * Create name author item
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_permisions( $item ) {
		return implode( ', ', $item['events'] );
	}

	/**
	 * Sortable columns
	 *
	 * @return array
	 */
	public static function get_sortable_columns_static() {
		return array(
				'name'       => array( 'user_email', false ),
				'permisions' => array( 'user_type', true ),
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
		<div class="alignright">
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
		// $this->process_bulk_action(); @codingStandardsIgnoreLine Squiz.PHP.CommentedOutCode.Found
		$per_page     = 10;
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( array(
				'total_items' => $total_items, // WE have to calculate the total number of items.
				'per_page'    => $per_page, // WE have to determine how many items to show on a page.
		) );

		$this->items = self::get_subscribers( $per_page, $current_page );

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
