<?php
/**
 * Wishlist Plugin for WooCoommerce.
 * Plugin Name:       TI WooCommerce Wishlist Premium
 * Plugin URI:        https://templateinvaders.com/product/ti-woocommerce-wishlist-wordpress-plugin/
 * Description:       More than just a Wishlist, a powerful marketing & analytics tool.
 * Version:           2.5.2
 * Requires at least: 4.7
 * Tested up to: 6.2
 * WC requires at least: 3.0
 * WC tested up to: 7.7
 * Author:            TemplateInvaders
 * Author URI:        https://templateinvaders.com/
 * License:           TemplateInvaders License
 * License URI:       https://templateinvaders.com/license-agreement/
 * Text Domain:       ti-woocommerce-wishlist-premium
 * Domain Path:       /languages
 *
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Define default path.
if ( ! defined( 'TINVWL_URL' ) ) {
	define( 'TINVWL_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'TINVWL_PATH' ) ) {
	define( 'TINVWL_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'TINVWL_PREFIX' ) ) {
	define( 'TINVWL_PREFIX', 'tinvwl' );
}

if ( ! defined( 'TINVWL_DOMAIN' ) ) {
	define( 'TINVWL_DOMAIN', 'ti-woocommerce-wishlist-premium' );
}

if ( ! defined( 'TINVWL_VERSION' ) ) {
	define( 'TINVWL_VERSION', '2.5.2' );
}

if ( ! defined( 'TINVWL_SOURCE' ) ) {
	define( 'TINVWL_SOURCE', '7_9_0_3_5' );
}

if ( ! defined( 'TINVWL_LOAD_PREMIUM' ) ) {
	define( 'TINVWL_LOAD_PREMIUM', plugin_basename( __FILE__ ) );
}


if ( ! function_exists( 'tinv_array_merge' ) ) {

	/**
	 * Function to merge arrays with replacement options
	 *
	 * @param array $array1 Array.
	 * @param array $_ Array.
	 *
	 * @return array
	 */
	function tinv_array_merge( $array1, $_ = null ) {
		if ( ! is_array( $array1 ) ) {
			return $array1;
		}
		$args = func_get_args();
		array_shift( $args );
		foreach ( $args as $array2 ) {
			if ( is_array( $array2 ) ) {
				foreach ( $array2 as $key => $value ) {
					$array1[ $key ] = $value;
				}
			}
		}

		return $array1;
	}
}


if ( ! function_exists( 'tinv_get_option_defaults' ) ) {

	/**
	 * Extract default options from settings class
	 *
	 * @param string $category Name category settings.
	 *
	 * @return array
	 */
	function tinv_get_option_defaults( $category ) {

		$defaults = get_transient( TINVWL_PREFIX . '_default_settings_' . TINVWL_VERSION );

		if ( false === $defaults ) {

			$dir = TINVWL_PATH . 'admin/settings/';
			if ( ! file_exists( $dir ) || ! is_dir( $dir ) ) {
				return array();
			}
			$files = scandir( $dir );
			foreach ( $files as $key => $value ) {
				if ( preg_match( '/\.class\.php$/i', $value ) ) {
					$files[ $key ] = preg_replace( '/\.class\.php$/i', '', $value );
				} else {
					unset( $files[ $key ] );
				}
			}
			$defaults = array();
			foreach ( $files as $file ) {
				$class         = 'TInvWL_Admin_Settings_' . ucfirst( $file );
				$class         = $class::instance( TINVWL_PREFIX, TINVWL_VERSION );
				$class_methods = get_class_methods( $class );
				foreach ( $class_methods as $method ) {
					if ( preg_match( '/_data$/i', $method ) ) {
						$settings = $class->get_defaults( $class->$method() );
						$defaults = tinv_array_merge( $defaults, $settings );
					}
				}
			}

			set_transient( TINVWL_PREFIX . '_default_settings_' . TINVWL_VERSION, $defaults, DAY_IN_SECONDS );
		}
		if ( 'all' === $category ) {
			return $defaults;
		}
		if ( array_key_exists( $category, $defaults ) ) {
			return $defaults[ $category ];
		}

		return array();
	}
} // End if().


if ( ! function_exists( 'tinv_get_option_admin' ) ) {

	/**
	 * Extract options from database or default array settings.
	 *
	 * @param string $category Name category settings.
	 * @param string $option Name paremetr. If is empty string, then function return array category settings.
	 *
	 * @return mixed
	 */
	function tinv_get_option_admin( $category, $option = '' ) {
		$prefix = TINVWL_PREFIX . '-';
		$values = get_option( $prefix . $category, array() );
		if ( empty( $values ) ) {
			$values = array();
		}
		if ( empty( $option ) ) {
			return $values;
		} elseif ( array_key_exists( $option, $values ) ) {
			return $values[ $option ];
		}

		return null;
	}
}


