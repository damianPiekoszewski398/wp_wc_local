<?php

/*
  Plugin Name:WooCommerce Estimated Delivery Or Shipping Date Per Product 
  Plugin URI: http://www.magerips.com
  Description: WooCommerce Estimated Shipping Per Product allow you to display estimated delivery date per product.
  Author: Magerips
  Version: 5.3
  Author URI: http://www.magerips.com
 */


global $rpesp_plugin_url, $rpesp_plugin_dir;

$rpesp_plugin_dir = dirname(__FILE__) . "/";
$rpesp_plugin_url = plugins_url() . "/" . basename($rpesp_plugin_dir) . "/";
include_once $rpesp_plugin_dir . 'lib/Rpesp_Loader.php';

