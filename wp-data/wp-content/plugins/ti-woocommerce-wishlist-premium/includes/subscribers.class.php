<?php
/**
 * Subscribers function class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Subscribers
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Subscribers function class
 */
class TInvWL_Subscribers {

	/**
	 * Table name
	 *
	 * @var string
	 */
	private $table;
	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $_name;
	/**
	 * Wishlist object
	 *
	 * @var array
	 */
	public $wishlist;
	/**
	 * User profile
	 *
	 * @var object
	 */
	public $user;

	/**
	 * Constructor
	 *
	 * @param array $wishlist Object wishlist.
	 * @param string $plugin_name Plugin name.
	 *
	 * @global wpdb $wpdb
	 */
	function __construct( $wishlist, $plugin_name = TINVWL_PREFIX ) {
		global $wpdb;

		$this->wishlist = (array) $wishlist;
		$this->_name    = $plugin_name;
		$this->table    = sprintf( '%s%s_%s', $wpdb->prefix, $this->_name, 'subscribers' );
		$this->user     = wp_get_current_user();
	}

	/**
	 * Get wishlist id
	 *
	 * @return int
	 */
	function wishlist_id() {
		if ( is_array( $this->wishlist ) && array_key_exists( 'ID', $this->wishlist ) ) {
			return $this->wishlist['ID'];
		}

		return 0;
	}

	/**
	 * Get author wishlist
	 *
	 * @return int
	 */
	function wishlist_author() {
		if ( is_array( $this->wishlist ) && array_key_exists( 'author', $this->wishlist ) ) {
			return $this->wishlist['author'];
		}

		return 0;
	}

	/**
	 * Get curent user ID
	 *
	 * @return integer
	 */
	function user_id() {
		return get_current_user_id();
	}

	/**
	 * Get curent user email
	 *
	 * @return string
	 */
	function user_email() {
		$user = wp_get_current_user();
		if ( $user->exists() ) {
			return $user->user_email;
		}

		return false;
	}

	/**
	 * Add subscriber
	 *
	 * @param array $data User info.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 */
	function add( $data = array() ) {
		$default = array(
			'wishlist_id' => $this->wishlist_id(),
			'user_id'     => $this->user_id(),
			'user_email'  => $this->user_email(),
			'user_type'   => 0,
		);
		$data    = filter_var_array( $data, array(
			'wishlist_id' => FILTER_VALIDATE_INT,
			'user_id'     => FILTER_VALIDATE_INT,
			'user_email'  => FILTER_VALIDATE_EMAIL,
			'user_type'   => FILTER_VALIDATE_INT,
		) );
		$data    = array_filter( $data );
		$data    = tinv_array_merge( $default, $data );

		if ( empty( $data['user_email'] ) || empty( $data['wishlist_id'] ) ) {
			return false;
		}
		if ( $this->wishlist_author() == $data['user_id'] ) { // WPCS: loose comparison ok.
			return false;
		}

		$c_data = array(
			'wishlist_id' => $data['wishlist_id'],
			'count'       => 1,
		);
		if ( empty( $data['user_id'] ) ) {
			$c_data['user_email'] = $data['user_email'];
		} else {
			$c_data['user_id'] = $data['user_id'];
		}
		if ( 0 < count( $this->get( $c_data ) ) ) {
			return false;
		}

		global $wpdb;
		if ( $wpdb->insert( $this->table, $data ) ) { // @codingStandardsIgnoreLine WordPress.VIP.DirectDatabaseQuery.DirectQuery
			$id = $wpdb->insert_id;

			return $id;
		}

		return false;
	}

	/**
	 * Get subscriber mask
	 *
	 * @param array $data Requset.
	 *
	 * @return integer
	 */
	function get_current_mask( $data = array() ) {
		$subscribes = $this->get_current( $data );
		if ( empty( $subscribes ) ) {
			return null;
		}

		return $subscribes['user_type'];
	}

	/**
	 * Get subscriber by wishlist
	 *
	 * @param array $data Requset.
	 *
	 * @return array
	 */
	function get_by_wishlist( $data = array() ) {
		if ( empty( $data['wishlist_id'] ) ) {
			$data['wishlist_id'] = $this->wishlist_id();
		}
		if ( empty( $data['wishlist_id'] ) ) {
			return array();
		}

		return $this->get( $data );
	}

