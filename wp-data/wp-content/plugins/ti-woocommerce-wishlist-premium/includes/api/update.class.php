<?php
/**
 * License API Managerfunction class
 *
 * @since             1.0.0
 * @package           TInvWishlist\API
 * @subpackage        License
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'TI_Api_Manager_Update' ) ) {
	require_once TINVWL_PATH . 'includes/api/update/class-ti-update.php';
}

/**
 * License API Managerfunction class
 */
class TInvWL_Includes_API_Update extends TI_Api_Manager_Update {

	/**
	 * Self object
	 *
	 * @var \TInvWL_Includes_API_Update
	 */
	protected static $_instance;

	/**
	 * Create object
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 *
	 * @return /TInvWL_Includes_API_Update
	 */
	public static function instance( $plugin_name = '', $version = '' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $plugin_name, $version );
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 * @param string $dictionary Dictonary name.
	 */
	public function __construct( $plugin_name, $version, $dictionary = 'ti-woocommerce-wishlist-premium' ) {
		$this->_name    = empty( $plugin_name ) ? TINVWL_PREFIX : $plugin_name;
		$this->_version = empty( $version ) ? get_option( $this->_name . '_verp', 0 ) : $version;

		$this->upgrade_url       = 'https://templateinvaders.com/';
		$this->renew_license_url = 'https://templateinvaders.com/my-account/';
		$this->product_id        = '993';
		$this->plugin_or_theme   = 'plugin'; // Variable: 'theme' or 'plugin'.

		if ( defined( 'TINVWL_LOAD_PREMIUM' ) ) {
			$this->product_name = untrailingslashit( TINVWL_LOAD_PREMIUM );
		} elseif ( defined( 'TINVWL_LOAD_FREE' ) ) {
			$this->product_name = untrailingslashit( TINVWL_LOAD_FREE );
		}

		parent::__construct( $this->_name, $this->_version, $dictionary );
	}

	/**
	 * Load function
	 */
	public function load_function() {
		$this->define_hooks();
	}

	/**
	 * Add settings page error
	 *
	 * @param mixed $code Error code, used only for type error.
	 * @param string $message Error message.
	 * @param string $type Type error.
	 *
	 * @return boolean
	 */
	function add_settings_error( $code, $message, $type = 'error' ) {
		switch ( $type ) {
			case 'error':
				TInvWL_View::set_error( $message, $code );
				break;
			case 'attentions':
				TInvWL_View::set_attentions( $message );
				break;
			case 'tips':
			default:
				TInvWL_View::set_tips( $message );
				break;
		}

		return false;
	}

	/**
	 * Define hooks
	 */
	function define_hooks() {
		add_filter( 'tinvwl_view_panelstatus', array( $this, 'status_panel' ), 9999 );

		add_filter( 'tinvwl_admin_menu', array( $this, 'adminmenu' ), 1000 );
		add_filter( 'tinvwl_section_before', array( $this, 'start_form' ) );
		add_filter( 'tinvwl_section_after', array( $this, 'end_form' ) );
	}

	/**
	 * Add item to admin menu
	 *
	 * @param array $data Menu.
	 *
	 * @return array
	 */
	function adminmenu( $data ) {
		$data[] = $this->menu();

		return $data;
	}

	/**
	 * Menu array
	 *
	 * @return array
	 */
	function menu() {
		return array(
			'title'  => __( 'License & Updates', 'ti-woocommerce-wishlist-premium' ),
			'method' => array( $this, '_print_' ),
			'slug'   => 'license',
		);
	}

