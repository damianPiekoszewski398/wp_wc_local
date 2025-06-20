<?php

if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Rpesp_Wcfm')) {

    class Rpesp_Wcfm extends Rpesp_Main
    {

        public function __construct()
        {
            /* call parent class construct */
            parent::__construct();
            add_action('rpesp_general_tab_additional_fields', array($this, "addGeneralFields"),10,1);
            if ($this->getSetting('allow_wcfm_vendor')) {
                add_action('after_wcfm_products_manage_linked', array($this, "wcfmEditProduct"), 10, 2);
                add_action('after_wcfm_products_manage_meta_save', array($this, "saveProductMeta"), 10, 2);
            }
            

        }

        /**
         *  Function for add setting
         *  @param int $dokansettings
         */
        public function addGeneralFields($settings)
        {
            include  self::$plugin_dir . 'view/admin/global/wcfm-general-fields.php';

        }

        /**
         * Function for add delivery fields in product page of vendor
         * 
         * @param object $product
         * @param int $product_id
         */
        public function wcfmEditProduct($product_id, $product)
        {

            include_once self::$plugin_dir . 'view/admin/product/wcfm/product.php';
        }


        /**
         * Function for save product meta
         * @param int $product_id
         * @param array $data
         */
        public function saveProductMeta($product_id, $data)
        {
            

            $saveMeta = array();
            if (isset($data['esttime'])) {
                $saveMeta['esttime'] = $data['esttime'];
            }
            if (isset($data['esttime_outofstock'])) {
                $saveMeta['esttime_outofstock'] = $data['esttime_outofstock'];
            }
            if (isset($data['esttime_backorder'])) {
                $saveMeta['esttime_backorder'] = $data['esttime_backorder'];
            }
            if (!empty($saveMeta)) {
                update_post_meta($product_id, self::$meta_key, $saveMeta);
            }
        }

    }
    new Rpesp_Wcfm();

}