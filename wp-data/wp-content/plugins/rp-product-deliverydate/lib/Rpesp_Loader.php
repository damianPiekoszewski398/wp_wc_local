<?php

if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('Rpesp_Loader')) {

	class Rpesp_Loader
	{

		public $objAdmin;
		public $objFront;
		protected static $_instance = null;

		public function __construct()
		{
			add_action('plugins_loaded', array($this, 'loadPlugin'), 200);
		}

		/**
		 * Load plugin
		 */
		public function loadPlugin()
		{
			$this->loadPluginFiles();
			$this->loadSetup();
		}

		/**
		 * Main Plugin Instance
		 *
		 * Ensures only one instance of plugin is loaded or can be loaded.
		 */
		public static function instance()
		{

			if (is_null(self::$_instance)) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * include plugin files
		 */
		public function loadPluginFiles()
		{
			global $rpesp_plugin_dir;
			include_once $rpesp_plugin_dir . 'lib/Rpesp_Main.php';
			include_once $rpesp_plugin_dir . 'lib/Rpesp_Admin.php';
			include_once $rpesp_plugin_dir . 'lib/Rpesp_Front.php';
			if($this->dokanActivate()){
				include_once $rpesp_plugin_dir . 'lib/Rpesp_Dokan.php';
			}
			if($this->wcfmActivate()){
				include_once $rpesp_plugin_dir . 'lib/Rpesp_Wcfm.php';
			}
			
		}

		/**
		 * initialize plugin classes
		 */
		public function loadSetup()
		{
			if (is_admin()) {

				$this->objAdmin = new Rpesp_Admin();
			}
			$this->objFront = new Rpesp_Front();
		}

		public function dokanActivate()
		{
			if (is_plugin_active('dokan-lite/dokan.php') || is_plugin_active('dokan-pro/dokan.php')) {
				return true;
			}

			return false;
		}

		public function wcfmActivate()
		{
			if (is_plugin_active('wc-multivendor-marketplace/wc-multivendor-marketplace.php')) {
				return true;
			}

			return false;
		}

	}

}

/**
 * Returns the main instance of WooCommerce PDF Invoices & Packing Slips to prevent the need to use globals.
 */
if (!function_exists('WOO_RPESP')) {

	function WOO_RPESP()
	{
		return Rpesp_Loader::instance();
	}

	WOO_RPESP();
}