	/**
	 * Output status button
	 *
	 * @param array $panel Array button.
	 *
	 * @return array
	 */
	function status_panel( $panel ) {
		if ( $this->status() ) {
			$panel[] = sprintf( '<a class="tinvwl-btn grey w-icon smaller-txt" href="%s"><i class="ftinvwl ftinvwl-star"></i><span class="tinvwl-txt">%s</span></a>', 'https://templateinvaders.com/help/?utm_source=support&utm_campaign=wishlist_premium&utm_medium=plugin', __( 'support', 'ti-woocommerce-wishlist-premium' ) );
		} else {
			$panel[] = sprintf( '<a class="tinvwl-btn red w-icon smaller-txt" href="%s"><i class="ftinvwl ftinvwl-star"></i><span class="tinvwl-txt">%s</span></a>', $this->admin_url( 'license' ), __( 'activate license', 'ti-woocommerce-wishlist-premium' ) );
		}

		return $panel;
	}

	/**
	 * Constructor settings page form
	 *
	 * @return array
	 */
	function constructor_data() {
		$license_status = $this->status( true );
		$this->_v_new   = $this->get_inapi_version();
		$update_exist   = version_compare( $this->_v_new, $this->_version, 'gt' );

		return array(
			array(
				'id'         => 'license',
				'title'      => __( 'API License', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => '',
				'show_names' => false,
				'fields'     => array(
					array(
						'type'  => 'button',
						'name'  => 'status',
						'text'  => __( 'API License Key Status', 'ti-woocommerce-wishlist-premium' ),
						'std'   => ( $license_status ? '<i class="ftinvwl ftinvwl-check"></i>' . __( 'Plugin Activated', 'ti-woocommerce-wishlist-premium' ) : '<i class="ftinvwl ftinvwl-exclamation-triangle"></i>' . __( 'Not Activated', 'ti-woocommerce-wishlist-premium' ) ),
						'extra' => array(
							'disabled' => 'disabled',
							'class'    => 'tinvwl-license-' . ( $license_status ? '' : 'not-' ) . 'activated tinvwl-btn medium smaller-txt w-icon ' . ( $license_status ? 'xs-icon' : 'md-icon red' ),
						),
						'class' => 'tinvwl-header-row',
					),
					array(
						'type' => 'groupHTML',
						'id'   => 'licenceinfo',
						'html' => '<div class="tinvwl-inner">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <div class="col-lg-6">
                                        <div class="tinvwl-img-w-desc tinvwl-table auto-width"><i class="admin-rescue"></i>
                                        <div class="tinvwl-cell">
											<h5>' . __( 'Dedicated Support', 'ti-woocommerce-wishlist-premium' ) . '</h5>
											<div class="tinvwl-desc">' . __( 'Direct help from our qualified support team', 'ti-woocommerce-wishlist-premium' ) . '</div>
                                        </div>
                                        </div>
                                        </div>

                                        <div class="col-lg-6">
                                        <div class="tinvwl-img-w-desc tinvwl-table auto-width"><i class="admin-update"></i>
                                        <div class="tinvwl-cell">
											<h5>' . __( 'Live Updates', 'ti-woocommerce-wishlist-premium' ) . '</h5>
											<div class="tinvwl-desc">' . __( 'Stay up to date with automatic updates', 'ti-woocommerce-wishlist-premium' ) . '</div>
                                        </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
							</div>',
					),
					array(
						'type' => 'group',
						'id'   => 'licence',
					),
					array(
						'type'     => 'text',
						'name'     => 'licence_key',
						'text'     => __( 'API License Key', 'ti-woocommerce-wishlist-premium' ),
						'std'      => '',
						'desc'     => sprintf( __( 'You can learn how to find your license key %s.', 'ti-woocommerce-wishlist-premium' ), sprintf( '<a target="_blank" href="%s">%s</a>', 'https://templateinvaders.com/license-activation/?utm_source=license_activation&utm_campaign=wishlist_premium&utm_medium=plugin', __( 'here', 'ti-woocommerce-wishlist-premium' ) ) ),
						// @todo add link to learn how to find your license key
						'validate' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
						'extra'    => ( $license_status ? array( 'disabled' => 'disabled' ) : array() ),
					),
					array(
						'type'     => 'text',
						'name'     => 'email',
						'text'     => __( 'API License email', 'ti-woocommerce-wishlist-premium' ),
						'std'      => '',
						'desc'     => __( 'Enter email used for purchase of this plugin.', 'ti-woocommerce-wishlist-premium' ),
						'validate' => FILTER_VALIDATE_EMAIL,
						'extra'    => ( $license_status ? array( 'disabled' => 'disabled' ) : array() ),
					),
					( $license_status ?
						array(
							'type'  => 'html',
							'name'  => 'deactivate_license',
							'text'  => '',
							'std'   => '<div class="tinvwl-btns-group tinv-wishlist-clearfix">{button_submit}</div>',
							'extra' => array(
								'button_submit' => TInvWL_Form::_button_submit( 'license-deactivate_license', __( 'Unregister wishlist plugin', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'tinvwl-btn large grey' ) ),
							),
						) :
						array(
							'type'  => 'html',
							'name'  => 'activate_license',
							'text'  => '',
							'std'   => '<div class="tinvwl-btns-group tinv-wishlist-clearfix">{button_submit}</div>',
							'extra' => array(
								'button_submit' => TInvWL_Form::_button_submit( 'license-activate_license', __( 'Register wishlist plugin', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'tinvwl-btn large' ) ),
							),
						) ),
				),
			),
			array(
				'id'         => 'updates',
				'title'      => __( 'Plugin Updates', 'ti-woocommerce-wishlist-premium' ),
				'desc'       => '',
				'show_names' => false,
				'noform'     => true,
				'style'      => ( $license_status ? '' : 'display:none;' ),
				'fields'     => array(
					array(
						'type'  => 'button',
						'name'  => 'status',
						'text'  => __( 'Plugin Updates', 'ti-woocommerce-wishlist-premium' ),
						'std'   => ( $update_exist ? '<i class="ftinvwl ftinvwl-exclamation-triangle"></i>' . __( 'Plugin is Outdated', 'ti-woocommerce-wishlist-premium' ) : '<i class="ftinvwl ftinvwl-check"></i>' . __( 'Plugin is up to Date', 'ti-woocommerce-wishlist-premium' ) ),
						'desc'  => sprintf( __( 'You can learn how to manually update plugin %s.', 'ti-woocommerce-wishlist-premium' ), sprintf( '<a target="_blank" href="%s">%s</a>', '//templateinvaders.com/documentation/ti-woocommerce-wishlist/installation/#update', __( 'here', 'ti-woocommerce-wishlist-premium' ) ) ),
						// @todo add link to learn how to manually update plugin
						'extra' => array(
							'disabled' => 'disabled',
							'class'    => 'tinvwl-btn medium smaller-txt w-icon ' . ( $update_exist ? 'md-icon orange' : 'xs-icon' ),
						),
						'class' => 'tinvwl-header-row',
					),
					array(
						'type' => 'group',
						'id'   => 'versions',
					),
					array(
						'type'  => 'text',
						'name'  => 'cur_version',
						'text'  => __( 'Installed Version', 'ti-woocommerce-wishlist-premium' ),
						'std'   => $this->_version,
						'extra' => array(
							'class'    => 'text-right',
							'disabled' => 'disabled',
						),
					),
					array(
						'type'  => 'text',
						'name'  => 'new_version',
						'text'  => __( 'Latest Available Version', 'ti-woocommerce-wishlist-premium' ),
						'std'   => $this->_v_new,
						'extra' => array(
							'class'    => 'text-right',
							'disabled' => 'disabled',
						),
					),
					array(
						'type'  => 'html',
						'name'  => 'action_version',
						'text'  => '',
						'std'   => '<div class="tinvwl-btns-group tinv-wishlist-clearfix">{button_update}{button_check}</div>',
						'extra' => array(
							'button_check'  => TInvWL_Form::_button_submit( 'action_version_check', __( 'Check for Updates', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'tinvwl-btn large grey' ) ),
							'button_update' => ( $update_exist ? TInvWL_Form::_button_submit( 'action_version_update', __( 'Update Plugin', 'ti-woocommerce-wishlist-premium' ), array( 'class' => 'tinvwl-btn large' ) ) : '' ),
						),
					),
				),
			),
		);
	}

	/**
	 * Constructor load variable for form
	 *
	 * @return type
	 */
	function constructor_load() {
		return array( 'license' => $this->get_option( 'license', '', array() ) );
	}

	/**
	 * Constructor save variable for form
	 *
	 * @param array $data Value from form.
	 *
	 * @return boolean
	 */
	function constructor_save( $data ) {
		if ( empty( $data ) || ! is_array( $data ) ) {
			return false;
		}
		if ( array_key_exists( 'license', $data ) ) {
			$license = $data['license'];
			if ( ! empty( $license['deactivate_license'] ) ) {
				if ( $this->deactivate() ) {
					$this->status( true );

					return TInvWL_View::set_redirect( $this->admin_url( 'license' ) );
				}
			} elseif ( ! empty( $license['activate_license'] ) ) {
				if ( ! empty( $license['licence_key'] ) && ! empty( $license['email'] ) ) {
					if ( $this->validate( $license['licence_key'], $license['email'] ) ) {
						$this->status( true );

						return TInvWL_View::set_redirect( $this->admin_url( 'license' ) );
					}
				} else {
					return TInvWL_View::set_error( __( 'Fields "API License Key" and "API License email" could not be empty!', 'ti-woocommerce-wishlist-premium' ), 247 );
				}
			}
		}
		$post = filter_input_array( INPUT_POST, array(
			'action_version_check'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'action_version_update' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		) );
		if ( ! empty( $post['action_version_check'] ) ) {
			$this->_v_new = $this->get_inapi_version( true );

			return TInvWL_View::set_redirect( $this->admin_url( 'license' ) );
		} elseif ( ! empty( $post['action_version_update'] ) ) {
			delete_site_transient( 'update_plugins' );
			wp_update_plugins();
			$file = '';
			if ( defined( 'TINVWL_LOAD_PREMIUM' ) ) {
				$file = TINVWL_LOAD_PREMIUM;
			} elseif ( defined( 'TINVWL_LOAD_FREE' ) ) {
				$file = TINVWL_LOAD_FREE;
			}
			if ( empty( $file ) ) {
				return TInvWL_View::set_redirect( admin_url( 'plugins.php' ) );
			} else {
				return TInvWL_View::set_redirect( htmlspecialchars_decode( wp_nonce_url( admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file, 'upgrade-plugin_' . $file ) ) );
			}
		}
	}

	/**
	 * Output License settings page
	 */
	function _print_() {
		$title = $this->menu();
		$title = isset( $title['page_title'] ) ? $title['page_title'] : $title['title'];
		$data  = array(
			'_header' => $title,
		);

		$view     = new TInvWL_ViewSection( $this->_name, $this->_version );
		$sections = $this->constructor_data();

		$view->load_data( $sections );
		$this->constructor_save( $view->post_form() );
		$view->load_value( $this->constructor_load() );

		TInvWL_View::render( $view, $view->form_data( $data ) );
	}

	/**
	 * Form start for section
	 *
	 * @param string $content Sections content.
	 *
	 * @return string
	 */
	function start_form( $content ) {
		$content .= '<form method="POST" autocomplete="off">';

		return $content;
	}

	/**
	 * Form end for section
	 *
	 * @param string $content Sections content.
	 *
	 * @return string
	 */
	function end_form( $content ) {
		$content .= '</form>';

		return $content;
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
	public function admin_url( $page, $cat = '', $arg = array() ) {
		$protocol = is_ssl() ? 'https' : 'http';
		$glue     = '-';
		$params   = array(
			'page' => $this->_name . $glue . $page,
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
}
