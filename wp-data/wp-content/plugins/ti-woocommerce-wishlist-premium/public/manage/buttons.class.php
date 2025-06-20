<?php
/**
 * Action buttons for Manage Wishlist
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Action buttons for Manage Wishlist
 */
class TInvWL_Public_Manage_Buttons {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	static $_name;
	/**
	 * Basic event
	 *
	 * @var string
	 */
	static $event;

	static $privacy;

	/**
	 * First run function
	 *
	 * @param string $plugin_name Plugin name.
	 */
	public static function init( $plugin_name = TINVWL_PREFIX ) {
		self::$_name   = $plugin_name;
		self::$event   = 'tinvwl_after_wishlistmanage_table';
		self::$privacy = TInvWL_Public_Manage_Wishlist::get_wishlists_privacy();
		self::htmloutput();
	}

	/**
	 * Defined buttons
	 *
	 * @return array
	 */
	private static function prepare() {
		$buttons   = array();
		$buttons[] = array(
			'name'     => 'manage_apply',
			'title'    => sprintf( __( 'Apply %s', 'ti-woocommerce-wishlist-premium' ), "<span class='tinvwl-mobile'>" . __( 'Action', 'ti-woocommerce-wishlist-premium' ) . '</span>' ),
//			'method'   => array( __CLASS__, 'apply_action' ),
			'before'   => array( __CLASS__, 'apply_action_before' ),
			'after'    => '</span>',
			'priority' => 10,
		);
		$buttons[] = array(
			'name'     => 'manage_save',
			'title'    => __( 'Save Wishlists', 'ti-woocommerce-wishlist-premium' ),
//			'method'   => array( __CLASS__, 'save' ),
			'priority' => 30,
		);

		add_filter( 'tinvwl_prepare_attr_button_manage_save', array( __CLASS__, 'class_action' ) );
		$buttons = apply_filters( 'tinvwl_manage_buttons_create', $buttons );

		return $buttons;
	}

	/**
	 * Output buttons
	 */
	public static function htmloutput() {
		$buttons = self::prepare();
		foreach ( $buttons as $button ) {
			self::addbutton( $button );
		}
	}

	/**
	 * Create button and action
	 *
	 * @param array $button Structure for button.
	 *
	 * @return boolean
	 */
	public static function addbutton( $button ) {
		if ( ! array_key_exists( 'name', $button ) ) {
			return false;
		}
		if ( ! array_key_exists( 'priority', $button ) ) {
			$button['priority'] = 10;
		}
		if ( ! array_key_exists( 'method', $button ) ) {
			$button['method'] = array( __CLASS__, 'null_action' );
		}
		if ( ! array_key_exists( 'event', $button ) ) {
			$button['event'] = self::$event;
		}
		if ( ! array_key_exists( 'condition', $button ) ) {
			$button['condition'] = isset( $button['condition'] ) ? $button['condition'] : false;
		}
		if ( array_key_exists( 'submit', $button ) ) {
			$button['submit'] = $button['submit'] ? 'submit' : 'button';
		} else {
			$button['submit'] = 'submit';
		}

		if ( array_key_exists( 'before', $button ) ) {
			add_filter( 'tinvwl_before_button_' . $button['name'], $button['before'] );
		}
		if ( array_key_exists( 'after', $button ) ) {
			add_filter( 'tinvwl_after_button_' . $button['name'], $button['after'] );
		}

		add_action( $button['event'], function () use ( $button ) {
			if ( ! $button['condition'] ) {
				self::button( $button['name'], __( $button['title'] ), $button['submit'] );
			}
		}, $button['priority'] );

		add_action( 'tinvwl_action_' . $button['name'], $button['method'], 10, 4 );
	}


	/**
	 * Create html button
	 *
	 * @param string $value Vaule for tinvwl-action.
	 * @param string $title HTML title for button.
	 * @param string $submit Type button.
	 * @param boolean $echo Retun or echo.
	 *
	 * @return string
	 */
	public static function button( $value, $title, $submit, $echo = true ) {
		$html = apply_filters( 'tinvwl_before_button_' . $value, '' );
		$attr = array(
			'type'  => $submit,
			'class' => 'button tinvwl-button',
			'name'  => 'tinvwl-action-' . $value,
			'value' => $value,
			'title' => esc_attr( wp_strip_all_tags( $title ) ),
		);
		$attr = apply_filters( 'tinvwl_prepare_attr_button_' . $value, $attr );
		foreach ( $attr as $key => &$value ) {
			$value = sprintf( '%s="%s"', $key, esc_attr( $value ) );
		}
		$attr = implode( ' ', $attr );

		$html .= apply_filters( 'tinvwl_button_' . $value, sprintf( '<button %s>%s</button>', $attr, $title ) );
		$html .= apply_filters( 'tinvwl_after_button_' . $value, '' );

		if ( $echo ) {
			echo $html; // WPCS: xss ok.
		} else {
			return $html;
		}
	}

	/**
	 * Default action for button
	 *
	 * @return boolean
	 */
	public static function null_action() {
		return false;
	}

	/**
	 * Add class 'alt' to button
	 *
	 * @param array $attr Attributes for button.
	 *
	 * @return array
	 */
	public static function class_action( $attr ) {
		if ( array_key_exists( 'class', (array) $attr ) ) {
			$attr['class'] .= ' alt';
		} else {
			$attr['class'] = 'alt';
		}

		return $attr;
	}

