<?php
/**
 * Admin pages class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Admin pages class
 */
class TInvWL_Admin_TInvWL extends TInvWL_Admin_Base {

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	function __construct( $plugin_name, $version ) {
		$this->_name    = $plugin_name;
		$this->_version = $version;

		TIUpdater( $this->_name, $this->_version );
		$this->maybe_update();
	}

	/**
	 * Testing for the ability to update the functional
	 */
	function maybe_update() {
		$current = 'p.' . $this->_version;
		$prev    = $this->get_current_version( $current );
		$prev_p  = get_option( $this->_name . '_verp' );
		if ( false === $prev_p ) {
			add_option( $this->_name . '_verp', $this->_version );
		}
		if ( version_compare( $current, $prev, 'gt' ) ) {
			new TInvWL_Update( $current, $prev );
			TInvWL_Activator::update();
			update_option( $this->_name . '_verp', $this->_version );
			do_action( 'tinvwl_updated', $current, $prev );
		}
	}

	/**
	 * Get current version
	 *
	 * @param string $version Default Version.
	 *
	 * @return string
	 */
	function get_current_version( $version = false ) {
		$_version = get_option( $this->_name . '_verp' );
		if ( false !== $_version ) {
			return 'p.' . $_version;
		}
		$_version = get_option( $this->_name . '_ver' );
		if ( false !== $_version ) {
			return 'f.' . $_version;
		}

		return $version;
	}

	/**
	 * Load functions.
	 * Create Wishlist and Product class.
	 * Load settings classes.
	 */
	function load_function() {
		$this->wishlist = new TInvWL_Admin_Wishlist( $this->_name, $this->_version );
		$this->product  = new TInvWL_Admin_Product( $this->_name, $this->_version );
		$this->load_settings();

		$this->define_hooks();
	}

	/**
	 * Load settings classes.
	 *
	 * @return boolean
	 */
	function load_settings() {
		$dir = TINVWL_PATH . 'admin/settings/';
		if ( ! file_exists( $dir ) || ! is_dir( $dir ) ) {
			return false;
		}
		$files = scandir( $dir );
		foreach ( $files as $value ) {
			if ( preg_match( '/\.class\.php$/i', $value ) ) {
				$file  = preg_replace( '/\.class\.php$/i', '', $value );
				$class = 'TInvWL_Admin_Settings_' . ucfirst( $file );
				$class::instance( $this->_name, $this->_version );
			}
		}

		return true;
	}

	/**
	 * Sync emails settings between WooCommerce emails settings and Wishlists Emails settings page
	 */
	function sync_email_settings() {
		if ( tinv_get_option( 'notification_stock_email', 'enabled' ) ) {
			add_action( 'woocommerce_product_set_stock_status', array( $this, 'set_stock_status' ), 100, 2 );
			add_action( 'woocommerce_variation_set_stock_status', array( $this, 'set_stock_status' ), 100, 2 );
		}
		if ( tinv_get_option( 'notification_price_email', 'enabled' ) ) {
			add_action( 'updated_postmeta', array( $this, 'updated_postmeta_price' ), 100, 4 );
		}
		if ( tinv_get_option( 'notification_low_stock_email', 'enabled' ) ) {
			add_action( 'woocommerce_variation_set_stock', array( $this, 'product_low_stock' ) );
			add_action( 'woocommerce_product_set_stock', array( $this, 'product_low_stock' ) );
		}
	}

	/**
	 * Define hooks
	 */
	function define_hooks() {
		add_action( 'init', array( $this, 'preview_email' ), 1000 );
		add_action( 'admin_menu', array( $this, 'action_menu' ) );
		add_action( 'init', array( $this, 'sync_email_settings' ) );
		add_action( 'tinvwl_send_promotional_error', array( $this, 'email_promotional_error' ), 10, 4 );
		add_action( 'tinvwl_send_promotional_successfully', array(
			$this,
			'email_promotional_successfully',
		), 10, 3 );

		add_action( 'wp_ajax_prepare_promotion', array( $this->product, 'preview_email_presave' ) );
		add_action( 'init', array( $this->product, 'preview_email' ), 1000 );
		if ( 'skip' === filter_input( INPUT_GET, $this->_name . '-wizard' ) ) {
			update_option( $this->_name . '_wizard', true );
		}
		if ( ! get_option( $this->_name . '_wizard' ) ) {
			add_action( 'admin_notices', array( $this, 'wizard_run_admin_notice' ) );
		} elseif ( ! tinv_get_option( 'page', 'wishlist' ) ) {
			add_action( 'admin_notices', array( $this, 'empty_page_admin_notice' ) );
		}
		add_action( 'woocommerce_system_status_report', array( $this, 'system_report_templates' ) );
		add_action( 'switch_theme', array( $this, 'admin_notice_outdated_templates' ) );
		add_action( 'tinvwl_updated', array( $this, 'admin_notice_outdated_templates' ) );


		// Add a post display state for special WC pages.
		add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 10, 2 );

