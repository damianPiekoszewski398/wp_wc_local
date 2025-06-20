<?php
/**
 * Admin wishlists table class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Admin wishlists table class
 */
class TInvWL_Admin_Wishlist_Table extends WP_List_Table {

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
				'singular' => __( 'Wishlist', 'ti-woocommerce-wishlist-premium' ),
			// Singular name of the listed records.
				'plural'   => __( 'Wishlists', 'ti-woocommerce-wishlist-premium' ),
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
		$columns = array(
				'cb'      => '<input type="checkbox"/>',
				'title'   => __( 'Title', 'ti-woocommerce-wishlist-premium' ),
				'author'  => __( 'Author', 'ti-woocommerce-wishlist-premium' ),
				'privacy' => __( 'Privacy', 'ti-woocommerce-wishlist-premium' ),
				'items'   => sprintf( '%s <span class="tinvwl-full">%s</span>', __( 'Items', 'ti-woocommerce-wishlist-premium' ), __( 'in Wishlists', 'ti-woocommerce-wishlist-premium' ) ),
		);
		if ( tinv_get_option( 'subscribe', 'allow' ) ) {
			$columns['followers'] = __( 'Followers', 'ti-woocommerce-wishlist-premium' );
		}
		$columns['date']       = __( 'Date of creation', 'ti-woocommerce-wishlist-premium' );
		$columns['preference'] = __( 'Actions', 'ti-woocommerce-wishlist-premium' );