	/**
	 * Create select for custom action
	 *
	 * @return string
	 */
	public static function apply_action_before() {


		$actions = array(
			''       => __( 'Actions', 'ti-woocommerce-wishlist-premium' ),
			'remove_selected' => __( 'Delete', 'ti-woocommerce-wishlist-premium' ),
		);

		$privacy = is_array( self::$privacy ) ? self::$privacy : array();

		foreach ( $privacy as $k => $v ) {
			$privacy[ $k ] = __( 'Set "' . $v . '"', 'ti-woocommerce-wishlist-premium' );
		}

		$actions = array_merge( $actions, $privacy );

		return TInvWL_Form::_select( 'manage_actions', '', array( 'class' => 'tinvwl-break-input-field  form-control' ), $actions ) . '<span class="tinvwl-input-group-btn">';
	}

	/**
	 * Apply action for manage_apply
	 *
	 * @param array $wishlists Array user wishlists.
	 * @param array $wishlist_pr Array selected wishlists.
	 */
	public static function apply_action( $wishlists, $wishlist_pr ) {
		$action = filter_input( INPUT_POST, 'manage_actions', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		switch ( $action ) {
			case array_key_exists( $action, self::$privacy ):
				$action = array( 'status', $action );
				break;
			default:
				$action = array( $action );
				break;
		}
		$wl     = new TInvWL_Wishlist();
		$result = array();
		foreach ( $wishlists as $wishlist ) {
			$wishlist_id = $wishlist['ID'];
			if ( false === array_search( $wishlist_id, (array) $wishlist_pr, true ) ) {
				continue;
			}
			switch ( $action[0] ) {
				case 'remove':
					if ( $wl->remove( $wishlist_id ) ) {
						$result[ $wishlist_id ] = $wishlist['title'];
					}
					break;
				case 'status':
					if ( ! in_array( $action[1], self::$privacy ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
						$action[1] = 'public';
					}
					if ( $wishlist['status'] !== $action[1] ) {
						$wishlist['status'] = $action[1];
						if ( $wl->update( $wishlist_id, $wishlist ) ) {
							$result[ $wishlist_id ] = $wishlist['title'];
						}
					}
					break;
			}
		}
		if ( ! empty( $result ) ) {
			switch ( $action[0] ) {
				case 'remove':
					wc_add_notice( sprintf( __( 'Successfully deleted wishlists: %s.', 'ti-woocommerce-wishlist-premium' ), implode( ', ', $result ) ) );
					break;
				case 'status':
					$privacy = TInvWL_Public_Manage_Wishlist::instance()::get_wishlists_privacy();
					wc_add_notice( sprintf( __( 'Successfully changed the status to "%1$s" wishlists: %2$s.', 'ti-woocommerce-wishlist-premium' ), ( array_key_exists( $action[1], $privacy ) ? $privacy[ $action[1] ] : $action[1] ), implode( ', ', $result ) ) );
					break;
			}

			return true;
		}

		return false;
	}

	/**
	 * Apply action for manage_save
	 *
	 * @param array $wishlists Array user wishlists.
	 * @param array $wishlist_pr Not used.
	 * @param array $wishlist_privacy Array privacy wishlists.
	 * @param array $wishlist_name Array name wishlists.
	 */
	public static function save( $wishlists, $wishlist_pr, $wishlist_privacy, $wishlist_name ) {
		$wl     = new TInvWL_Wishlist();
		$result = array();

		foreach ( $wishlists as $wishlist ) {
			$need_update       = false;
			$wishlist_id       = $wishlist['ID'];
			$privacy           = 'public';
			$original_wishlist = $wishlist;
			if ( array_key_exists( $wishlist_id, (array) $wishlist_privacy ) ) {
				$_privacy = $wishlist_privacy[ $wishlist_id ];
				if ( in_array( $_privacy, self::$privacy ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
					$privacy = $_privacy;
				}
				if ( $wishlist['status'] !== $privacy ) {
					$need_update = true;
				}
				$wishlist['status'] = $privacy;
			}
			if ( array_key_exists( $wishlist_id, (array) $wishlist_name ) ) {
				$name = trim( $wishlist_name[ $wishlist_id ] );
				if ( 'default' === $wishlist['type'] ) {
					if ( $wishlist['title'] !== $name ) {
						$need_update = true;
					}
					if ( apply_filters( 'tinvwl_default_wishlist_title', tinv_get_option( 'general', 'default_title' ) ) == $name ) { // WPCS: loose comparison ok.
						$name = '';
					}
					$wishlist['title'] = $name;
				} elseif ( ! empty( $name ) ) {
					if ( $wishlist['title'] !== $name ) {
						$need_update = true;
					}
					$wishlist['title'] = $name;
				}
			}
			$wishlist = apply_filters( 'tinvwl_manage_wishlist_update', $wishlist, $wishlist_id );
			if ( apply_filters( 'tinvwl_manage_wishlist_need_update', $need_update, $wishlist_id, $wishlist, $original_wishlist ) ) {
				if ( $wl->update( $wishlist_id, $wishlist ) ) {
					$result[ $wishlist_id ] = ( 'default' === $wishlist['type'] && empty( $wishlist['title'] ) ? apply_filters( 'tinvwl_default_wishlist_title', tinv_get_option( 'general', 'default_title' ) ) : $wishlist['title'] );
				}
			}
		} // End foreach().
		if ( ! empty( $result ) ) {
			wc_add_notice( sprintf( __( 'Successfully updated wishlists: %s.', 'ti-woocommerce-wishlist-premium' ), implode( ', ', $result ) ) );

			return true;
		}

		return false;
	}
}
