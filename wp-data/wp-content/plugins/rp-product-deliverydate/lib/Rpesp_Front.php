<?php

if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Rpesp_Front')) {

    class Rpesp_Front extends Rpesp_Main
    {

        public function __construct()
        {
            /* call parent class construct */
            parent::__construct();

            /* init hook */
            add_action("init", array($this, "initAction"));

            if ($this->isEnable() === true) {
                $this->initFrontEnd();
            }

        }

        /**
         * Function for init hook
         */
        public function initAction()
        {

            load_plugin_textdomain("rp-product-deliverydate", false, dirname(plugin_basename(__FILE__)) . '/../lang/');
        }

        /**
         *  Function for register plugin frontend hooks
         */
        public function initFrontEnd()
        {

            /* hook for register css and js */
            add_action("wp_enqueue_scripts", array($this, "enqueueScripts"), 100);

            /* wp_footer hook */
            add_action('wp_footer', array($this, 'wpFooter'), 10);

            $displayOnProduct = $this->getSetting("display_on_product");
            if ($displayOnProduct == 1) {
                $productPagePosition = $this->getProductPagePosition();

                /* hook for display text on product page */
                add_action($productPagePosition['hook'], array($this, 'dispalyDateOnProductPage'), $productPagePosition['position']);
            }

            $displayOnProductArchive = $this->getSetting("display_on_product_archive");

            if ($displayOnProductArchive == 1) {
                /* hook for display text on product archive/shop page */
                add_action($this->getSetting('text_pos_archive'), array($this, 'dispalyDateOnProductShopPages'), 0);
            }


            if ($this->getSetting('combine_date') == 1) {
                /* call hook for display combine date on cart page */
                $cartPosition = $this->getSetting('cart_position');
                add_filter($cartPosition, array($this, 'displayCombineDateForCart'), 10);

                /* call hook for display combine date on checkout page */
                $checkoutPosition = $this->getSetting('checkout_position');
                add_filter($checkoutPosition, array($this, 'displayCombineDateForCart'), 10);

                /* show delivery date on customer order detail page */
                $orderPosition = $this->getSetting('orderpage_position');
                add_action($orderPosition, array($this, "showCombineDateOnOrderDetail"), 10, 1);

                /* show delivery date on customer order detail page */
                $this->displayCombineDateForEmail();

                /* Show time delivery on order detail page in admin area */
                add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'displayDateInAdminOrderDetail'));
            } else {
                /* hook for display text on cart and checkout */
                add_filter('woocommerce_after_cart_item_name', array($this, 'displayOnCart'), 10, 1);

                add_filter('woocommerce_cart_item_name', array($this, 'displayOnCheckout'), 10, 2);

                /* hook for display text on order page */
                add_filter('woocommerce_before_order_itemmeta', array($this, 'displayOrderItemMeta'), 10, 2);

                /* hook for display text in order email */
                add_action('woocommerce_display_item_meta', array($this, 'displayInOrderEmail'), 10, 3);
            }


            /* hook for save delivery date for order item */
            add_action('woocommerce_checkout_create_order_line_item', array($this, 'saveItemMeta'), 10, 4);

            /* hook for hide date meta key for order */
            add_filter('woocommerce_hidden_order_itemmeta', array($this, 'hideDateMeta'), 10, 1);

            /* Shortcode for delivery date display on product page */
            add_shortcode('rp_delivery_date', array($this, "srtDeliveryDate"));
            /* Shortcode for display combine delivery date  on cart and checkout page */
            add_shortcode('rp_single_cart_delivery_date', array($this, "srtCombineDeliveryDateCartAndCheckout"));
            /* Shortcode for display combine delivery date  on cart and checkout page */
            add_shortcode('rp_order_delivery_date', array($this, "srtCombineDeliveryDateForOrder"));

            if ($this->getSetting("load_using_ajax")) {
                if ($this->getSetting("enable_carrier")) {
                    /* hooks for load delivery date via ajax */
                    add_action('wp_ajax_nopriv_load_carrier_date', array($this, 'ajaxGetCarrierDate'));
                    add_action('wp_ajax_load_carrier_date', array($this, 'ajaxGetCarrierDate'));
                }
                if ($displayOnProductArchive == 1) {
                    /* hooks for load delivery date via ajax for shop pages */
                    add_action('woocommerce_after_shop_loop', array($this, 'afterShopLoop'));
                }
                add_action('wp_ajax_nopriv_load_product_date_shop', array($this, 'ajaxGetDeliveryDateShop'));
                add_action('wp_ajax_load_product_date_shop', array($this, 'ajaxGetDeliveryDateShop'));
            }

            /* calculate and save delivery time for manual order */
            add_action('woocommerce_ajax_order_item', array($this, "saveOrderItemMetaForManuallOrderItem"), 10, 4);

            if ($this->getSetting("restapi") == 1) {
                /* hook for enable rest api support */
                add_action('rest_api_init', array($this, "addRestApiFields"), 0);
            }

        }


        /**
         *  Callback function for rest api
         */
        public function addRestApiFields()
        {
            /* add delivery time field to product rest api json */
            register_rest_field(
                'product',
                'delivery_text_html',
                array(
                    'get_callback' => function ($object, $field_name, $request) {
                        return $this->callbackDeliveryHtml($object, $field_name, $request);
                    },
                    'update_callback' => null,
                    'schema' => array(
                        'description' => __('Delivery text html.', 'woocommerce'),
                        'type' => 'string',
                        'context' => array('view')
                    ),
                )
            );

            register_rest_field(
                'product',
                'delivery_time',
                array(
                    'get_callback' => function ($object, $field_name, $request) {
                        return $this->callbackDeliveryTime($object, $field_name, $request);
                    },
                    'update_callback' => null,
                    'schema' => array(
                        'description' => __('Delivery time.', 'woocommerce'),
                        'type' => 'string',
                        'context' => array('view')
                    ),
                )
            );

            /* add delivery time field to order rest api json */
            register_rest_field(
                'shop_order',
                'delivery_time',
                array(
                    'get_callback' => function ($object, $field_name, $request) {
                        return $this->callbackOrderDeliveryTime($object, $field_name, $request);
                    },
                    'update_callback' => null,
                    'schema' => array(
                        'description' => __('Delivery time.', 'woocommerce'),
                        'type' => 'array',
                        'context' => array('view')
                    ),
                )
            );
            register_rest_field(
                'shop_order',
                'delivery_text_html',
                array(
                    'get_callback' => function ($object, $field_name, $request) {
                        return $this->callbackOrderDeliveryHtml($object, $field_name, $request);
                    },
                    'update_callback' => null,
                    'schema' => array(
                        'description' => __('Delivery time.', 'woocommerce'),
                        'type' => 'string',
                        'context' => array('view')
                    ),
                )
            );

        }

        /**
         * Proruct rest api callback function for delivery text field
         * 
         * @param object $object 
         * @param string $field_name 
         * @param object $request
         * @return string
         */

        public function callbackDeliveryHtml($object, $field_name, $request)
        {
            $product = wc_get_product($object['id']);
            return $this->getTextForProductPage($product);
        }

        /**
         * Proruct rest api callback function for delivery time field
         * 
         * @param object $object 
         * @param string $field_name 
         * @param object $request
         * @return string
         */

        public function callbackDeliveryTime($object, $field_name, $request)
        {
            $product = wc_get_product($object['id']);
            return $this->getEstimatedDateForProduct($product);
        }

        /**
         * Order rest api callback function for delivery time field
         * 
         * @param object $object 
         * @param string $field_name 
         * @param object $request
         * @return array
         */
        public function callbackOrderDeliveryTime($object, $field_name, $request)
        {
            $order = wc_get_order($object['id']);
            return $this->getCombineDateForOrder($order);
        }

        /**
         * Order rest api callback function for delivery time field
         * 
         * @param object $object 
         * @param string $field_name 
         * @param object $request
         * @return string
         */
        public function callbackOrderDeliveryHtml($object, $field_name, $request)
        {
            $order = wc_get_order($object['id']);
            $combineDate = $this->getCombineDateForOrder($order);
            if ($combineDate !== false) {
                return $this->getCombineDateTextForOrder($combineDate);
            }
            return false;
        }

        /**
         *  Callback function for after shop loop hook
         */
        public function afterShopLoop()
        {
            echo "<script>rpLoadDeliveryDate()</script>";
        }


        /**
         *  Function for save order item meta for manual create order in admin
         * 
         * @param object $item 
         * @param int $item_id 
         * @param object $order 
         * @param object $product
         * @return object
         */
        public function saveOrderItemMetaForManuallOrderItem($item, $item_id, $order, $product)
        {

            if (empty($product)) {
                return $item;
            }

            $estDay = $this->getEstimatedDateForProduct($product);

            if ($estDay) {
                wc_update_order_item_meta($item_id, self::$order_item_meta_key, $estDay);
            }
            return $item;
        }

        /**
         *  Function for display delivery date on admin order detail page
         * 
         * @param object $order 
         */
        public function displayDateInAdminOrderDetail($order)
        {
            $combineDate = $this->getCombineDateForOrder($order);
            if ($combineDate !== false) {
                echo $this->getCombineDateTextForOrder($combineDate);
            }
        }

        /**
         *  Function for call hook for display delivery date on order email as per setting
         */
        public function displayCombineDateForEmail()
        {
            if ($this->getSetting('enable_on_orderemail') != 1) {
                return;
            }
            if ($this->getSetting('email_position') == 0) {
                $hook = "woocommerce_email_order_details";
                $position = "10";
            } else if ($this->getSetting('email_position') == 1) {
                $hook = "woocommerce_email_order_details";
                $position = "100";
            } else if ($this->getSetting('email_position') == 2) {
                $hook = "woocommerce_email_order_meta";
                $position = "10";
            } else if ($this->getSetting('email_position') == 3) {
                $hook = "woocommerce_email_customer_details";
                $position = "10";
            } else if ($this->getSetting('email_position') == 4) {
                $hook = "woocommerce_email_customer_details";
                $position = "100";
            }
            add_action($hook, array($this, "showCombineDateOnOrderEmail"), $position, 1);
        }

        /**
         * Function for display delivery date on customer order email
         * 
         * @param object $order
         */
        public function showCombineDateOnOrderEmail($order)
        {

            $combineDate = $this->getCombineDateForOrder($order);
            if ($combineDate !== false) {
                echo $this->getCombineDateTextForOrder($combineDate);
            }
        }

        /**
         * Shortcode callback function for display delivery date for order
         * 
         * @param array $attr
         */
        public function srtCombineDeliveryDateForOrder($attr)
        {
            if (!function_exists('WC') || !isset(WC()->customer) || !isset(WC()->countries)) {
                return;
            }
            if (empty($attr) || !isset($attr['order_id'])) {
                return;
            }
            $order = wc_get_order($attr['order_id']);
            $combineDate = $this->getCombineDateForOrder($order);
            if ($combineDate !== false) {
                return $this->getCombineDateTextForOrder($combineDate);
            }
            return;
        }

        /**
         * Function for display delivery date on customer order detail page
         * 
         * @param object $order
         */
        public function showCombineDateOnOrderDetail($order)
        {
            if ($this->getSetting('enable_on_orderpage') != 1) {
                return;
            }
            $combineDate = $this->getCombineDateForOrder($order);
            if ($combineDate !== false) {
                echo $this->getCombineDateTextForOrder($combineDate);
            }
        }

        /**
         * Function for get combine date for order
         * 
         * @param object $order
         */
        public function getCombineDateForOrder($order)
        {
            $dateForEachItem = array();

            foreach ($order->get_items() as $item_id => $item) {
                $itemDeliveryDate = $item->get_meta(self::$order_item_meta_key, true);
                if (!empty($itemDeliveryDate)) {
                    $dateForEachItem[] = $itemDeliveryDate;
                }
            }
            $finalDate = $this->getFinalCombineDate($dateForEachItem);
            return $finalDate;
        }

        /**
         * Function for display combine delivery date for order on cart page
         */
        public function srtCombineDeliveryDateCartAndCheckout()
        {
            if (!function_exists('WC') || !isset(WC()->customer) || !isset(WC()->countries)) {
                return;
            }
            $combineDate = $this->getCombineDate();

            if ($combineDate !== false) {
                return $this->getCombineDateTextForCartAndCheckout($combineDate);
            }
            return '';
        }

        /**
         * Function for display combine delivery date for order on cart page
         */
        public function displayCombineDateForCart()
        {
            if ($this->getSetting('enable_on_cart') != 1) {
                return;
            }
            $combineDate = $this->getCombineDate();

            if ($combineDate !== false) {
                echo $this->getCombineDateTextForCartAndCheckout($combineDate);
            }
        }

        /**
         * Function for get combine date text
         * 
         * @param array $date
         * @return string
         */
        public function getCombineDateTextForOrder($date)
        {
            $settingText = $this->getSetting('text_order_combine_date');
            $settingText = __(stripslashes($settingText));
            $response = str_replace(
                array(
                    '{product_with_min_date}',
                    '{product_with_max_date}'
                ),
                array(
                    $this->getFormatedDate($date['min']),
                    $this->getFormatedDate($date['max'])
                ),
                $settingText
            );
            $response = $this->pregReplaceMinDate($date['min'], $response);
            $response = $this->pregReplaceMaxDate($date['max'], $response);
            return '<div class="rp_combine_estimated_date">' . $response . '</div>';
        }

        /**
         * Function for get combine date text
         * 
         * @param array $date
         * @return string
         */
        public function getCombineDateTextForCartAndCheckout($date)
        {
            $settingText = $this->getSetting('text_cart_checkout_combine_date');
            $settingText = __(stripslashes($settingText));
            $minNumberOfDay = $this->getDateDiff($date['min']);
            $maxNumberOfDay = $this->getDateDiff($date['max']);
            $response = str_replace(
                array(
                    '{product_with_min_d}',
                    '{product_with_min_date}',
                    '{product_with_max_d}',
                    '{product_with_max_date}'
                ),
                array(
                    $minNumberOfDay,
                    $this->getFormatedDate($date['min']),
                    $maxNumberOfDay,
                    $this->getFormatedDate($date['max'])
                ),
                $settingText
            );
            $response = $this->pregReplaceMinDate($date['min'], $response);
            $response = $this->pregReplaceMaxDate($date['max'], $response);
            return '<div class="rp_combine_estimated_date">' . $this->getIconHtml('delivery_text_icon_combine') . '<span class="rp_text">' . $response . '</span></div>';
        }

        /**
         * Function for get combine date for order as per settings
         * 
         * @return mixed 
         */
        public function getCombineDate()
        {
            if (WC()->cart->get_cart_contents_count() == 0) {
                return false;
            }
            $dateForEachProduct = array();
            foreach (WC()->cart->get_cart() as $item) {
                $_product = $item['data'];
                $estDay = $this->getEstimatedDateForProduct($_product);
                if ($estDay !== false) {
                    $dateForEachProduct[] = $estDay;
                }
            }

            $finalDate = $this->getFinalCombineDate($dateForEachProduct);
            return $finalDate;
        }

        /**
         * Function for get final combine date from each product date
         * 
         * @param array $dateForEachProduct
         * @return mixed
         */
        public function getFinalCombineDate($dateForEachProduct)
        {
            if (empty($dateForEachProduct)) {
                return false;
            }
            return array(
                "max" => max($dateForEachProduct),
                "min" => min($dateForEachProduct),
            );
        }

        /**
         *  Function for register css and js for frontend
         */
        public function enqueueScripts()
        {

            wp_enqueue_script(self::$plugin_slug . '-front', self::$plugin_url . "assets/js/script.js", array('jquery'), false, false);
            wp_register_style(self::$plugin_slug . '-handle', false);
            wp_enqueue_style(self::$plugin_slug . '-handle');
            wp_add_inline_style(self::$plugin_slug . '-handle', $this->getInlineCss());

            $optionParams = array(
                'ajaxUrl' => admin_url("admin-ajax.php"),
                'enableAjax' => $this->getSetting("load_using_ajax") ? true : false,
                'enableCarrier' => $this->getSetting("enable_carrier") ? true : false
            );
            if (is_product() || is_shop() || is_product_category() || is_product_tag()) {
                $optionParams['isWCPage'] = true;
                $optionParams['isProductPage'] = is_product() ? true : false;
            } else {
                $optionParams['isWCPage'] = false;
                $optionParams['isProductPage'] = false;
            }
            wp_localize_script(self::$plugin_slug . '-front', 'RPPDDF', $optionParams);
        }

        /**
         *  Function for register css and js for frontend
         */
        public function getInlineCss()
        {
            $css = ".rp_estimated_date{background-color:" . $this->getSetting('bg_text_color') . ";color:" . $this->getSetting('text_color') . ";font-size:" . $this->getSetting('text_size') . "px;margin: 10px 0;padding:5px 10px;}";
            $css .= ".rp_estimated_date.variation_date,.rp_estimated_date_carrier_date.variation_date{padding:0px;}";
            $css .= ".rp_estimated_date.variation_date .date_for_variation,.rp_estimated_date_carrier_date.variation_date .date_for_variation{padding:5px 10px;}";
            $css .= ".rp_combine_estimated_date{background-color:" . $this->getSetting('bg_text_color_combine_date') . ";color:" . $this->getSetting('text_color_combine_date') . ";font-size:" . $this->getSetting('text_size') . "px;margin: 5px 0;padding:5px 10px;}";
            $css .= ".rp_estimated_date.rp_outofstock,.rp_estimated_date .rp_outofstock{background-color:" . $this->getSetting('bg_text_color_outofstock') . ";color:" . $this->getSetting('text_color_outofstock') . ";}";
            $css .= ".rp_estimated_date_carrier_date {background-color:" . $this->getSetting('carrier_bg_text_color') . ";color:" . $this->getSetting('carrier_text_color') . ";font-size:" . $this->getSetting('text_size') . "px;padding:5px 10px;}";
            $css .= ".rp_estimated_date.rp_back_order,.rp_estimated_date .rp_back_order{background-color:" . $this->getSetting('bg_text_color_backorder') . ";color:" . $this->getSetting('text_color_backorder') . ";}";
            $css .= ".date_for_variation,.variation_date{display:none;text-align: left;align-items: center;}";
            $css .= ".rp_estimated_date,.rp_combine_estimated_date,.rp_estimated_date_carrier_date{display: flex;text-align: left;align-items: center;}";
            $css .= ".rp_estimated_date .rp_icon,.rp_combine_estimated_date .rp_icon{margin-right: 5px;}";
            $css .= ".rp_estimated_date .rp_icon img,.rp_combine_estimated_date .rp_icon img{max-width:50px;}";
            $css .= ".rp_estimated_date_carrier_date .rp_icon,.rp_combine_estimated_date .rp_icon{margin-right: 5px;}";
            $css .= ".rp_estimated_date_carrier_date .rp_icon img,.rp_combine_estimated_date .rp_icon img{max-width:50px;}";
            $css .= ".date_for_variation.date_variation_novariation {display:flex;}";
            if ($this->getSetting('hide_icon') == "1") {
                $css .= ".products  .rp_estimated_date  .rp_icon{display:none;}";
            }
            if ($this->getSetting('hide_icon_cart') == "1") {
                $css .= ".woocommerce-cart  .rp_estimated_date  .rp_icon,.woocommerce-checkout .rp_estimated_date  .rp_icon{display:none;}";
            }
            if (!empty($this->getSetting('custom_css'))) {
                $css .= $this->getSetting('custom_css');
            }
            return $css;
        }

        /**
         * Function for footer hook
         */
        public function wpFooter()
        {
            global $product;
        }

        /**
         * Function for get product page position
         */
        public function getProductPagePosition()
        {
            $position = $this->getSetting("text_pos");
            $positionHook = array();
            switch ($position) {
                case 1:
                    $positionHook = array(
                        'hook' => 'woocommerce_single_product_summary',
                        'position' => 20,
                    );
                    break;
                case 2:
                    $positionHook = array(
                        'hook' => 'woocommerce_single_product_summary',
                        'position' => 10,
                    );
                    break;
                case 3:
                    $positionHook = array(
                        'hook' => 'woocommerce_single_product_summary',
                        'position' => 30,
                    );
                    break;
                case 4:
                    $positionHook = array(
                        'hook' => 'woocommerce_before_add_to_cart_button',
                        'position' => 10,
                    );
                    break;

                default:
                    $positionHook = array(
                        'hook' => 'woocommerce_single_product_summary',
                        'position' => 8,
                    );
                    break;
            }
            return $positionHook;
        }

        /**
         * Function for display text on product page
         * 
         * @global object $product
         */
        public function dispalyDateOnProductPage()
        {
            global $product;

            if (empty($product)) {
                return;
            }

            $text = "";
            if ($this->getSetting("enable_carrier")) {
                $text .= $this->getCarrierTextForProductPage($product);
            }

            $text .= $this->getTextForProductPage($product);
            echo $text;
        }

        /**
         * Function for display text on product shop/archive page
         * 
         * @global object $product
         */
        public function dispalyDateOnProductShopPages()
        {
            global $product;

            if (empty($product)) {
                return;
            }

            echo $this->getTextForProductPage($product);
        }

        /**
         * Function for display text on cart and checkout page
         * 
         * @param string $title
         * @param object $values
         * 
         
         */
        public function displayOnCart($values)
        {

            if ($this->getSetting('enable_on_cart') != 1) {
                return '';
            }

            if (!$values || !$values['data']) {
                return '';
            }

            $_product = $values['data'];
            $estDay = $this->getEstimatedDateForProduct($_product);

            if ($estDay !== false) {
                if ($_product->get_type() == "variation") {

                    if ($this->getSetting('alt_text_cart_checkout')) {
                        $deliveryText = $this->getAlternativeTextForVariableCartAndCheckout($estDay, $_product, true);
                    } else {
                        $deliveryText = $this->getTextForVariableProduct($estDay, $_product, true);
                    }
                } else {
                    if ($this->getSetting('alt_text_cart_checkout')) {
                        $deliveryText = $this->getAlternativeTextCartAndCheckout($estDay, $_product);
                    } else {
                        $deliveryText = $this->getTextForProduct($estDay, $_product);
                    }
                }

                echo $deliveryText;
            }
        }

        public function displayOnCheckout($title, $values)
        {
            if (is_cart()) {
                return $title;
            }
            if ($this->getSetting('enable_on_cart') != 1) {
                return $title;
            }

            if (!$values || !$values['data']) {
                return $title;
            }

            $_product = $values['data'];
            $estDay = $this->getEstimatedDateForProduct($_product);

            if ($estDay !== false) {
                if ($_product->get_type() == "variation") {

                    if ($this->getSetting('alt_text_cart_checkout')) {
                        $deliveryText = $this->getAlternativeTextForVariableCartAndCheckout($estDay, $_product, true);
                    } else {
                        $deliveryText = $this->getTextForVariableProduct($estDay, $_product, true);
                    }
                } else {
                    if ($this->getSetting('alt_text_cart_checkout')) {
                        $deliveryText = $this->getAlternativeTextCartAndCheckout($estDay, $_product);
                    } else {
                        $deliveryText = $this->getTextForProduct($estDay, $_product);
                    }
                }

                return $title . $deliveryText;
            }
            return $title;
        }

        /**
         * Function for display text on order page
         * 
         * @param int $itemId
         * @param object $item
         */
        public function displayOrderItemMetaForCustomer($item_id, $item)
        {

            if ($this->getSetting('enable_on_orderpage') != 1) {
                return;
            }

            $dateMeta = wc_get_order_item_meta($item_id, self::$order_item_meta_key, true);
            if (!empty($dateMeta)) {
                echo $this->getEstimaedTextForOrderPage($dateMeta, $item);
            }
        }

        /**
         * Function for display text on order page
         * 
         * @param int $itemId
         * @param object $item
         */
        public function displayOrderItemMeta($itemId, $item)
        {
            if ($this->getSetting('enable_on_orderpage') != 1) {
                return;
            }
            $dateMeta = wc_get_order_item_meta($itemId, self::$order_item_meta_key, true);
            if (!empty($dateMeta)) {
                echo $this->getEstimaedTextForOrderPage($dateMeta, $item);
            }
        }

        /**
         * Function for display date in order email
         * @param int $itemId
         * @param object $item
         */
        public function displayInOrderEmail($html, $item, $args)
        {
            if ($this->getSetting('enable_on_orderemail') != 1) {
                return $html;
            }

            $dateMeta = wc_get_order_item_meta($item->get_id(), self::$order_item_meta_key, true);
            if (!empty($dateMeta)) {
                $html .= "<br>" . $this->getEstimaedTextForEmail($dateMeta, $item);
            }
            return $html;
        }

        /**
         * Function for save delivery date for order
         * 
         * @param int $itemId
         * @param object $cartItem
         * 
         */
        public function saveItemMeta($item, $cart_item_key, $values, $order)
        {
            $product = $values['data'];

            if (empty($product)) {
                return;
            }

            $estDay = $this->getEstimatedDateForProduct($product);
            if ($estDay) {
                $item->update_meta_data(self::$order_item_meta_key, $estDay);
            }
        }

        /**
         * Hide date meta key
         * 
         * @param array $arr
         * 
         * @return array
         */
        public function hideDateMeta($arr)
        {
            $arr[] = self::$order_item_meta_key;
            return $arr;
        }

        /**
         * Function for delivery date shortcode
         * 
         * @param array $attr 
         * @return mixed
         */
        public function srtDeliveryDate($attr)
        {
            if (!function_exists('WC') || !isset(WC()->customer) || !isset(WC()->countries)) {
                return;
            }
            global $product;
            if (!empty($attr) && isset($attr['product_id']) && is_numeric($attr['product_id'])) {
                $product = wc_get_product($attr['product_id']);
            }
            if (empty($product)) {
                return;
            }
            $text = "";
            if ($this->getSetting("enable_carrier")) {
                $text .= $this->getCarrierTextForProductPage($product);
            }

            $text .= $this->getTextForProductPage($product);
            return $text;
        }

        /**
         * Function for load date via ajax
         */
        public function ajaxGetCarrierDate()
        {

            $productID = $_POST['product_id'];
            $product = wc_get_product($productID);
            if (empty($product)) {
                die();
            }
            $carrierText = "";
            if ($this->getSetting("enable_carrier")) {
                $carrierText .= $this->getCarrierTextForProductPage($product);
            }

            echo json_encode(array("success" => 1, 'carrier_text' => $carrierText));
            die();
        }

        /**
         * Function for load date via ajax
         */
        public function ajaxGetDeliveryDateShop()
        {
            $productids = $_POST['ids'];
            $response = array();
            if (!empty($productids)) {
                foreach ($productids as $id) {
                    $product = wc_get_product($id);
                    $response[$id] = $this->getTextForProductPage($product);
                }
            }

            echo json_encode(array("success" => 1, 'text' => $response));
            die();
        }

        /**
         * Function for get text for order page
         * 
         * @param string $estDay
         * @param object $item
         * 
         * @return mixed
         */
        public function getEstimaedTextForOrderPage($estDay, $item)
        {
            if ($item->get_type() !== "line_item") {
                return;
            }
            $product = $item->get_product();
            $productEstMeta = get_post_meta($item->get_product_id(), self::$meta_key, true);
            $settingText = (isset($productEstMeta['esttext_orderpage']) && !empty($productEstMeta['esttext_orderpage'])) ? $productEstMeta['esttext_orderpage'] : $this->getSetting('text_order');

            if ('variation' === $product->get_type()) {
                $varProductEstMeta = get_post_meta($item->get_variation_id(), self::$meta_key, true);
                $settingText = (isset($varProductEstMeta['esttext_orderpage']) && !empty($varProductEstMeta['esttext_orderpage'])) ? $varProductEstMeta['esttext_orderpage'] : $settingText;
            }


            $settingText = stripslashes($settingText);
            $response = str_replace('{date}', $this->getFormatedDate($estDay), $settingText);
            $response = $this->pregReplaceDate($estDay, $response);
            return '<div class="rp_estimated_date">' . $response . '</div>';
        }

        /**
         * Function for get delivery text for email
         * 
         * @param string $estDay
         * @param object $item
         * 
         * @return string
         */
        public function getEstimaedTextForEmail($estDay, $item)
        {
            $product = $item->get_product();
            $productEstMeta = get_post_meta($item->get_product_id(), self::$meta_key, true);
            $settingText = (isset($productEstMeta['esttext_orderpage']) && !empty($productEstMeta['esttext_orderpage'])) ? $productEstMeta['esttext_orderpage'] : $this->getSetting('text_order');
            if ('variation' === $product->get_type()) {
                $varProductEstMeta = get_post_meta($item->get_variation_id(), self::$meta_key, true);
                $settingText = (isset($varProductEstMeta['esttext_orderpage']) && !empty($varProductEstMeta['esttext_orderpage'])) ? $varProductEstMeta['esttext_orderpage'] : $settingText;
            }
            $settingText = stripslashes($settingText);
            $response = str_replace('{date}', $this->getFormatedDate($estDay), $settingText);
            $response = $this->pregReplaceDate($estDay, $response);
            return '<small>' . $response . '</small>';
        }

        /**
         * Function for check delivery date disply for outofstock product or for backorder product
         * 
         * @param object $product 
         */
        public function isDisplayForBackOrderAndOutofstock($product)
        {
            if ($this->getSetting("hide_out_of_stock") == 1 && !$product->is_in_stock()) {
                return false;
            }


            if ($this->getSetting("hide_backorder") == 1 && $product->is_on_backorder(1)) {
                return false;
            }


            if ($this->getSetting("hide_backorder") == 0 && $this->getSetting("backorder_only") == 1 && !$product->is_on_backorder(1)) {
                return false;
            }


            return true;
        }

        /**
         * Function for get day setting for simple instock product
         * 
         * @param object $product 
         * @param object $estSetting 
         * 
         * @return string
         */
        public function getDaySettingForSimpleInStock($product, $estSetting)
        {
            $estimateTime = ($this->getSetting('enable_for') == 1) ? $this->getSetting('estimate_time') : "";
            if (isset($estSetting['esttime']) && is_numeric($estSetting['esttime'])) {
                return $estSetting['esttime'];
            }
            return $estimateTime;
        }

        /**
         * Function for get day setting for simple instock product
         * 
         * @param object $product 
         * @param object $estSetting 
         * 
         * @return string
         */
        public function getCarrierDaySettingForSimpleInStock($product, $estSetting)
        {
            $estimateTime = ($this->getSetting('enable_for') == 1) ? $this->getSetting('carrier_estimate_time') : "";
            if (isset($estSetting['carrier_esttime']) && is_numeric($estSetting['carrier_esttime'])) {
                return $estSetting['carrier_esttime'];
            }
            return $estimateTime;
        }

        /**
         * Function for get day setting for simple outofstock product
         * 
         * @param object $product 
         * @param object $estSetting 
         * 
         * @return string
         */
        public function getDaySettingForSimpleOutofstock($product, $estSetting)
        {
            $estimateTimeOutofStock = ($this->getSetting('enable_for') == 1) ?  $this->getSetting('estimate_time_outofstock') : "";;
            if (isset($estSetting['esttime_outofstock']) && is_numeric($estSetting['esttime_outofstock'])) {
                return $estSetting['esttime_outofstock'];
            }
            return $estimateTimeOutofStock;
        }

        /**
         * Function for get day setting for simple outofstock product
         * 
         * @param object $product 
         * @param object $estSetting 
         * 
         * @return string
         */
        public function getCarrierDaySettingForSimpleOutofstock($product, $estSetting)
        {
            $estimateTimeOutofStock = ($this->getSetting('enable_for') == 1) ?  $this->getSetting('carrier_estimate_time_outofstock') : "";
            if (isset($estSetting['carrier_esttime_outofstock']) && is_numeric($estSetting['carrier_esttime_outofstock'])) {
                return $estSetting['carrier_esttime_outofstock'];
            }
            return $estimateTimeOutofStock;
        }

        /**
         * Function for get day setting for simple backorder product
         * 
         * @param object $product 
         * @param object $estSetting 
         * 
         * @return string
         */
        public function getDaySettingForSimpleBackorder($product, $estSetting)
        {
            $estimateTimeBackorder = ($this->getSetting('enable_for') == 1) ?  $this->getSetting('estimate_time_backorder') : "";
            if (isset($estSetting['esttime_backorder']) && is_numeric($estSetting['esttime_backorder'])) {
                return $estSetting['esttime_backorder'];
            }
            return $estimateTimeBackorder;
        }

        /**
         * Function for get day setting for simple backorder product
         * 
         * @param object $product 
         * @param object $estSetting 
         * 
         * @return string
         */
        public function getCarrierDaySettingForSimpleBackorder($product, $estSetting)
        {
            $estimateTimeBackorder = ($this->getSetting('enable_for') == 1) ? $this->getSetting('carrier_estimate_time_backorder') : "";
            if (isset($estSetting['carrier_esttime_backorder']) && is_numeric($estSetting['carrier_esttime_backorder'])) {
                return $estSetting['carrier_esttime_backorder'];
            }
            return $estimateTimeBackorder;
        }

        /**
         * Function for get day setting for variation instock
         * 
         * @param object $product 
         * @param object $estSetting 
         * 
         * @return string
         */
        public function getDaySettingForVariationInStock($product, $estSetting)
        {
            $estSettingParent = get_post_meta($product->get_parent_id(), self::$meta_key, true);
            $estimateTime = ($this->getSetting('enable_for') == 1) ? $this->getSetting('estimate_time') : "";
            
            if (isset($estSetting['esttime']) && is_numeric($estSetting['esttime'])) {
                return $estSetting['esttime'];
            }

            if (isset($estSettingParent['esttime']) && is_numeric($estSettingParent['esttime'])) {
                return $estSettingParent['esttime'];
            }
            return $estimateTime;
        }

        /**
         * Function for get day setting for variation instock
         * 
         * @param object $product 
         * @param object $estSetting 
         * 
         * @return string
         */
        public function getCarrierDaySettingForVariationInStock($product, $estSetting)
        {
            $estSettingParent = get_post_meta($product->get_parent_id(), self::$meta_key, true);
            $estimateTime = ($this->getSetting('enable_for') == 1) ? $this->getSetting('carrier_estimate_time') : "";
            if (isset($estSetting['carrier_esttime']) && is_numeric($estSetting['carrier_esttime'])) {
                return $estSetting['carrier_esttime'];
            }
            if (isset($estSettingParent['carrier_esttime']) && is_numeric($estSettingParent['carrier_esttime'])) {
                return $estSettingParent['carrier_esttime'];
            }
            return $estimateTime;
        }

        /**
         * Function for get day setting for variation outofstock
         * 
         * @param object $product 
         * @param object $estSetting 
         * 
         * @return string
         */
        public function getDaySettingForVariationOutofstock($product, $estSetting)
        {
            $estSettingParent = get_post_meta($product->get_parent_id(), self::$meta_key, true);
            $estimateTimeOutofStock = ($this->getSetting('enable_for') == 1) ? $this->getSetting('estimate_time_outofstock') : "";
            if (isset($estSetting['esttime_outofstock']) && is_numeric($estSetting['esttime_outofstock'])) {
                return $estSetting['esttime_outofstock'];
            }
            if (isset($estSettingParent['esttime_outofstock']) && is_numeric($estSettingParent['esttime_outofstock'])) {
                return $estSettingParent['esttime_outofstock'];
            }
            return $estimateTimeOutofStock;
        }

        /**
         * Function for get day setting for variation outofstock
         * 
         * @param object $product 
         * @param object $estSetting 
         * 
         * @return string
         */
        public function getCarrierDaySettingForVariationOutofstock($product, $estSetting)
        {
            $estSettingParent = get_post_meta($product->get_parent_id(), self::$meta_key, true);
            $estimateTimeOutofStock = ($this->getSetting('enable_for') == 1) ? $this->getSetting('carrier_estimate_time_outofstock') : "";
            if (isset($estSetting['carrier_esttime_outofstock']) && is_numeric($estSetting['carrier_esttime_outofstock'])) {
                return $estSetting['carrier_esttime_outofstock'];
            }
            if (isset($estSettingParent['carrier_esttime_outofstock']) && is_numeric($estSettingParent['carrier_esttime_outofstock'])) {
                return $estSettingParent['carrier_esttime_outofstock'];
            }
            return $estimateTimeOutofStock;
        }

        /**
         * Function for get day setting for variation backorder
         * 
         * @param object $product 
         * @param object $estSetting 
         * 
         * @return string
         */
        public function getDaySettingForVariationBackorder($product, $estSetting)
        {
            $estSettingParent = get_post_meta($product->get_parent_id(), self::$meta_key, true);
            $estimateTimeBackorder = ($this->getSetting('enable_for') == 1) ? $this->getSetting('estimate_time_backorder') : "";
            if (isset($estSetting['esttime_backorder']) && is_numeric($estSetting['esttime_backorder'])) {
                return $estSetting['esttime_backorder'];
            }
            if (isset($estSettingParent['esttime_backorder']) && is_numeric($estSettingParent['esttime_backorder'])) {
                return $estSettingParent['esttime_backorder'];
            }
            return $estimateTimeBackorder;
        }

        /**
         * Function for get day setting for variation backorder
         * 
         * @param object $product 
         * @param object $estSetting 
         * 
         * @return string
         */
        public function getCarrierDaySettingForVariationBackorder($product, $estSetting)
        {
            $estSettingParent = get_post_meta($product->get_parent_id(), self::$meta_key, true);
            $estimateTimeBackorder = ($this->getSetting('enable_for') == 1) ? $this->getSetting('carrier_estimate_time_backorder') : "";
            if (isset($estSetting['carrier_esttime_backorder']) && is_numeric($estSetting['carrier_esttime_backorder'])) {
                return $estSetting['carrier_esttime_backorder'];
            }
            if (isset($estSettingParent['carrier_esttime_backorder']) && is_numeric($estSettingParent['carrier_esttime_backorder'])) {
                return $estSettingParent['carrier_esttime_backorder'];
            }
            return $estimateTimeBackorder;
        }

        /**
         * Function for get day setting for product
         * 
         * @param object $product 
         * @return string
         */
        public function getDaySettingsForProduct($product)
        {

            $isOutofStock = (!$product->is_in_stock()) ? true : false;
            $isBackorder = ($product->is_on_backorder(1)) ? true : false;

            $estSetting = get_post_meta($product->get_id(), self::$meta_key, true);
            $estDy = ($product->get_type() == "variation") ? $this->getDaySettingForVariationInStock($product, $estSetting) : $this->getDaySettingForSimpleInStock($product, $estSetting);

            if ($isOutofStock === true) {
                $estDyOutofStock = ($product->get_type() == "variation") ? $this->getDaySettingForVariationOutofstock($product, $estSetting) : $this->getDaySettingForSimpleOutofstock($product, $estSetting);
                $estDy = !empty($estDyOutofStock) ? $estDyOutofStock : $estDy;
            }

            if ($isBackorder === true) {
                $estDyBackorder = ($product->get_type() == "variation") ? $this->getDaySettingForVariationBackorder($product, $estSetting) : $this->getDaySettingForSimpleBackorder($product, $estSetting);
                $estDy = !empty($estDyBackorder) ? $estDyBackorder : $estDy;
            }
            return $estDy;
        }

        /**
         * Function for get day setting for product
         * 
         * @param object $product 
         * @return string
         */
        public function getCarrierDaySettingsForProduct($product)
        {

            $isOutofStock = (!$product->is_in_stock()) ? true : false;
            $isBackorder = ($product->is_on_backorder(1)) ? true : false;

            $estSetting = get_post_meta($product->get_id(), self::$meta_key, true);
            $estDy = ($product->get_type() == "variation") ? $this->getCarrierDaySettingForVariationInStock($product, $estSetting) : $this->getCarrierDaySettingForSimpleInStock($product, $estSetting);

            if ($isOutofStock === true) {
                $estDyOutofStock = ($product->get_type() == "variation") ? $this->getCarrierDaySettingForVariationOutofstock($product, $estSetting) : $this->getCarrierDaySettingForSimpleOutofstock($product, $estSetting);
                $estDy = !empty($estDyOutofStock) ? $estDyOutofStock : $estDy;
            }

            if ($isBackorder === true) {
                $estDyBackorder = ($product->get_type() == "variation") ? $this->getCarrierDaySettingForVariationBackorder($product, $estSetting) : $this->getCarrierDaySettingForSimpleBackorder($product, $estSetting);
                $estDy = !empty($estDyBackorder) ? $estDyBackorder : $estDy;
            }
            return $estDy;
        }

        /**
         * Function delivery date for product
         * 
         * @param object $product 
         * @return string
         */
        public function getEstimatedDateForProduct($product)
        {

            if ($this->isDisplayForBackOrderAndOutofstock($product) === false) {
                return false;
            }

            $estDy = $this->getDaySettingsForProduct($product);

            if(trim($estDy)==""){
                return false;
            }

            $estDy = is_numeric($estDy)?$estDy:0;
            $estDy = $estDy + $this->getShippingMethodDeliveryTime();
            if (trim($estDy) == "") {
                return false;
            }

            $estTime = $this->calculateDayEndTimeSettings($estDy);

            $blockDate = $this->getBlockDates();
            $blockWeekday = $this->getBlockWeekday();
            $estDateCount = 0;
            $totalDayCount = 0;
            $currentDay = strtotime(date('Y-m-d'));
            $estDay = strtotime(date('Y-m-d'));
            while ($estTime >= $estDateCount) {
                $estDay = strtotime('+' . $totalDayCount . ' day', $currentDay);
                $weekDay = date('w', $estDay);
                if (!in_array($estDay, $blockDate) && !in_array($weekDay, $blockWeekday)) {
                    $estDateCount++;
                }

                $totalDayCount++;
            }
            return $estDay;
        }


        public function getShippingMethodDeliveryTime()
        {
            $shippingTime=0;
            $chosenShipping = WC()->session->get('chosen_shipping_methods');
            if (!empty($chosenShipping) && isset($chosenShipping[0])) {
                $shippingMethod = $chosenShipping[0];
                $addinationShippingSettings = $this->getSetting('addition_shipping_time');
                if (isset($addinationShippingSettings[$shippingMethod]) && !empty($addinationShippingSettings[$shippingMethod]) && is_numeric($addinationShippingSettings[$shippingMethod])) {
                    $shippingTime=$addinationShippingSettings[$shippingMethod];
                }
            }
            return $shippingTime;
        }

        /**
         * Function delivery date to carrier for product
         * 
         * @param object $product 
         * @return string
         */
        public function getCarrierEstimatedDateForProduct($product)
        {

            if ($this->isDisplayForBackOrderAndOutofstock($product) === false) {
                return false;
            }

            $estDy = $this->getCarrierDaySettingsForProduct($product);

            if (trim($estDy) == "") {
                return false;
            }

            $estTime = $this->calculateDayEndTimeSettings($estDy);

            $blockDate = $this->getBlockDates();
            $blockWeekday = $this->getBlockWeekday();
            $estDateCount = 0;
            $totalDayCount = 0;
            $currentDay = strtotime(date('Y-m-d'));
            $estDay = strtotime(date('Y-m-d'));
            while ($estTime >= $estDateCount) {
                $estDay = strtotime('+' . $totalDayCount . ' day', $currentDay);
                $weekDay = date('w', $estDay);
                if (!in_array($estDay, $blockDate) && !in_array($weekDay, $blockWeekday)) {
                    $estDateCount++;
                }
                $totalDayCount++;
            }
            return $estDay;
        }

        /**
         * Function for get delivery text for product page
         * 
         * @param object $product 
         * 
         * @return  string
         */
        public function getTextForProductPage($product)
        {
            global $woocommerce_loop;
            $dateText = "";
            if ($product->is_type('variable')) {
                $chidData = $product->get_children();
                if (count($chidData) > 0) {
                    $arrayDays = array();

                    $dateText = "<div class='rp_estimated_date variation_date dpid_" . $product->get_id() . " ' data-pid='" . $product->get_id() . "' data-loaded='false'>";
                    foreach ($chidData as $variation_id) {
                        $variation_obj = new WC_Product_variation($variation_id);
                        $estDay = $this->getEstimatedDateForProduct($variation_obj);
                        
                        if ($estDay !== false) {
                            $arrayDays[] = $estDay;
                            $dateText .= $this->getTextForVariableProduct($estDay, $variation_obj);
                        }
                    }
                    if(!empty($arrayDays)){
                        $dateText .= $this->getNoVariationSelectedText($arrayDays);
                    }
                    $dateText .= "</div>";
                } else {

                    $estDay = $this->getEstimatedDateForProduct($product);
                    if ($estDay !== false) {
                        $dateText = $this->getTextForProduct($estDay, $product);
                    }
                }
            } else {
                $estDay = $this->getEstimatedDateForProduct($product);
                if ($estDay !== false) {
                    $dateText = $this->getTextForProduct($estDay, $product);
                }
            }
            return $dateText;
        }

        public function getNoVariationSelectedText($arrayDays)
        {

            $settingText = $this->getSetting('delivery_text_no_vairation');

            if (empty($settingText)) {
                return '';
            }

            $maxDay = max($arrayDays);
            $minDay = min($arrayDays);
            $numberOfDayMax = $this->getDateDiff($maxDay);
            $numberOfDayMin = $this->getDateDiff($minDay);
            $response = str_replace(array('{variation_min_date}', '{variation_max_date}', "{variation_min_d}", '{variation_max_d}'), array($this->getFormatedDate($minDay), $this->getFormatedDate($maxDay), $numberOfDayMin, $numberOfDayMax, ), $settingText);
            return '<div class="date_variation_novariation date_for_variation">' . $this->getIconHtml('delivery_text_icon') . '<span class="rp_text">' . $response . '</span></div>';
        }

        public function getCarrierTextForProductPage($product)
        {

            $dateText = "";
            if ($product->is_type('variable')) {
                $chidData = $product->get_children();
                if (count($chidData) > 0) {

                    $dateText = "<div class='rp_estimated_date_carrier_date variation_date' data-pid='" . $product->get_id() . "'>";
                    foreach ($chidData as $variation_id) {
                        $variation_obj = new WC_Product_variation($variation_id);
                        $estDay = $this->getCarrierEstimatedDateForProduct($variation_obj);
                        if ($estDay !== false) {
                            $dateText .= $this->getCarrierForVariableProduct($estDay, $variation_obj);
                        }
                    }
                    $dateText .= "</div>";
                } else {

                    $estDay = $this->getCarrierEstimatedDateForProduct($product);
                    if ($estDay !== false) {
                        $dateText = $this->getCarrierTextForProduct($estDay, $product);
                    }
                }
            } else {
                $estDay = $this->getCarrierEstimatedDateForProduct($product);
                if ($estDay !== false) {
                    $dateText = $this->getCarrierTextForProduct($estDay, $product);
                }
            }
            return $dateText;
        }

        /**
         * Function for get text for variable product
         * 
         * @param string $estDay
         * @param int $productId
         * @param bool $isOutofstock
         * @return string
         */
        public function getTextForVariableProduct($estDay, $product, $isCart = false)
        {

            $class = "";
            $estSetting = get_post_meta($product->get_parent_id(), self::$meta_key, true);

            $estSettingVariation = get_post_meta($product->get_id(), self::$meta_key, true);
            $settingText = (isset($estSettingVariation['esttext']) && trim($estSettingVariation['esttext']) != "") ? $estSettingVariation['esttext'] : ((isset($estSetting['esttext']) && trim($estSetting['esttext']) != "") ? $estSetting['esttext'] : $this->getSetting('estimate_text'));
            if (!$product->is_in_stock()) {
                $estimateTextOutofStock = (isset($estSettingVariation['esttext_outofstock']) && trim($estSettingVariation['esttext_outofstock']) != "") ? $estSettingVariation['esttext_outofstock'] : ((isset($estSetting['esttext_outofstock']) && trim($estSetting['esttext_outofstock']) != "") ? $estSetting['esttext_outofstock'] : $this->getSetting('estimate_text_outofstock'));
                $settingText = !empty($estimateTextOutofStock) ? $estimateTextOutofStock : $settingText;
                $class = "rp_outofstock";
            }
            if ($product->is_on_backorder(1)) {
                $estimateTextBackorder = (isset($estSettingVariation['esttext_backorder']) && trim($estSettingVariation['esttext_backorder']) != "") ? $estSettingVariation['esttext_backorder'] : ((isset($estSetting['esttext_backorder']) && trim($estSetting['esttext_backorder']) != "") ? $estSetting['esttext_backorder'] : $this->getSetting('estimate_text_backorder'));
                $settingText = !empty($estimateTextBackorder) ? $estimateTextBackorder : $settingText;
                $class = "rp_back_order";
            }
            $settingText = __(stripslashes($settingText));
            if (strpos($settingText, "{time_limit}") !== false && ($product->is_in_stock() || $product->is_on_backorder(1))) {
                $timeLimit = $this->getTimeLimit();

                if ($timeLimit !== false) {
                    $settingText = str_replace("{time_limit}", $timeLimit, $settingText);
                } else {
                    $settingText = __(stripslashes($this->getAlternativeTextVariable($estSetting, $estSettingVariation, $product)));
                }
            }
            $numberOfDay = $this->getDateDiff($estDay);
            $response = str_replace(array('{d}', '{date}'), array($numberOfDay, $this->getFormatedDate($estDay)), $settingText);
            $response = $this->pregReplaceDate($estDay, $response);

            if ($isCart === true) {
                return '<div class="rp_estimated_date ' . $class . '">' . $this->getIconHtml('delivery_text_icon') . '<span class="rp_text">' . $response . '</span></div>';
            }
            return '<div class="date_variation_' . $product->get_id() . ' date_for_variation ' . $class . '">' . $this->getIconHtml('delivery_text_icon') . '<span class="rp_text">' . $response . '</span></div>';
        }

        /**
         * Function for get text for variable product
         * 
         * @param string $estDay
         * @param int $productId
         * @param bool $isOutofstock
         * @return string
         */
        public function getCarrierForVariableProduct($estDay, $product, $isCart = false)
        {
            $class = "";
            $class = "";
            $settingText = __(stripslashes($this->getSetting('delivery_text_carier')));
            $numberOfDay = $this->getDateDiff($estDay);
            $response = str_replace(array('{d}', '{date}'), array($numberOfDay, $this->getFormatedDate($estDay)), $settingText);
            $response = $this->pregReplaceDate($estDay, $response);

            return '<div class="date_variation_' . $product->get_id() . ' date_for_variation ' . $class . '">' . $this->getIconHtml('carrier_delivery_text_icon') . '<span class="rp_text">' . $response . '</span></div>';
        }

        /**
         * Function for get alternative text
         * 
         * @param array $estSetting
         * @param object $product
         * @return string
         */
        public function getAlternativeText($estSetting, $product)
        {
            $settingText = (isset($estSetting['alt_esttext']) && trim($estSetting['alt_esttext']) != "") ? $estSetting['alt_esttext'] : $this->getSetting('alt_estimate_text');

            if ($product->is_on_backorder(1)) {
                $estimateTextBackorder = (isset($estSetting['alt_esttext_backorder']) && trim($estSetting['alt_esttext_backorder']) != "") ? $estSetting['alt_esttext_backorder'] : $this->getSetting('alt_estimate_text_backorder');
                $settingText = !empty($estimateTextBackorder) ? $estimateTextBackorder : $settingText;
            }
            return $settingText;
        }

        /**
         * Function for get alternative text
         * 
         * @param array $estSetting
         * @param object $product
         * @return string
         */
        public function getAlternativeTextVariable($estSetting, $estSettingVariation, $product)
        {
            $settingText = (isset($estSettingVariation['alt_esttext']) && trim($estSettingVariation['alt_esttext']) != "") ? $estSettingVariation['alt_esttext'] : ((isset($estSetting['alt_esttext']) && trim($estSetting['alt_esttext']) != "") ? $estSetting['alt_esttext'] : $this->getSetting('alt_estimate_text'));
            if ($product->is_on_backorder(1)) {
                $estimateTextBackorder = (isset($estSettingVariation['alt_esttext_backorder']) && trim($estSettingVariation['alt_esttext_backorder']) != "") ? $estSettingVariation['alt_esttext_backorder'] : ((isset($estSetting['alt_esttext_backorder']) && trim($estSetting['alt_esttext_backorder']) != "") ? $estSetting['alt_esttext_backorder'] : $this->getSetting('alt_estimate_text_backorder'));
                $settingText = !empty($estimateTextBackorder) ? $estimateTextBackorder : $settingText;
            }
            return $settingText;
        }

        /**
         * Function for get alternative text for cart and checkout
         * 
         * @param array $estSetting
         * @param object $product
         * @return string
         */
        public function getAlternativeTextCartAndCheckout($estDay, $product)
        {
            $class = "";
            $estSetting = get_post_meta($product->get_id(), self::$meta_key, true);
            $settingText = (isset($estSetting['alt_esttext']) && trim($estSetting['alt_esttext']) != "") ? $estSetting['alt_esttext'] : $this->getSetting('alt_estimate_text');
            if (!$product->is_in_stock()) {
                $estimateTextOutofStock = (isset($estSetting['esttext_outofstock']) && trim($estSetting['esttext_outofstock']) != "") ? $estSetting['esttext_outofstock'] : $this->getSetting('estimate_text_outofstock');
                $settingText = !empty($estimateTextOutofStock) ? $estimateTextOutofStock : $this->getSetting('alt_estimate_text');
                $class = "rp_outofstock";
            }
            if ($product->is_on_backorder(1)) {
                $estimateTextBackorder = (isset($estSetting['alt_esttext_backorder']) && trim($estSetting['alt_esttext_backorder']) != "") ? $estSetting['alt_esttext_backorder'] : $this->getSetting('alt_estimate_text_backorder');
                $settingText = !empty($estimateTextBackorder) ? $estimateTextBackorder : $settingText;
                $class = "rp_back_order";
            }
            $numberOfDay = $this->getDateDiff($estDay);
            $response = str_replace(array('{d}', '{date}'), array($numberOfDay, $this->getFormatedDate($estDay)), $settingText);
            $response = $this->pregReplaceDate($estDay, $response);
            return '<div class="rp_estimated_date ' . $class . '">' . $this->getIconHtml('delivery_text_icon') . '<span class="rp_text">' . $response . '</span></div>';
        }

        /**
         * Function for get alternative text for cart and checkout
         * 
         * @param array $estSetting
         * @param object $product
         * @return string
         */
        public function getAlternativeTextForVariableCartAndCheckout($estDay, $product, $isCart)
        {
            $class = "";
            $estSetting = get_post_meta($product->get_parent_id(), self::$meta_key, true);

            $estSettingVariation = get_post_meta($product->get_id(), self::$meta_key, true);
            $settingText = (isset($estSettingVariation['alt_esttext']) && trim($estSettingVariation['alt_esttext']) != "") ? $estSettingVariation['alt_esttext'] : ((isset($estSetting['alt_esttext']) && trim($estSetting['alt_esttext']) != "") ? $estSetting['alt_esttext'] : $this->getSetting('alt_estimate_text'));
            if (!$product->is_in_stock()) {
                $estimateTextOutofStock = (isset($estSettingVariation['esttext_outofstock']) && trim($estSettingVariation['esttext_outofstock']) != "") ? $estSettingVariation['esttext_outofstock'] : ((isset($estSetting['esttext_outofstock']) && trim($estSetting['esttext_outofstock']) != "") ? $estSetting['esttext_outofstock'] : $this->getSetting('estimate_text_outofstock'));
                $settingText = !empty($estimateTextOutofStock) ? $estimateTextOutofStock : $settingText;
                $class = "rp_outofstock";
            }
            if ($product->is_on_backorder(1)) {
                $estimateTextBackorder = (isset($estSettingVariation['alt_esttext_backorder']) && trim($estSettingVariation['alt_esttext_backorder']) != "") ? $estSettingVariation['alt_esttext_backorder'] : ((isset($estSetting['alt_esttext_backorder']) && trim($estSetting['alt_esttext_backorder']) != "") ? $estSetting['alt_esttext_backorder'] : $this->getSetting('alt_estimate_text_backorder'));
                $settingText = !empty($estimateTextBackorder) ? $estimateTextBackorder : $settingText;
                $class = "rp_back_order";
            }
            $settingText = __(stripslashes($settingText));
            $numberOfDay = $this->getDateDiff($estDay);
            $response = str_replace(array('{d}', '{date}'), array($numberOfDay, $this->getFormatedDate($estDay)), $settingText);
            $response = $this->pregReplaceDate($estDay, $response);

            if ($isCart === true) {
                return '<div class="rp_estimated_date ' . $class . '">' . $this->getIconHtml('delivery_text_icon') . '<span class="rp_text">' . $response . '</span></div>';
            }
            return '<div class="date_variation_' . $product->get_id() . ' date_for_variation ' . $class . '">' . $this->getIconHtml('delivery_text_icon') . '<span class="rp_text">' . $response . '</span></div>';
        }

        /**
         * Function for get text for product
         * 
         * @param string $estDay
         * @param int $productId
         * @param bool $isOutofstock
         * @return string
         */
        public function getTextForProduct($estDay, $product)
        {
            global $woocommerce_loop;
            $class = "";
            $estSetting = get_post_meta($product->get_id(), self::$meta_key, true);
            $settingText = (isset($estSetting['esttext']) && trim($estSetting['esttext']) != "") ? $estSetting['esttext'] : $this->getSetting('estimate_text');
            if (!$product->is_in_stock()) {
                $estimateTextOutofStock = (isset($estSetting['esttext_outofstock']) && trim($estSetting['esttext_outofstock']) != "") ? $estSetting['esttext_outofstock'] : $this->getSetting('estimate_text_outofstock');
                $settingText = !empty($estimateTextOutofStock) ? $estimateTextOutofStock : $this->getSetting('alt_estimate_text');
                $class = "rp_outofstock";
            }
            if ($product->is_on_backorder(1)) {
                $estimateTextBackorder = (isset($estSetting['esttext_backorder']) && trim($estSetting['esttext_backorder']) != "") ? $estSetting['esttext_backorder'] : $this->getSetting('estimate_text_backorder');
                $settingText = !empty($estimateTextBackorder) ? $estimateTextBackorder : $settingText;
                $class = "rp_back_order";
            }
            $settingText = __(stripslashes($settingText));
            if (strpos($settingText, "{time_limit}") !== false && ($product->is_in_stock() || $product->is_on_backorder(1))) {

                $timeLimit = $this->getTimeLimit();
                if ($timeLimit !== false) {
                    $settingText = str_replace("{time_limit}", $timeLimit, $settingText);
                } else {
                    $settingText = __(stripslashes($this->getAlternativeText($estSetting, $product)));
                }
            }

            $numberOfDay = $this->getDateDiff($estDay);
            $response = str_replace(array('{d}', '{date}'), array($numberOfDay, $this->getFormatedDate($estDay)), $settingText);
            $response = $this->pregReplaceDate($estDay, $response);

            return '<div class="rp_estimated_date ' . $class . ' dpid_' . $product->get_id() . '" data-pid="' . $product->get_id() . '" data-loaded="false">' . $this->getIconHtml('delivery_text_icon') . '<span class="rp_text">' . $response . '</span></div>';
        }

        /**
         * Function for get time limit
         */
        public function getTimeLimit()
        {
            $weekDay = current_time('w');
            $estDay = strtotime(current_time("Y-m-d"));
            $blockDate = $this->getBlockDates();
            $blockWeekday = $this->getBlockWeekday();

            if (in_array($estDay, $blockDate) || in_array($weekDay, $blockWeekday)) {
                return false;
            }
            $hoursSettings = $this->getSetting('hours');
            $minitueSettings = $this->getSetting('minute');
            $endHours = isset($hoursSettings[$weekDay]) ? $hoursSettings[$weekDay] : 23;
            $endMinute = isset($minitueSettings[$weekDay]) ? $minitueSettings[$weekDay] : 59;

            $endDate = current_time("Y-m-d") . " " . $endHours . ":" . $endMinute . ":00";
            $endDeliveryTimeUnix = strtotime($endDate);
            $currentDay = current_time("Y-m-d H:i:s");
            $currentDayUnix = current_time("timestamp");
            if ($currentDayUnix < $endDeliveryTimeUnix) {
                $datetime1 = date_create($endDate);
                $datetime2 = date_create($currentDay);
                $interval = date_diff($datetime1, $datetime2);
                $min = $interval->format('%i');
                $hour = $interval->format('%h');
                if ($hour > 0) {
                    return $hour . " " . __("Hours") . " " . $min . " " . __("Minutes");
                } else {
                    return $min . " " . __("Minutes");
                }
            }
            return false;
        }

        /**
         * Function for get text for product
         * 
         * @param string $estDay
         * @param int $productId
         * @param bool $isOutofstock
         * @return string
         */
        public function getCarrierTextForProduct($estDay, $product)
        {
            $class = "";
            $settingText = __(stripslashes($this->getSetting('delivery_text_carier')));
            $numberOfDay = $this->getDateDiff($estDay);
            $response = str_replace(array('{d}', '{date}'), array($numberOfDay, $this->getFormatedDate($estDay)), $settingText);
            $response = $this->pregReplaceDate($estDay, $response);

            return '<div class="rp_estimated_date_carrier_date' . $class . '" data-pid="' . $product->get_id() . '">' . $this->getIconHtml('carrier_delivery_text_icon') . '<span class="rp_text">' . $response . '</span></div>';
        }

        /**
         * Function for calculate day end time settings
         * 
         * @param string $estTime
         * @return string
         */
        public function calculateDayEndTimeSettings($estTime)
        {
            $weekDay = current_time('w');

            $hoursSettings = $this->getSetting('hours');
            $minitueSettings = $this->getSetting('minute');
            $endHours = isset($hoursSettings[$weekDay]) ? $hoursSettings[$weekDay] : 23;
            $endMinute = isset($minitueSettings[$weekDay]) ? $minitueSettings[$weekDay] : 59;
            $endDeliveryTime = strtotime(current_time("Y-m-d") . " " . $endHours . ":" . $endMinute . ":00");
            $currentDay = current_time("timestamp");
            $blockDate = $this->getBlockDates();
            $blockWeekday = $this->getBlockWeekday();
            $estDay = strtotime(current_time("Y-m-d"));
            if (in_array($estDay, $blockDate) || in_array($weekDay, $blockWeekday)) {
                return $estTime;
            }
            if ($endHours == "" || $currentDay > $endDeliveryTime) {
                return $estTime + 1;
            }
            return $estTime;
        }
    }

}