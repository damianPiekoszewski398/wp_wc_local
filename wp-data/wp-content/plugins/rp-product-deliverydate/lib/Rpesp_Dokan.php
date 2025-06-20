<?php

if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Rpesp_Dokan')) {

    class Rpesp_Dokan extends Rpesp_Main
    {

        public function __construct()
        {
            /* call parent class construct */
            parent::__construct();
            add_action('rpesp_general_tab_additional_fields', array($this, "addGeneralFields"),10,1);
            if ($this->getSetting('allow_dokan_vendor')) {
                add_action('dokan_product_edit_after_main', array($this, "dokanEditProduct"), 10, 2);
                add_action('dokan_new_product_added', array($this, "saveProductMeta"), 10, 2);
                add_action('dokan_product_updated', array($this, "saveProductMeta"), 10, 2);
            }
            

        }

        /**
         *  Function for add setting
         *  @param int $dokansettings
         */
        public function addGeneralFields($settings)
        {
            include  self::$plugin_dir . 'view/admin/global/dokan-general-fields.php';

        }

        /**
         * Function for add delivery fields in product page of vendor
         * 
         * @param object $product
         * @param int $product_id
         */
        public function dokanEditProduct($product, $product_id)
        {

            include_once self::$plugin_dir . 'view/admin/product/dokan/product.php';
        }


        /**
         * Function for save product meta
         * @param int $product_id
         * @param array $data
         */
        public function saveProductMeta($product_id, $data)
        {

            $saveMeta = array();
            if (isset($_POST['esttime'])) {
                $saveMeta['esttime'] = $_POST['esttime'];
            }
            if (isset($_POST['esttime_outofstock'])) {
                $saveMeta['esttime_outofstock'] = $_POST['esttime_outofstock'];
            }
            if (isset($_POST['esttime_backorder'])) {
                $saveMeta['esttime_backorder'] = $_POST['esttime_backorder'];
            }
            if (!empty($saveMeta)) {
                update_post_meta($product_id, self::$meta_key, $saveMeta);
            }
        }

    }
    new Rpesp_Dokan();

}