	/**
	 * Get subscriber mask
	 *
	 * @param array $data Requset.
	 *
	 * @return array
	 */
	function get_current( $data = array() ) {
		$default = array(
			'wishlist_id' => $this->wishlist_id(),
			'user_id'     => $this->user_id(),
			'user_email'  => $this->user_email(),
		);
		$data    = filter_var_array( $data, array(
			'wishlist_id' => FILTER_VALIDATE_INT,
			'user_id'     => FILTER_VALIDATE_INT,
			'user_email'  => FILTER_VALIDATE_EMAIL,
		) );
		$data    = array_filter( $data );
		$data    = tinv_array_merge( $default, $data );
		if ( empty( $data['wishlist_id'] ) ) {
			return null;
		}
		if ( ! empty( $data['user_email'] ) ) {
			unset( $data['user_id'] );
		}
		$subscribes = $this->get( $data );
		$subscribes = array_shift( $subscribes );

		return $subscribes;
	}

	/**
	 * Get subscribers
	 *
	 * @param array $data Requset.
	 *
	 * @return array
	 * @global wpdb $wpdb
	 */
	function get( $data = array() ) {
		$default = array(
			'count'    => 10,
			'field'    => null,
			'offset'   => 0,
			'order'    => 'ASC',
			'order_by' => 'user_type',
		);

		foreach ( array_keys( $default ) as $_k ) {
			if ( array_key_exists( $_k, $data ) ) {
				$default[ $_k ] = $data[ $_k ];
				unset( $data[ $_k ] );
			}
		}

		if ( is_array( $default['field'] ) ) {
			$default['field'] = '`' . implode( '`,`', $default['field'] ) . '`';
		} elseif ( is_string( $default['field'] ) ) {
			$default['field'] = array( 'ID', 'type', $default['field'] );
			$default['field'] = '`' . implode( '`,`', $default['field'] ) . '`';
		} else {
			$default['field'] = '*';
		}
		$sql = "SELECT {$default[ 'field' ]} FROM `{$this->table}`";

		$where = '';
		if ( ! empty( $data ) && is_array( $data ) ) {
			foreach ( $data as $f => $v ) {
				$s = is_array( $v ) ? ' IN ' : '=';
				if ( is_array( $v ) ) {
					$v = "'" . implode( "','", $v ) . "'";
					$v = "($v)";
				} else {
					$v = "'$v'";
				}
				$data[ $f ] = sprintf( '`%s`%s%s', $f, $s, $v );
			}
			$where = ' WHERE ' . implode( ' AND ', $data );
			$sql   .= $where;
		}
		$sql .= sprintf( ' ORDER BY `%s` %s LIMIT %d,%d;', $default['order_by'], $default['order'], $default['offset'], $default['count'] );

		if ( ! empty( $default['sql'] ) ) {
			$replacer    = $replace = array();
			$replace[0]  = '{table}';
			$replacer[0] = $this->table;
			$replace[1]  = '{where}';
			$replacer[1] = $where;

			foreach ( $default as $key => $value ) {
				$i = count( $replace );

				$replace[ $i ]  = '{' . $key . '}';
				$replacer[ $i ] = $value;
			}

			$sql = str_replace( $replace, $replacer, $default['sql'] );
		}

		global $wpdb;
		$subscribers = $wpdb->get_results( $sql, ARRAY_A ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.

		if ( empty( $subscribers ) ) {
			return array();
		}

		$_subscribers = array();
		foreach ( $subscribers as $subscriber ) {
			unset( $subscriber['ID'] );
			$subscriber['wishlist_id'] = absint( $subscriber['wishlist_id'] );
			$subscriber['user_type']   = absint( $subscriber['user_type'] );
			$subscriber['events']      = $this->prepare_event( $subscriber['user_type'] );
			$subscriber['user_id']     = absint( $subscriber['user_id'] );
			if ( ! empty( $subscriber['user_id'] ) ) {
				$user = get_userdata( $subscriber['user_id'] );
				if ( $user->exists() ) {
					$subscriber['user_email'] = $user->user_email;
				}
			}
			if ( ! empty( $subscriber['user_email'] ) ) {
				$_subscribers[] = $subscriber;
			}
		}

		return $_subscribers;
	}

	/**
	 * Update Subscriber
	 *
	 * @param array $data User info.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 */
	function update( $data ) {
		$default = array(
			'wishlist_id' => $this->wishlist_id(),
			'user_id'     => $this->user_id(),
			'user_email'  => $this->user_email(),
			'user_type'   => 7,
		);
		$data    = filter_var_array( $data, array(
			'wishlist_id' => FILTER_VALIDATE_INT,
			'user_id'     => FILTER_VALIDATE_INT,
			'user_email'  => FILTER_VALIDATE_EMAIL,
			'user_type'   => FILTER_VALIDATE_INT,
		) );
		$data    = array_filter( $data );
		$data    = tinv_array_merge( $default, $data );
		if ( empty( $data['user_email'] ) || empty( $data['wishlist_id'] ) ) {
			return false;
		}
		global $wpdb;
		if ( ! empty( $data['user_id'] ) ) {
			return false !== $wpdb->update( $this->table, $data, array(
					'user_id'     => $data['user_id'],
					'wishlist_id' => $data['wishlist_id'],
				) ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
		}
		if ( ! empty( $data['user_email'] ) ) {
			return false !== $wpdb->update( $this->table, $data, array(
					'user_email'  => $data['user_email'],
					'wishlist_id' => $data['wishlist_id'],
				) ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
		}

		return false;
	}

	/**
	 * Delete subscriber
	 *
	 * @param integer $wishlist_id If exist wishlist object, you can put 0.
	 * @param integer $user_id User id.
	 * @param string $user_email User email.
	 *
	 * @return boolean
	 * @global wpdb $wpdb
	 */
	function delete( $wishlist_id = 0, $user_id = 0, $user_email = '' ) {
		$wishlist_id = absint( $wishlist_id );
		$user_email  = filter_var( $user_email, FILTER_VALIDATE_EMAIL );
		if ( empty( $wishlist_id ) ) {
			$wishlist_id = $this->wishlist_id();
		}
		if ( empty( $user_id ) ) {
			$user_id = $this->user_id();
		}
		if ( empty( $user_email ) ) {
			$user_email = $this->user_email();
		}
		if ( empty( $wishlist_id ) ) {
			return false;
		}
		global $wpdb;
		if ( ! empty( $user_id ) ) {
			return false !== $wpdb->delete( $this->table, array(
					'wishlist_id' => $wishlist_id,
					'user_id'     => $user_id,
				) ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
		}
		if ( $user_email ) {
			return false !== $wpdb->delete( $this->table, array(
					'wishlist_id' => $wishlist_id,
					'user_email'  => $user_email,
				) ); // WPCS: db call ok; no-cache ok; unprepared SQL ok.
		}

		return false;
	}

	/**
	 * Explode mask
	 *
	 * @param integer $mask Mask event subscribe.
	 *
	 * @return array
	 */
	private function prepare_mask( $mask ) {
		$event = array();
		$step  = 1;
		while ( 0 < $mask ) {
			if ( 1 === $mask % 2 ) {
				$event[] = $step;
			}
			$step *= 2;
			$mask = floor( $mask / 2 );
		}

		return $event;
	}

	/**
	 * Get name events by mask
	 *
	 * @param integer $mask Mask event subscribe.
	 *
	 * @return array
	 */
	function prepare_event( $mask ) {
		$events = self::event_lists();
		$mask   = $this->prepare_mask( $mask );
		foreach ( array_keys( $events ) as $key ) {
			if ( ! in_array( $key, $mask ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
				unset( $events[ $key ] );
			}
		}

		return $events;
	}

	/**
	 * Array of event status
	 *
	 * @return array
	 */
	public static function event_lists() {
		return array(
			1  => __( 'Product added', 'ti-woocommerce-wishlist-premium' ),
			2  => __( 'Product removed', 'ti-woocommerce-wishlist-premium' ),
			4  => __( 'Product removed on purchase', 'ti-woocommerce-wishlist-premium' ),
			8  => __( 'Product quantity changed', 'ti-woocommerce-wishlist-premium' ),
			16 => __( 'Out of stock', 'ti-woocommerce-wishlist-premium' ),
			32 => __( 'In stock', 'ti-woocommerce-wishlist-premium' ),
			64 => __( 'Low stock', 'ti-woocommerce-wishlist-premium' ),
		);
	}
}