if ( ! function_exists( 'tinv_template' ) ) {

	/**
	 * Get template from database
	 *
	 * @return string
	 */
	function tinv_template() {
		$values = get_option( TINVWL_PREFIX . '-style', array() );
		if ( empty( $values ) ) {
			return '';
		}
		if ( array_key_exists( 'template', $values ) ) {
			return $values['template'];
		}

		return '';
	}
}

if ( ! function_exists( 'activation_tinv_wishlist' ) ) {

	/**
	 * Activation plugin
	 */
	function activation_tinv_wishlist() {
		if ( dependency_tinv_wishlist( false ) ) {
			TInvWL_Activator::activate();
			flush_rewrite_rules();
		}
	}
}

if ( ! function_exists( 'deactivation_tinv_wishlist' ) ) {
	/**
	 * Deactivation plugin
	 */
	function deactivation_tinv_wishlist() {
		flush_rewrite_rules();
	}
}

if ( ! function_exists( 'uninstall_tinv_wishlist' ) ) {
	/**
	 * Uninstall plugin
	 */
	function uninstall_tinv_wishlist() {
		if ( ! defined( 'TINVWL_LOAD_FREE' ) ) {
			TInvWL_Activator::uninstall();
			flush_rewrite_rules();
			wp_clear_scheduled_hook( 'tinvwl_remove_without_author_wishlist' );
		}
		wp_clear_scheduled_hook( 'tinvwl_subscribers_notification' );
	}
}


if ( function_exists( 'spl_autoload_register' ) && ! function_exists( 'autoload_tinv_wishlist' ) ) {

	/**
	 * Autoloader class. If no function spl_autoload_register, then all the files will be required
	 *
	 * @param string $_class Required class name.
	 *
	 * @return boolean
	 */
	function autoload_tinv_wishlist( $_class ) {
		$preffix = 'TInvWL';
		$ext     = '.php';
		$class   = explode( '_', $_class );
		$object  = array_shift( $class );
		if ( $preffix !== $object ) {
			return false;
		}
		if ( empty( $class ) ) {
			$class = array( $preffix );
		}
		$basicclass = $class;
		array_unshift( $class, 'includes' );
		$classes = array(
			TINVWL_PATH . strtolower( implode( DIRECTORY_SEPARATOR, $basicclass ) ),
			TINVWL_PATH . strtolower( implode( DIRECTORY_SEPARATOR, $class ) ),
		);

		foreach ( $classes as $class ) {
			foreach ( array( '.class', '.helper' ) as $suffix ) {
				$filename = $class . $suffix . $ext;
				if ( file_exists( $filename ) ) {
					require_once $filename;
				}
			}
		}

		require_once TINVWL_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

		return false;
	}

	spl_autoload_register( 'autoload_tinv_wishlist' );
} // End if().

if ( ! function_exists( 'dependency_tinv_wishlist' ) ) {
	/**
	 * Dependency plugin
	 *
	 * @param boolean $run For run hooks dependency or return error message.
	 *
	 * @return boolean
	 */
	function dependency_tinv_wishlist( $run = true ) {
		$ext = new TInvWL_PluginExtend( null, __FILE__, TINVWL_PREFIX );
		$ext->set_dependency( 'woocommerce/woocommerce.php', 'WooCommerce' )->need();
		if ( $run ) {
			$ext->run();
		}

		return $ext->status_dependency();
	}
}

if ( ! function_exists( 'run_tinv_wishlist' ) ) {

	/**
	 * Run plugin
	 */
	function run_tinv_wishlist() {
		require_once TINVWL_PATH . 'tinv-wishlists-function.php';

		foreach ( glob( TINVWL_PATH . 'integrations' . DIRECTORY_SEPARATOR . '*.php' ) as $file ) {
			require_once $file;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		if ( defined( 'TINVWL_LOAD_PREMIUM' ) && defined( 'TINVWL_LOAD_FREE' ) || defined( 'TINVWL_LOAD_PREMIUM' ) && is_plugin_active_for_network( TINVWL_LOAD_PREMIUM ) || defined( 'TINVWL_LOAD_FREE' ) && is_plugin_active_for_network( TINVWL_LOAD_FREE ) ) {
			$redirect = tinv_wishlist_status( plugin_basename( __FILE__ ) );
			if ( $redirect ) {
				header( 'Location: ' . $redirect );
				exit;
			}
		} elseif ( dependency_tinv_wishlist() ) {
			$plugin = new TInvWL();
			$plugin->run();
		}
	}
}

add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

register_activation_hook( __FILE__, 'activation_tinv_wishlist' );
register_deactivation_hook( __FILE__, 'deactivation_tinv_wishlist' );
register_uninstall_hook( __FILE__, 'uninstall_tinv_wishlist' );
add_action( 'plugins_loaded', 'run_tinv_wishlist', 20 );