		return $columns;
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
	public static function get_wishlists( $per_page = 10, $page_number = 1 ) {
		$wl      = new TInvWL_Wishlist( self::$_name );
		$orderby = strtolower( filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
		$order   = strtoupper( filter_input( INPUT_GET, 'order', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
		$privacy = filter_input( INPUT_GET, 'privacy', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
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
				'count'    => $per_page,
				'offset'   => $per_page * ( $page_number - 1 ),
				'order'    => $order,
				'order_by' => $orderby,
		);
		if ( ! tinv_get_option( 'general', 'multi' ) ) {
			$attr['type'] = 'default';
		}
		if ( ! empty( $privacy ) ) {
			$attr['status'] = $privacy;
		}
		if ( ! empty( $search ) ) {
			$words = explode( ' ', $search );
			$i     = 0;
			global $wpdb;
			$sql_ids = "SELECT `A`.`ID` FROM `{$wpdb->users}` AS `A` INNER JOIN `{$wpdb->usermeta}` AS `B` ON `A`.`ID` = `B`.`user_id` WHERE `B`.`meta_key` IN ('first_name', 'last_name') AND (";
			foreach ( $words as $p ) {
				if ( $i == 0 ) {
					$sql_ids .= "CONCAT(`A`.`display_name`, ' ', `A`.`user_email`, ' ',`B`.`meta_value`) LIKE '%" . $p . "%' ";

				} elseif ( $i > 0 ) {
					$sql_ids .= "OR CONCAT(`A`.`display_name`, ' ', `A`.`user_email`, ' ',`B`.`meta_value`) LIKE '%" . $p . "%' ";

				}
				$i ++;
			}
			$sql_ids .= ") GROUP BY `A`.`ID`";

			$users = $wpdb->get_col( $sql_ids ); // @codingStandardsIgnoreLine WordPress.VIP.RestrictedVariables.user_meta

			$users = empty( $users ) ? array( '-1' ) : $users;

			$attr['author'] = $users;
		}
		$wishlists = $wl->get( $attr );

		return $wishlists;
	}

	/**
	 * Get counts all wishlists
	 *
	 * @return integer
	 */
	public static function record_count() {
		$wl      = new TInvWL_Wishlist( self::$_name );
		$privacy = filter_input( INPUT_GET, 'privacy', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$search  = filter_input( INPUT_POST, 's', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$attr    = array(
				'count'  => 9999999,
				'offset' => 0,
		);
		if ( ! tinv_get_option( 'general', 'multi' ) ) {
			$attr['type'] = 'default';
		}
		if ( ! empty( $privacy ) ) {
			$attr['status'] = $privacy;
		}
		if ( ! empty( $search ) ) {
			$args       = array(
					'search'         => '*' . $search . '*',
					'search_columns' => array( 'user_login', 'user_email', 'user_nicename' ),
			);
			$user_query = new WP_User_Query( $args );
			$users      = array();
			if ( ! empty( $user_query->results ) ) {
				foreach ( $user_query->results as $user ) {
					$users[] = $user->ID;
				}
			}

			$users = empty( $users ) ? array( '-1' ) : $users;

			$attr['author'] = $users;
		}

		return count( $wl->get( $attr ) );
	}

	/**
	 * Display message empty result
	 */
	public function no_items() {
		esc_html_e( 'No Wishlists available.', 'ti-woocommerce-wishlist-premium' );
	}

	/**
	 * Create checkbox item
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
				'<input type="checkbox" name="wishlists[]" value="%s" />', $item['ID']
		);
	}

	/**
	 * Create title item
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_title( $item ) {
		return sprintf(
				'<a href="%s">%s</a>', tinv_url_wishlist( $item['ID'] ), $item['title']
		);
	}

	/**
	 * Create author item
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_author( $item ) {
		$user = get_user_by( 'id', $item['author'] );
		if ( $user && $user->exists() ) {
			return sprintf( '<a href="%s">%s</a>', admin_url( 'profile.php?user_id=' . $user->ID ), $user->display_name );
		} else {
			return __( 'Guest', 'ti-woocommerce-wishlist-premium' );
		}
	}

	/**
	 * Create privacy item
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_privacy( $item ) {
		return $item['status'];
	}

	/**
	 * Create items count item
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_items( $item ) {
		$wlp = new TInvWL_Product( $item, self::$_name );

		return count( $wlp->get_wishlist( array( 'count' => 9999999 ) ) );
	}

	/**
	 * Get count Followers
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_followers( $item ) {
		$wls         = new TInvWL_Subscribers( $item, self::$_name );
		$subscribers = $wls->get( array( 'wishlist_id' => $item['ID'], 'count' => 9999999 ) );

		return count( $subscribers );
	}

	/**
	 * Create preference item
	 *
	 * @param array $item Row array.
	 *
	 * @return string
	 */
	function column_preference( $item ) {
		$actions = array();
		if ( tinv_get_option( 'subscribe', 'allow' ) && 'private' !== $item['status'] ) {
			$wls         = new TInvWL_Subscribers( $item, self::$_name );
			$subscribers = $wls->get( array( 'wishlist_id' => $item['ID'] ) );
			if ( 0 < count( $subscribers ) ) {
				$actions['subscribers'] = sprintf( '<a class="tinvwl-btn tinvwl-w-mobile white small" href="%s"><i class="tinvwl-mobile ftinvwl ftinvwl-user"></i><span class="tinvwl-full">%s</span></a>', esc_url( self::admin_url( '', 'subscribers', array( 'id' => $item['ID'] ) ) ), __( 'Followers', 'ti-woocommerce-wishlist-premium' ) );
			}
		}
		$actions['remove'] = $this->confirm_remove( $item );

		return implode( ' ', $actions );
	}

	/**
	 * Create confirm dialog
	 *
	 * @param array $wishlist Wishlist data.
	 *
	 * @return string
	 */
	function confirm_remove( $wishlist ) {
		ob_start();
		TInvWL_View::view( 'wishlist-confirm', array(
				'wishlist'   => $wishlist,
				'remove_url' => self::admin_url( '', '', array(
						'wishlist' => $wishlist['ID'],
						'action'   => 'remove',
						'_wpnonce' => wp_create_nonce( 'remove_wishlist' ),
				) ),
		) );

		return ob_get_clean();
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
			case 'author':
				return $item['author'];
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
				'title'   => array( 'title', true ),
				'author'  => array( 'author', false ),
				'privacy' => array( 'status', false ),
				'date'    => array( 'date', false ),
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
	 * Sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return self::get_sortable_columns_static();
	}

	/**
	 * Display the list of views available on this table.
	 */
	public function views() {
		if ( ! tinv_get_option( 'general', 'multi' ) ) {
			return;
		}

		$views = array(
				'public'  => __( 'Public', 'ti-woocommerce-wishlist-premium' ),
				'share'   => __( 'Share', 'ti-woocommerce-wishlist-premium' ),
				'private' => __( 'Private', 'ti-woocommerce-wishlist-premium' ),
		);

		$wl       = new TInvWL_Wishlist( self::$_name );
		$statuses = $wl->get( array( 'sql' => 'SELECT `ID`, `author`, `type`, `status`, COUNT(*) AS `counts` FROM `{table}` GROUP BY `status`' ) );
		$counts   = array( 'all' => 0 );
		foreach ( $statuses as $status ) {
			$counts['all']               += absint( $status['counts'] );
			$counts[ $status['status'] ] = absint( $status['counts'] );
		}
		foreach ( $views as $key => &$name ) {
			if ( array_key_exists( $key, (array) $counts ) ) {
				$name = sprintf( __( '%1$s (%2$d)', 'ti-woocommerce-wishlist-premium' ), $name, $counts[ $key ] );
			} else {
				unset( $views[ $key ] );
			}
		}
		if ( 1 >= count( $views ) ) {
			return;
		}

		$current     = filter_input( INPUT_GET, 'privacy', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$current_url = set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); // @codingStandardsIgnoreLine WordPress.VIP.SuperGlobalInputUsage.AccessDetected
		$current_url = remove_query_arg( array(
				'hotkeys_highlight_last',
				'hotkeys_highlight_first',
				'paged',
		), $current_url );

		foreach ( $views as $key => &$view ) {
			$view = sprintf( '<li class="%s"><a href="%s">%s</a></li>', ( $key == $current ? 'active' : '' ), esc_url( add_query_arg( 'privacy', $key, $current_url ) ), $view ); // WPCS: loose comparison ok.
		}
		$views = tinv_array_merge( array(
				'all' => sprintf( '<li class="%s"><a href="%s">%s (%d)</a></li>', ( empty( $current ) ? 'active' : '' ), esc_url( remove_query_arg( 'privacy', $current_url ) ), __( 'All', 'ti-woocommerce-wishlist-premium' ), $counts['all'] ),
		), $views );
		echo '<ul class="tinwl-wishlists-privacy tinv-wishlist-clearfix">' . implode( '', $views ) . '</ul>'; // WPSC: xss ok.
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

		/** Process all action */
		$this->process_action();
		$this->process_bulk_action();

		$per_page     = apply_filters( 'tinvwl_wishlists_per_page', 10 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( array(
				'total_items' => $total_items, // WE have to calculate the total number of items.
				'per_page'    => $per_page, // WE have to determine how many items to show on a page.
		) );

		$this->items = self::get_wishlists( $per_page, $current_page );

		return $this;
	}

	/**
	 * Create Bulk actions
	 *
	 * @return array
	 */
	function get_bulk_actions() {
		$actions = array(
				'remove' => __( 'Delete', 'ti-woocommerce-wishlist-premium' ),
		);

		return $actions;
	}

	/**
	 * Process action
	 */
	function process_action() {
		$nonce = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! wp_verify_nonce( $nonce, 'remove_wishlist' ) ) {
			return false;
		}
		$data = filter_input_array( INPUT_GET, array(
				'wishlist' => FILTER_VALIDATE_INT,
				'action'   => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		) );
		if ( 'remove' !== $data['action'] || empty( $data['wishlist'] ) ) {
			return self::reloadpage();
		}
		$wl = new TInvWL_Wishlist( self::$_name );
		if ( $wl->remove( $data['wishlist'] ) ) {
			TInvWL_View::set_tips( __( 'Wishlists successfully removed!', 'ti-woocommerce-wishlist-premium' ) );
		} else {
			TInvWL_View::set_tips( __( 'Wishlists is not removed!', 'ti-woocommerce-wishlist-premium' ) );
		}
		self::reloadpage();
	}

	/**
	 * Process bulk action
	 */
	function process_bulk_action() {
		$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$action = 'bulk-' . $this->_args['plural'];
		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			return false;
		}
		$action = $this->current_action();
		if ( 'remove' !== $action ) {
			return false;
		}
		$wishlists = filter_input( INPUT_POST, 'wishlists', FILTER_VALIDATE_INT, FILTER_FORCE_ARRAY );
		if ( empty( $wishlists ) ) {
			return false;
		}
		$wl        = new TInvWL_Wishlist( self::$_name );
		$wishlists = $wl->get( array(
				'ID'    => $wishlists,
				'field' => array( 'ID', 'title', 'author', 'type' ),
		) );

		$wishlists_removed = array(
				array(),
				array(),
		);
		foreach ( $wishlists as $wishlist ) {
			if ( 'default' === $wishlist['type'] ) {
				$user = get_user_by( 'id', $wishlist['author'] );
				if ( $user && $user->exists() ) {
					$wishlist['title'] = sprintf( __( '%1$s by %2$s', 'ti-woocommerce-wishlist-premium' ), $wishlist['title'], $user->display_name );
				}
			}
			if ( $wl->remove( $wishlist['ID'] ) ) {
				$wishlists_removed[0][] = $wishlist['title'];
			} else {
				$wishlists_removed[1][] = $wishlist['title'];
			}
		}
		if ( ! empty( $wishlists_removed[0] ) ) {
			TInvWL_View::set_tips( sprintf( __( 'Successfully deleted Wishlists: %s.', 'ti-woocommerce-wishlist-premium' ), implode( ', ', $wishlists_removed[0] ) ) );
		}
		if ( ! empty( $wishlists_removed[1] ) ) {
			TInvWL_View::set_error( sprintf( __( 'Not have been removed Wishlists: %s.', 'ti-woocommerce-wishlist-premium' ), implode( ', ', $wishlists_removed[1] ) ), 115 );
		}
		self::reloadpage();
	}

	/**
	 * Reload page from JS
	 */
	public static function reloadpage() {
		printf( '<script language = "javascript">document.location.href="%s";</script>', wp_get_referer() ); // WPCS: xss ok.

		return false;
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