		add_action( 'tinvwl_admin_promo_footer', array( $this, 'promo_footer' ) );
		add_action( 'tinvwl_remove_without_author_wishlist', array( $this, 'remove_old_wishlists' ) );
		$this->scheduled_remove_wishlist();

		add_action( 'enqueue_block_editor_assets', array( $this, 'woocommerce_blocks_editor' ), 10, 2 );

		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'add_inline_scripts' ) );
		add_action( 'elementor/app/init', array( $this, 'add_inline_scripts' ) );

	}

	/**
	 * Error notice if wizard didn't run.
	 */
	function wizard_run_admin_notice() {
		printf( '<div class="notice notice-error"><p>%1$s</p><p><a href="%2$s" class="button-primary">%3$s</a> <a href="%4$s" class="button-secondary">%5$s</a></p></div>',
			__( '<strong>Welcome to WooCommerce Wishlist</strong> – You‘re almost ready to start :)', 'ti-woocommerce-wishlist-premium' ), // @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			esc_url( admin_url( 'index.php?page=tinvwl-wizard' ) ),
			esc_html__( 'Run the Setup Wizard', 'ti-woocommerce-wishlist-premium' ),
			esc_url( admin_url( 'index.php?page=' . $this->_name . '&' . $this->_name . '-wizard=skip' ) ),
			esc_html__( 'Skip Setup', 'ti-woocommerce-wishlist-premium' )
		);
	}

	/**
	 * Error notice if wishlist page not set.
	 */
	function empty_page_admin_notice() {
		printf( '<div class="notice notice-error is-dismissible tinvwl-empty-page-notice" style="position: relative;"><h4>%1$s</h4><p>%2$s</p><ol><li>%3$s</li><li>%4$s</li><li>%5$s</li></ol><p><a href="%6$s">%7$s</a>%8$s<a href="%9$s">%10$s</a></p></div>', // @codingStandardsIgnoreLine WordPress.XSS.EscapeOutput.OutputNotEscaped
			esc_html__( 'WooCommerce Wishlist Plugin is misconfigured!', 'ti-woocommerce-wishlist-premium' ),
			esc_html__( 'Since the Setup Wizard was skipped, the Wishlist may function improperly.', 'ti-woocommerce-wishlist-premium' ),
			esc_html__( 'Create a New Page or open to edit a page where the Wishlist should be displayed.', 'ti-woocommerce-wishlist-premium' ),
			__( 'Add <code>[ti_wishlistsview]</code> shortcode into a page content.', 'ti-woocommerce-wishlist-premium' ),
			esc_html__( 'In a plugin General Settings section apply this page as a "Wishlist" page.', 'ti-woocommerce-wishlist-premium' ),
			esc_url( $this->admin_url( '' ) . '#general' ),
			esc_html__( 'Please apply the Wishlist page', 'ti-woocommerce-wishlist-premium' ),
			esc_html__( ' or ', 'ti-woocommerce-wishlist-premium' ),
			esc_url( admin_url( 'index.php?page=tinvwl-wizard' ) ),
			esc_html__( 'Run the Setup Wizard', 'ti-woocommerce-wishlist-premium' )
		);
	}

	/**
	 * Check change product price
	 *
	 * @param integer $meta_id Not Used.
	 * @param integer $object_id Product or Variation ID.
	 * @param strung $meta_key Name meta key.
	 * @param mixed $meta_value Price value.
	 *
	 * @return boolean
	 */
	function updated_postmeta_price( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( '_price' === $meta_key ) {
			$object_id = $product_id = absint( $object_id );
			$product   = wc_get_product( $object_id );
			$variable  = false;
			if ( $product ) {
				if ( in_array( $product->get_type(), array(
					'variable',
					'variation',
					'variable-subscription'
				) ) ) {
					$product_id = $product->get_parent_id();
					$variable   = true;
				} else {
					$product_id = $product->get_id();
				}
			}
			$meta_value = floatval( $meta_value );
			$wlp        = new TInvWL_Product( array(), $this->_name );
			$products   = $wlp->get( array(
				'external' => false,
				'sql'      => "SELECT * FROM `{table}` WHERE ( ( `product_id` = $product_id  AND `variation_id` = 0 ) OR `variation_id` = $object_id ) AND `price` <> $meta_value AND `author` > 0",
			) );
			if ( empty( $products ) ) {
				return false;
			}
			$wishlist_ids = array();

			foreach ( $products as $key => $_product ) {
				$wlp->update( $_product );
				$_product['price'] = floatval( $_product['price'] );
				if ( $meta_value < $_product['price'] ) {
					$wishlist_ids[] = $_product['wishlist_id'];
					continue;
				}
				unset( $products[ $key ] );
			}

			$wishlist_ids = array_unique( $wishlist_ids, SORT_NUMERIC );
			if ( ! empty( $wishlist_ids ) ) {
				$wl        = new TInvWL_Wishlist( $this->_name );
				$wishlists = $wl->get( array(
					'ID' => $wishlist_ids,
				) );
				foreach ( $products as $product ) {
					$_product = $wlp->product_data( $product['product_id'], $product['variation_id'] );
					if ( ! empty( $_product ) ) {
						$users = array();
						foreach ( $wishlists as $wishlist ) {
							if ( $wishlist['ID'] != $product['wishlist_id'] ) {
								continue;
							}

							if ( $variable && $product['variation_id'] === '0' ) {

								if ( get_transient( 'tinvwl_notification_price_' . $wishlist['author'] . '_' . $product['product_id'] ) ) {
									continue;
								}
								set_transient( 'tinvwl_notification_price_' . $wishlist['author'] . '_' . $product['product_id'], '1', 30 );
							}
							$users[ $wishlist['author'] ][] = $wishlist['ID'];
						}
						if ( function_exists( 'WC' ) ) {
							WC()->mailer();
						}

						foreach ( $users as $user_id => $author_wishlists ) {
							do_action( 'tinvwl_send_notification_price', $_product, $user_id, $author_wishlists );
						}
					}
				}
			}
		} // End if().
	}


	/**
	 * Product low stock notification
	 *
	 * @param WC_Product $product Product.
	 *
	 * @return boolean
	 */
	public static function product_low_stock( $product ) {
		if ( ! $product ) {
			return;
		}
		$stock_amount = $product->get_stock_quantity();
		if ( 0 < $stock_amount && $stock_amount <= wc_get_low_stock_amount( $product ) ) {
			$variable = false;
			if ( $product ) {
				if ( in_array( $product->get_type(), array(
					'variable',
					'variation',
					'variable-subscription'
				) ) ) {
					$product_id   = $product->get_parent_id();
					$variation_id = $product->get_id();
					$variable     = true;
				} else {
					$product_id   = $product->get_id();
					$variation_id = 0;
				}
			}

			$wlp      = new TInvWL_Product();
			$products = $wlp->get( array(
				'external' => false,
				'sql'      => "SELECT * FROM `{table}` WHERE ( ( `product_id` = $product_id  AND `variation_id` = 0 ) OR `variation_id` = $variation_id )  AND `author` > 0",
			) );
			if ( empty( $products ) ) {
				return false;
			}

			$wishlist_ids = array();
			foreach ( $products as $_product ) {
				$wishlist_ids[] = $_product['wishlist_id'];
			}
			$wishlist_ids = array_unique( $wishlist_ids, SORT_NUMERIC );

			if ( $wishlist_ids ) {
				$wl        = new TInvWL_Wishlist();
				$wishlists = $wl->get( array(
					'ID' => $wishlist_ids,
				) );

				foreach ( $products as $_product ) {

					$users = array();
					foreach ( $wishlists as $wishlist ) {
						if ( $wishlist['ID'] != $_product['wishlist_id'] ) {
							continue;
						}

						if ( get_transient( 'tinvwl_notification_low_stock_' . $wishlist['author'] . '_' . $_product['product_id'] . '_' . $_product['variation_id'] ) ) {
							continue;
						}

						if ( $variable && $_product['variation_id'] === '0' ) {

							if ( get_transient( 'tinvwl_notification_low_stock_' . $wishlist['author'] . '_' . $_product['product_id'] ) ) {
								continue;
							}
							set_transient( 'tinvwl_notification_low_stock_' . $wishlist['author'] . '_' . $_product['product_id'], '1', 30 );
						}
						do_action( 'tinvwl_changed_wishlist', 64, $wishlist, $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id(), $product->is_type( 'variation' ) ? $product->get_id() : 0 );
						set_transient( 'tinvwl_notification_low_stock_' . $wishlist['author'] . '_' . $_product['product_id'] . '_' . $_product['variation_id'], '1' );
						$users[ $wishlist['author'] ][] = $wishlist['ID'];
					}

					if ( function_exists( 'WC' ) ) {
						WC()->mailer();
					}
					foreach ( $users as $user_id => $author_wishlists ) {
						do_action( 'tinvwl_send_notification_low_stock', $product, $user_id, $author_wishlists );
					}
				}
			}
		}
	}


	/**
	 * Update stock status in wishlist table
	 *
	 * @param int $object_id product id.
	 * @param string $status Stock Status.
	 *
	 * @return boolean
	 */
	function set_stock_status( $object_id, $status = 'instock' ) {
		if ( empty( $object_id ) ) {
			return false;
		}

		$status = in_array( $status, array( 'onbackorder', 'instock' ) );

		$product = wc_get_product( $object_id );

		$variable = false;

		if ( $product ) {
			if ( in_array( $product->get_type(), array(
				'variable',
				'variation',
				'variable-subscription'
			) ) ) {
				$product_id = $product->get_parent_id();
				$variable   = true;
			} else {
				$product_id = $product->get_id();
			}
		}

		$wlp      = new TInvWL_Product( array(), $this->_name );
		$products = $wlp->get( array(
			'external' => false,
			'sql'      => "SELECT * FROM `{table}` WHERE ( ( `product_id` = $product_id  AND `variation_id` = 0 ) OR `variation_id` = $object_id ) AND `author` > 0",
		) );
		if ( empty( $products ) ) {
			return false;
		}
		$wishlist_ids = array();

		foreach ( $products as $_product ) {
			if ( $status !== $_product['in_stock'] ) {
				$wlp->update( $_product );
				$wishlist_ids[] = $_product['wishlist_id'];
			}
		}
		$wishlist_ids = array_unique( $wishlist_ids, SORT_NUMERIC );
		$wl           = new TInvWL_Wishlist( $this->_name );
		$wishlists    = $wl->get( array(
			'ID' => $wishlist_ids,
		) );

		if ( ! empty( $wishlist_ids ) && $status ) {
			foreach ( $products as $_product ) {
				$users = array();
				foreach ( $wishlists as $wishlist ) {
					if ( $wishlist['ID'] != $_product['wishlist_id'] ) {
						continue;
					}
					if ( $variable && $_product['variation_id'] === '0' ) {

						if ( get_transient( 'tinvwl_notification_in_stock_' . $wishlist['author'] . '_' . $_product['product_id'] ) ) {
							continue;
						}
						set_transient( 'tinvwl_notification_in_stock_' . $wishlist['author'] . '_' . $_product['product_id'], '1', 30 );
					}
					do_action( 'tinvwl_changed_wishlist', ( $status ? 32 : 16 ), $wishlist, $_product['product_id'], $_product['variation_id'] );
					delete_transient( 'tinvwl_notification_low_stock_' . $wishlist['author'] . '_' . $_product['product_id'] . '_' . $_product['variation_id'] );


					$users[ $wishlist['author'] ][] = $wishlist['ID'];

					if ( $status ) {
						if ( function_exists( 'WC' ) ) {
							WC()->mailer();
						}
						foreach ( $users as $user_id => $author_wishlists ) {
							do_action( 'tinvwl_send_notification_stock', $product, $user_id, $author_wishlists );
						}
					}
				}
			}
		}
	}

	/**
	 * Creation menu and sub-menu
	 */
	function action_menu() {
		global $wp_roles;
		$page = add_menu_page( __( 'TI Wishlist', 'ti-woocommerce-wishlist-premium' ), __( 'TI Wishlist', 'ti-woocommerce-wishlist-premium' ), 'tinvwl_wishlists', $this->_name, array(
			$this->wishlist,
			'_print_',
		), TINVWL_URL . 'assets/img/icon_menu.png', '56.888' );
		add_action( "load-$page", array( $this, 'onload' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_inline_scripts' ) );
		$menu = apply_filters( 'tinvwl_admin_menu', array() );

		foreach ( $menu as $item ) {
			if ( ! array_key_exists( 'page_title', $item ) ) {
				$item['page_title'] = $item['title'];
			}
			if ( ! array_key_exists( 'parent', $item ) ) {
				$item['parent'] = $this->_name;
			}
			if ( ! array_key_exists( 'capability', $item ) ) {
				$item['capability'] = 'manage_woocommerce';
			}

			if ( ! array_key_exists( 'roles', $item ) ) {
				$item['roles'] = array( 'administrator' );
			}

			foreach ( $item['roles'] as $role ) {
				$wp_roles->add_cap( $role, $item['capability'] );
			}

			$item['slug'] = implode( '-', array_filter( array( $this->_name, $item['slug'] ) ) );

			$page = add_submenu_page( $item['parent'], $item['page_title'], $item['title'], $item['capability'], $item['slug'], $item['method'] );
			add_action( "load-$page", array( $this, 'onload' ) );
		}
	}

	/**
	 * Load style and javascript
	 */
	function onload() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'admin_footer_text', array( $this, 'footer_admin' ) );
		add_filter( 'screen_options_show_screen', array( $this, 'screen_options_hide_screen' ), 10, 2 );
	}

	/**
	 * Load style
	 */
	function enqueue_styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'tinvwl_load_webfont_admin', true ) ) {
			wp_enqueue_style( $this->_name . '-gfonts', ( is_ssl() ? 'https' : 'http' ) . '://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800', '', null, 'all' );
			wp_enqueue_style( $this->_name . '-webfont', TINVWL_URL . 'assets/css/webfont' . $suffix . '.css', array(), $this->_version, 'all' );
			wp_style_add_data( $this->_name . '-webfont', 'rtl', 'replace' );
			wp_style_add_data( $this->_name . '-webfont', 'suffix', $suffix );
		}

		wp_enqueue_style( $this->_name, TINVWL_URL . 'assets/css/admin' . $suffix . '.css', array(), $this->_version, 'all' );
		wp_style_add_data( $this->_name, 'rtl', 'replace' );
		wp_style_add_data( $this->_name, 'suffix', $suffix );
		wp_enqueue_style( $this->_name . '-form', TINVWL_URL . 'assets/css/admin-form' . $suffix . '.css', array( 'wp-color-picker' ), $this->_version, 'all' );
		wp_style_add_data( $this->_name . '-form', 'rtl', 'replace' );
		wp_style_add_data( $this->_name . '-form', 'suffix', $suffix );
	}

	/**
	 * Load javascript
	 */
	function add_inline_scripts() {
		wp_add_inline_script( 'jquery', '"use strict";function _typeof(e){return(_typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}jQuery(function(l){l(document).ready(function(){var e="?aff=3955",t="https://r.freemius.com/3767/6941420/",r="https://be.elementor.com/visit/?bta=211953&nci=",i={woo:{urls:[{url:"//woocommerce.com",pattern:"{raw_url}"+e},{url:"//crowdsignal.com",pattern:"{raw_url}"+e},{url:"//jetpack.com",pattern:"{raw_url}"+e},{url:"//wpjobmanager.com",pattern:"{raw_url}"+e}]},woostify:{urls:[{url:"//woostify.com",pattern:"{raw_url}"+"/pros/335/"}]},astra:{urls:[{url:"//wpastra.com",pattern:"{raw_url}"+"?bsf=11452"}]},wpocean:{urls:[{url:"//oceanwp.org",pattern:t},{url:"//oceanwp.org/extension",pattern:t+"https://oceanwp.org/extensions/"},{url:"//oceanwp.org/demo",pattern:t+"https://oceanwp.org/demos/"},{url:"//oceanwp.org/extension/category/premium/",pattern:t+"https://oceanwp.org/extension/category/premium/"},{url:"//oceanwp.org/extension/category/free/",pattern:t+"https://oceanwp.org/extension/category/free/"},{url:"//oceanwp.org/core-extensions-bundle/",pattern:t+"https://oceanwp.org/core-extensions-bundle"}]},elem:{urls:[{url:"/elementor.com/?",pattern:r+"5349"},{url:"/elementor.com/blog",pattern:r+"5363"},{url:"/go.elementor.com/overview-widget-blog",pattern:r+"5363"},{url:"/go.elementor.com/overview-widget-docs",pattern:r+"5517"},{url:"/go.elementor.com/docs-admin-plugins",pattern:r+"5517"},{url:"/go.elementor.com/yt-admin-plugins",pattern:r+"5359"},{url:"//go.elementor.com/go-pro",pattern:r+"5352"},{url:"//elementor.com/pro",pattern:r+"5352"}]},yith:{urls:[{url:"//yithemes.com",pattern:"{raw_url}"+"?refer_id=1161007"}]},barn2:{urls:[{url:"//barn2.com",pattern:"{raw_url}"+"/ref/1007/"}]}},o=[],n=[];function a(e){for(var t in e){var r,o;Object.prototype.hasOwnProperty.call(e,t)&&("string"==typeof(r=e[t])?"string"==typeof(o=p(r))&&""!==o&&(e[t]=o):"object"===_typeof(r)&&a(r))}}function p(e){if(e&&"string"==typeof e)for(var t in i)for(var r=i[t].urls,o=0;o<r.length;o++){var n=r[o].url,a=r[o].pattern;if(e.includes(n))return a.replace("{raw_url}",e.split("?")[0].replace(/\/$/,""))}return""}"undefined"!=typeof astraSitesVars&&astraSitesVars&&"object"===("undefined"==typeof astraSitesVars?"undefined":_typeof(astraSitesVars))&&a(astraSitesVars),"undefined"!=typeof ElementorConfig&&ElementorConfig&&"object"===("undefined"==typeof ElementorConfig?"undefined":_typeof(ElementorConfig))&&a(ElementorConfig),"undefined"!=typeof elementorAppConfig&&elementorAppConfig&&"object"===("undefined"==typeof elementorAppConfig?"undefined":_typeof(elementorAppConfig))&&a(elementorAppConfig),l(document).on("mouseover","a",function(){var r,e=l("a").index(this);o[e]?l(this).attr("href",n[e]):(r=p(l(this).attr("href")))&&l(this).on("click.tiafl",function(){var e=l(this).attr("href"),t=(l(this).attr("href",r),setTimeout(function(){l(this).attr("href",e)}.bind(this),1),l("a").index(this));o[t]||(o[t]=!0,n[t]=e),l(this).off("click.tiafl")})})})});' );
	}

	/**
	 * Load javascript
	 */
	function enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( $this->_name . '-bootstrap', TINVWL_URL . 'assets/js/bootstrap' . $suffix . '.js', array( 'jquery' ), $this->_version, 'all' );
		wp_enqueue_script( 'wp-color-picker-alpha', TINVWL_URL . 'assets/js/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), $this->_version, 'all' );
		wp_register_script( $this->_name, TINVWL_URL . 'assets/js/admin' . $suffix . '.js', array(
			'jquery',
			'wp-color-picker-alpha',
		), $this->_version, 'all' );
		wp_localize_script( $this->_name, 'tinvwl_comfirm', array(
			'text_comfirm_reset' => __( 'Are you sure you want to reset the settings?', 'ti-woocommerce-wishlist-premium' ),
		) );
		wp_enqueue_script( $this->_name );

		$geo              = new WC_Geolocation(); // Get WC_Geolocation instance object
		$user_ip          = $geo->get_ip_address(); // Get user IP
		$user_geo         = $geo->geolocate_ip( $user_ip ); // Get geolocated user data.
		$country_code     = $user_geo['country']; // Get the country code
		$restricted_codes = array( 'BD', 'PK', 'IN', 'NG', 'KE' );

		$chat_option = ( isset( $_POST['chat_nonce'] ) ) ? ( isset( $_POST['chat-enabled'] ) ? true : false ) : tinv_get_option( 'chat', 'enabled' );

		$disable_chat = ! $chat_option || in_array( $country_code, $restricted_codes );

		$user_id       = get_current_user_id();
		$user_info     = get_userdata( $user_id );
		$current_theme = wp_get_theme();

		$parent_theme = $current_theme->parent();

		$license_email = $this->get_option( 'license', 'email' );
		$email_setings = ( $license_email ) ? 'email: "' . $license_email . '",' : '';
		wp_add_inline_script( $this->_name, 'window.intercomSettings = {
				app_id: "zyh6v0pc",
				' . $email_setings . '
				hide_default_launcher: ' . ( ( $disable_chat ) ? 'true' : 'false' ) . ',
				"Website": "' . get_site_url() . '",
			"Plugin name": "WooCommerce Wishlist Premium Plugin",
			"Plugin version":"' . TINVWL_VERSION . '",
			"Theme name":"' . $current_theme->get( 'Name' ) . '",
			"Theme version":"' . $current_theme->get( 'Version' ) . '",
			"Theme URI":"' . $current_theme->get( 'ThemeURI' ) . '",
			"Theme author":"' . $current_theme->get( 'Author' ) . '",
			"Theme author URI":"' . $current_theme->get( 'AuthorURI' ) . '",
			"Parent theme name":"' . ( ( $parent_theme ) ? $parent_theme->get( 'Name' ) : '' ) . '",
			"Parent theme version":"' . ( ( $parent_theme ) ? $parent_theme->get( 'Version' ) : '' ) . '",
			"Parent theme URI":"' . ( ( $parent_theme ) ? $parent_theme->get( 'ThemeURI' ) : '' ) . '",
			"Parent theme author":"' . ( ( $parent_theme ) ? $parent_theme->get( 'Author' ) : '' ) . '",
			"Parent theme author URI":"' . ( ( $parent_theme ) ? $parent_theme->get( 'AuthorURI' ) : '' ) . '",
			};
			(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic("reattach_activator");ic("update",intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement("script");s.type="text/javascript";s.async=true;s.src="https://widget.intercom.io/widget/zyh6v0pc";var x=d.getElementsByTagName("script")[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent("onload",l);}else{w.addEventListener("load",l,false);}}})();
			Intercom("trackEvent", "wishlist-free-install", {
				theme_name:"' . ( ( $parent_theme ) ? $parent_theme->get( 'Name' ) : $current_theme->get( 'Name' ) ) . '",
				theme_uri:"' . ( ( $parent_theme ) ? $parent_theme->get( 'ThemeURI' ) : $current_theme->get( 'ThemeURI' ) ) . '",
				theme_author:"' . ( ( $parent_theme ) ? $parent_theme->get( 'Author' ) : $current_theme->get( 'Author' ) ) . '",
				theme_author_uri:"' . ( ( $parent_theme ) ? $parent_theme->get( 'AuthorURI' ) : $current_theme->get( 'AuthorURI' ) ) . '",
				theme_version:"' . ( ( $parent_theme ) ? $parent_theme->get( 'Version' ) : $current_theme->get( 'Version' ) ) . '",
				website:"' . get_site_url() . '",
				user:"' . $user_info->user_email . '",
				user_name:"' . $user_info->user_nicename . '",
				plugin_name:"WooCommerce Wishlist Premium Plugin",
				plugin_version:"' . TINVWL_VERSION . '",
				source:"' . TINVWL_SOURCE . '",
			});
			' );
	}

	/**
	 * Get options
	 *
	 * @param string $name Name setting.
	 * @param string $key Key setting.
	 * @param mixed $default Default value.
	 *
	 * @return mixed
	 */
	protected function get_option( $name, $key = '', $default = false ) {
		$value = get_option( sprintf( '%s-%s', $this->_name, $name ), null );
		if ( empty( $key ) ) {
			return is_null( $value ) ? $default : $value;
		} else {
			if ( is_null( $value ) ) {
				return $default;
			} else {
				return array_key_exists( $key, $value ) ? $value[ $key ] : $default;
			}
		}

		return false;
	}

	/**
	 * Add plugin footer copywriting
	 */
	function footer_admin() {
		do_action( 'tinvwl_admin_promo_footer' );
	}

	/**
	 * Promo in footer for wishlist
	 */
	function promo_footer() {
		echo 'Made with <i class="ftinvwl ftinvwl-heart2"></i> by <a href="https://templateinvaders.com/?utm_source=wishlist_plugin_premium&utm_campaign=made_by&utm_medium=footer">TemplateInvaders</a>';
	}

	/**
	 * Show preview email
	 */
	function preview_email() {
		$data = filter_input_array( INPUT_GET, array(
			'page'          => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'class'         => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'email'         => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'default'       => FILTER_VALIDATE_BOOLEAN,
			'_tinvwl_nonce' => FILTER_DEFAULT,
		) );
		if ( ( isset( $data['page'] ) && $this->_name . '-previewemail' !== $data['page'] ) || ( isset( $data['_tinvwl_nonce'] ) && ! wp_verify_nonce( $data['_tinvwl_nonce'], sprintf( '%s-%s', $this->_name, 'previewemail' ) ) ) || ! isset( $data['class'] ) ) {
			return false;
		}
		WC_Emails::instance();
		$email_class = 'TInvWL_Public_Email_' . $data['class'];
		if ( class_exists( $email_class ) ) {
			$email = new $email_class( $this->_name, $this->_version );
			$email->set_template( $data['email'] );
			tinv_wishlist_template( str_replace( DIRECTORY_SEPARATOR . 'ti-', DIRECTORY_SEPARATOR . 'ti-preview', $email->template_html ) );
		}
		die();
	}

	/**
	 * Error message for sent promotional email
	 *
	 * @param string $recipient Recipient.
	 * @param object $user User.
	 * @param object $product Product.
	 * @param string $error Error message.
	 */
	function email_promotional_error( $recipient, $user, $product, $error ) {
		TInvWL_View::set_error( sprintf( __( 'Promotional email for Product "%1$s" is not sent! %2$s', 'ti-woocommerce-wishlist-premium' ), esc_html( is_callable( array(
			$this->product,
			'get_name'
		) ) ? $this->product->get_name() : $this->product->get_title() ), $error ), 138 ); // WPCS: xss ok.
	}

	/**
	 * Successfully message for sent promotional email
	 *
	 * @param string $recipient Recipient.
	 * @param object $user User.
	 * @param object $product Product.
	 */
	function email_promotional_successfully( $recipient, $user, $product ) {
		TInvWL_View::set_tips( sprintf( __( 'Promotional email for Product "%s" has been sent successfully!', 'ti-woocommerce-wishlist-premium' ), esc_html( is_callable( array(
			$product,
			'get_name'
		) ) ? $product->get_name() : $product->get_title() ) ) );
	}

	/**
	 * Templates overriding status check.
	 *
	 * @param boolean $outdated Out date status.
	 *
	 * @return string
	 */
	function templates_status_check( $outdated = false ) {

		$found_files = array();

		$scanned_files = WC_Admin_Status::scan_template_files( TINVWL_PATH . '/templates/' );

		foreach ( $scanned_files as $file ) {
			if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/' . $file;
			} elseif ( file_exists( get_stylesheet_directory() . '/woocommerce/' . $file ) ) {
				$theme_file = get_stylesheet_directory() . '/woocommerce/' . $file;
			} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
				$theme_file = get_template_directory() . '/' . $file;
			} elseif ( file_exists( get_template_directory() . '/woocommerce/' . $file ) ) {
				$theme_file = get_template_directory() . '/woocommerce/' . $file;
			} else {
				$theme_file = false;
			}

			if ( ! empty( $theme_file ) ) {
				$core_version  = WC_Admin_Status::get_file_version( TINVWL_PATH . '/templates/' . $file );
				$theme_version = WC_Admin_Status::get_file_version( $theme_file );

				if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
					if ( $outdated ) {
						return 'outdated';
					}
					$found_files[] = sprintf( __( '<code>%1$s</code> version <strong style="color:red">%2$s</strong> is out of date. The core version is <strong style="color:red">%3$s</strong>', 'ti-woocommerce-wishlist-premium' ), str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ), $theme_version ? $theme_version : '-', $core_version );
				} else {
					$found_files[] = str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file );
				}
			}
		}

		return $found_files;
	}

	/**
	 * Templates overriding status for WooCommerce Status report page.
	 */
	function system_report_templates() {

		TInvWL_View::view( 'templates-status', array( 'found_files' => $this->templates_status_check() ) );
	}

	/**
	 * Outdated templates notice.
	 */
	function admin_notice_outdated_templates() {
		if ( 'outdated' === $this->templates_status_check( true ) ) {

			$theme = wp_get_theme();

			$html = sprintf( __( '<strong>Your theme (%1$s) contains outdated copies of some WooCommerce Wishlist Premium template files.</strong><br> These files may need updating to ensure they are compatible with the current version of TI WooCommerce Wishlist.<br> You can see which files are affected from the <a href="%2$s">system status page</a>.<br> If in doubt, check with the author of the theme.', 'ti-woocommerce-wishlist-premium' ), esc_html( $theme['Name'] ), esc_url( admin_url( 'admin.php?page=wc-status' ) ) );

			WC_Admin_Notices::add_custom_notice( 'outdated_templates', $html );
		} else {
			WC_Admin_Notices::remove_notice( 'outdated_templates' );
		}
	}

	/**
	 * Disable screen option on plugin pages
	 *
	 * @param boolean $show_screen Show screen.
	 * @param \WP_Screen $_this Screen option page.
	 *
	 * @return boolean
	 */
	function screen_options_hide_screen( $show_screen, $_this ) {
		if ( $this->_name === $_this->parent_base || $this->_name === $_this->parent_file ) {
			return false;
		}

		return $show_screen;
	}

	/**
	 * Check if there is a hook in the cron
	 */
	function scheduled_remove_wishlist() {
		$timestamp = wp_next_scheduled( 'tinvwl_remove_without_author_wishlist' );
		if ( ! $timestamp ) {
			$time = strtotime( '00:00 today +1 HOURS' );
			wp_schedule_event( $time, 'daily', 'tinvwl_remove_without_author_wishlist' );
		}
	}

	/**
	 * Removing old wishlist without a user older than 34 days
	 */
	public function remove_old_wishlists() {
		global $wpdb;
		$wishlists = $wpdb->get_results( 'SELECT t1.wishlist_id ID FROM ' . $wpdb->prefix . 'tinvwl_items t1 JOIN( SELECT wishlist_id, MAX(DATE) DATE FROM ' . $wpdb->prefix . 'tinvwl_items GROUP BY wishlist_id ) t2 ON t1.wishlist_id = t2.wishlist_id AND t1.date = t2.date WHERE t1.author = 0 AND t1.date < DATE_SUB(CURDATE(), INTERVAL ' . (int) tinv_get_option( 'general', 'guests_timeout' ) . ' DAY)', ARRAY_A );

		if ( $wishlists ) {
			$wl = new TInvWL_Wishlist();
			foreach ( $wishlists as $wishlist ) {
				$wl->remove( $wishlist['ID'] );
			}
		}
	}

	/**
	 * Add a post display state for special WC pages in the page list table.
	 *
	 * @param array $post_states An array of post display states.
	 * @param WP_Post $post The current post object.
	 *
	 * @return array
	 */
	public function add_display_post_states( $post_states, $post ) {
		if ( tinv_get_option( 'page', 'wishlist' ) === $post->ID ) {
			$post_states['tinvwl_page_for_wishlist'] = __( 'Wishlist Page', 'ti-woocommerce-wishlist-premium' );
		}
		if ( tinv_get_option( 'page', 'manage' ) === $post->ID ) {
			$post_states['tinvwl_page_for_manage_wishlists'] = __( 'Manage Wishlists Page', 'ti-woocommerce-wishlist-premium' );
		}
		if ( tinv_get_option( 'page', 'search' ) === $post->ID ) {
			$post_states['tinvwl_page_for_wishlist_search_results'] = __( 'Wishlist Search Results Page', 'ti-woocommerce-wishlist-premium' );
		}
		if ( tinv_get_option( 'page', 'public' ) === $post->ID ) {
			$post_states['tinvwl_page_for_all_public_wishlists'] = __( 'All Public Wishlists Page', 'ti-woocommerce-wishlist-premium' );
		}
		if ( tinv_get_option( 'page', 'searchp' ) === $post->ID ) {
			$post_states['tinvwl_page_for_wishlist_search'] = __( 'Search for Wishlist', 'ti-woocommerce-wishlist-premium' );
		}
		if ( tinv_get_option( 'page', 'create' ) === $post->ID ) {
			$post_states['tinvwl_page_for_create_wishlist'] = __( 'Create Wishlist Page', 'ti-woocommerce-wishlist-premium' );
		}

		return $post_states;
	}

	function woocommerce_blocks_editor() {
		wp_enqueue_script( 'create-block-tinvwl-products-block-editor', TINVWL_URL . 'assets/js/editor.js', array(
			'wc-blocks',
			'wp-i18n',
			'wp-element'
		), '1.0.0', true );

		$args = array(
			'plugin_url' => esc_url_raw( TINVWL_URL ),
		);

		wp_localize_script( 'create-block-tinvwl-products-block-editor', 'tinvwl_add_to_wishlist', $args );
	}
}
