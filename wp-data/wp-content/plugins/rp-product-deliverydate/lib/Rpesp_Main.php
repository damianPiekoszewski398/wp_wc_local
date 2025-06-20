<?php

if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Rpesp_Main')) {

    class Rpesp_Main {

        /**
         * plugin url 
         * @var string
         */
        public static $plugin_url;

        /**
         * plugin directory
         * @var string
         */
        public static $plugin_dir;

        /**
         * plugin unique text domain
         * @var string
         */
        public static $textdomain = "rp-product-deliverydate";

        /**
         * plugin title
         * @var string
         */
        public static $plugin_title = "Product Est Date";

        /**
         * plugin unique slug
         * @var string
         */
        public static $plugin_slug = "rpesp-setting";

        /**
         * plugin general setting key
         * @var string
         */
        public static $rpesp_option_key = "rpesp-setting";

        /**
         * Meta key for product setting
         * @var string
         */
        public static $meta_key = "rpesp-meta-setting";

        /**
         * Meta key for order item
         * @var string
         */
        public static $order_item_meta_key = "_rpesp-item-deliverydate";

        /**
         * plugin settings
         * @var array
         */
        public $rpesp_settings = array();

        /**
         * plugin default settings
         * @var array
         */
        public $default_settings = array(
            "display_on_product" => "1",
            "hide_product_setting" => "0",
            "text_pos" => "1",
            "combine_date" => "0",
            "estimate_text" => "Delivery on {date}",
            "text_order" => "Delivery on {date}",
            "date_format" => "d, F Y",
            "text_color" => "#000000",
            "text_color_combine_date" => "#000000",
            "text_size" => 15,
            "restapi" => 0,
        );

        /**
         * Define day list
         * @var array 
         */
        public static $day;

        /**
         * Define month list
         * @var array 
         */
        public static $month;

        /**
         * Define month list
         * @var string 
         */
        public $estDay;

        /**
         * Constructor
         */
        public function __construct() {
            global $rpesp_plugin_dir, $rpesp_plugin_url, $wpdb;

            /* plugin url and directory variable */
            self::$plugin_dir = $rpesp_plugin_dir;
            self::$plugin_url = $rpesp_plugin_url;

            self::$day = array(
                '0' => esc_html__("Sunday", self::$textdomain),
                '1' => esc_html__("Monday", self::$textdomain),
                '2' => esc_html__("Tuesday", self::$textdomain),
                '3' => esc_html__("Wednesday", self::$textdomain),
                '4' => esc_html__("Thursday", self::$textdomain),
                '5' => esc_html__("Friday", self::$textdomain),
                '6' => esc_html__("Saturday", self::$textdomain),
            );

            self::$month = array(
                '01' => esc_html__('January', self::$textdomain),
                '02' => esc_html__('February', self::$textdomain),
                '03' => esc_html__('March', self::$textdomain),
                '04' => esc_html__('April', self::$textdomain),
                '05' => esc_html__('May', self::$textdomain),
                '06' => esc_html__('June', self::$textdomain),
                '07' => esc_html__('July', self::$textdomain),
                '08' => esc_html__('August', self::$textdomain),
                '09' => esc_html__('September', self::$textdomain),
                '10' => esc_html__('October', self::$textdomain),
                '11' => esc_html__('November', self::$textdomain),
                '12' => esc_html__('December', self::$textdomain),
            );

            /* load plugin setting */
            if (empty($this->rpesp_settings)) {
                $this->rpesp_settings = get_option(self::$rpesp_option_key);
            }
        }

        /**
         * Function for save plugin general settings
         */
        public function saveSetting() {
            $saveData = array();

            if (isset($_POST[self::$textdomain])):

                update_option(self::$rpesp_option_key, $_POST);
                $this->rpesp_settings = $_POST;
            endif;
        }

        /**
         * Function for get plugin general settings
         * @return 	string 
         */
        public function getSetting($key) {

            if (!empty($this->rpesp_settings) && isset($this->rpesp_settings[$key])) {
                return $this->rpesp_settings[$key];
            }
            if (isset($this->default_settings[$key])) {
                return $this->default_settings[$key];
            }

            return false;
        }

        public function isEnable() {
            if ($this->getSetting('enable_delivery_date') == "1") {
                return true;
            }
            return false;
        }

        /**
         * Replace tags in date text
         * 
         * @param string $estDay
         * @param string $response
         * 
         * @return string
         */
        public function pregReplaceDate($estDay, $response) {

            $this->estDay = $estDay;
            $response = preg_replace_callback("/{(.*?)([+-])(.*?)}/", array($this, 'callbackPregReplace'), $response);
            return $response;
        }

        /**
         * Replace tags in date text for max product delivery time
         * 
         * @param string $estDay
         * @param string $response
         * 
         * @return string
         */
        public function pregReplaceMaxDate($estDay, $response) {

            $this->estDay = $estDay;
            $response = preg_replace_callback("/{(.*?)([+-])(.*?)}/", array($this, 'callbackPregReplaceMaxDate'), $response);
            return $response;
        }

        /**
         * Replace tags in date text for max product delivery time
         * 
         * @param string $estDay
         * @param string $response
         * 
         * @return string
         */
        public function pregReplaceMinDate($estDay, $response) {

            $this->estDay = $estDay;
            $response = preg_replace_callback("/{(.*?)([+-])(.*?)}/", array($this, 'callbackPregReplaceMinDate'), $response);
            return $response;
        }

        /**
         * Callback function for preg_replace_callback
         * 
         * @param array $matches
         * 
         * @return string
         */
        public function callbackPregReplace($matches) {
            if (!isset($matches[1]) || !isset($matches[2]) || !isset($matches[3])) {
                return $matches[0];
            }

            if (!in_array(trim($matches[1]), array('d', 'date'))) {
                return $matches[0];
            }

            if (!in_array(trim($matches[2]), array('+', '-'))) {
                return $matches[0];
            }


            if (is_numeric($matches[3])) {
                if (trim($matches[2]) == "-") {
                    $estDay = $this->estDay - (trim($matches[3]) * 86400);
                } else {
                    $estDay=$this->estDay;
                    for ($i = 1; $i <= trim($matches[3]); $i++) {
                        $estDay = $estDay + 86400;
                        $estDay = $this->isOffDay($estDay);
                    }
                    
                }
            } else {
                $estDay = $this->estDay;
            }


            $estDay = $this->isOffDay($estDay);

            if (trim($matches[1]) == "date") {
                return $this->getFormatedDate($estDay);
            } else {
                $numberOfDay = $this->getDateDiff($estDay);
                return $numberOfDay;
            }

            
        }

        /**
         * Function for check off days
         * 
         * @param string $estDay
         * 
         * @return string
         */
        public function isOffDay($estDay) {
            $blockDate = $this->getBlockDates();
            $blockWeekday = $this->getBlockWeekday();
            $weekDay = date('w', $estDay);
            if (in_array($estDay, $blockDate) || in_array($weekDay, $blockWeekday)) {
                $estDay = $estDay + 86400;
                $estDay = $this->isOffDay($estDay);
            }
            return $estDay;
        }

        /**
         * Function for get block weekdays
         *
         * @return array
         */
        public function getBlockWeekday() {
            $weekday_block = array();
            $weekday = $this->getSetting('weekdayoff');
            if ($weekday && !empty($weekday)) {
                foreach ($weekday as $bday):
                    $weekday_block[] = intval($bday);
                endforeach;
            }
            return $weekday_block;
        }

        /**
         * Function for get block dates
         * 
         * @return array
         */
        public function getBlockDates() {
            $specificBlockDate = $this->getSpecificBlockday();
            $periodBlockDate = $this->getPeriodBlockday();
            $blockDate = array_merge($specificBlockDate, $periodBlockDate);
            return $blockDate;
        }

        /**
         * Callback function for preg_replace_callback
         * 
         * @param array $matches
         * 
         * @return string
         */
        public function callbackPregReplaceMaxDate($matches) {
            if (!isset($matches[1]) || !isset($matches[2]) || !isset($matches[3])) {
                return $matches[0];
            }

            if (!in_array(trim($matches[1]), array('product_with_max_d', 'product_with_max_date'))) {
                return $matches[0];
            }

            if (!in_array(trim($matches[2]), array('+', '-'))) {
                return $matches[0];
            }


            if (is_numeric($matches[3])) {
                if (trim($matches[2]) == "-") {
                    $estDay = $this->estDay - (trim($matches[3]) * 86400);
                } else {
                    $estDay=$this->estDay;
                    for ($i = 1; $i <= trim($matches[3]); $i++) {
                        $estDay = $estDay + 86400;
                        $estDay = $this->isOffDay($estDay);
                    }
                    
                }
            } else {
                $estDay = $this->estDay;
            }


            $estDay = $this->isOffDay($estDay);

            if (trim($matches[1]) == "product_with_max_date") {
                return $this->getFormatedDate($estDay);
            } else {
                $numberOfDay = $this->getDateDiff($estDay);
                return $numberOfDay;
            }

            
        }

        /**
         * Get date difference
         * 
         * @param string $estDay
         * 
         * @return string
         */
        public function getDateDiff($estDay) {
            $date1 = date_create(date('Y-m-d'));
            $date2 = date_create(date('Y-m-d', $estDay));
            $diff = date_diff($date1, $date2);
            return $diff->format("%a");
        }

        /**
         * Callback function for preg_replace_callback
         * 
         * @param array $matches
         * 
         * @return string
         */
        public function callbackPregReplaceMinDate($matches) {
            if (!isset($matches[1]) || !isset($matches[2]) || !isset($matches[3])) {
                return $matches[0];
            }

            if (!in_array(trim($matches[1]), array('product_with_min_d', 'product_with_min_date'))) {
                return $matches[0];
            }

            if (!in_array(trim($matches[2]), array('+', '-'))) {
                return $matches[0];
            }


            if (is_numeric($matches[3])) {
                if (trim($matches[2]) == "-") {
                    $estDay = $this->estDay - (trim($matches[3]) * 86400);
                } else {
                    $estDay = $this->estDay;
                    for ($i = 1; $i <= trim($matches[3]); $i++) {
                        $estDay = $estDay + 86400;
                        $estDay = $this->isOffDay($estDay);
                    }
                }
            } else {
                $estDay = $this->estDay;
            }


            $estDay = $this->isOffDay($estDay);

            if (trim($matches[1]) == "product_with_min_date") {
                return $this->getFormatedDate($estDay);
            } else {
                $numberOfDay = $this->getDateDiff($estDay);
                return $numberOfDay;
            }

            
        }

        /**
         * Function for get specific block dates
         * 
         * @return array
         */
        public function getSpecificBlockday() {
            $specificdayBlock = array();
            
            $blockday = $this->getSetting('specific_day');

            if ($blockday && !empty($blockday['day']) && count($blockday['day']) > 0) {
                for ($i = 0; $i < count($blockday['day']); $i++):
                    if ($blockday['day'][$i] == '0') {
                        continue;
                    }
                    if ($blockday['month'][$i] == '0') {
                        continue;
                    }
                    if ($blockday['year'][$i] == '0') {
                        continue;
                    }
                    $year=($blockday['year'][$i]=="every")?date("Y"):$blockday['year'][$i];
                    $date = $year . "-" . $blockday['month'][$i] . "-" . $blockday['day'][$i];
                    $specificdayBlock[] = strtotime($date);
                endfor;
            }
            return $specificdayBlock;
        }

        /**
         * Function for get period block dates
         * @return array
         */
        public function getPeriodBlockday() {
            $blockDates = array();
            $blockPeriod = $this->getSetting('specific_period');
            if ($blockPeriod && !empty($blockPeriod['fday']) && count($blockPeriod['fday']) > 0) {
                for ($i = 0; $i < count($blockPeriod['fday']); $i++):
                    if ($blockPeriod['fday'][$i] == '0' || $blockPeriod['fmonth'][$i] == '0' || $blockPeriod['fyear'][$i] == '0' || $blockPeriod['tday'][$i] == '0' || $blockPeriod['tmonth'][$i] == '0' || $blockPeriod['tyear'][$i] == '0') {
                        continue;
                    }
                    $fyear=($blockPeriod['fyear'][$i]=="every")?date("Y"):$blockPeriod['fyear'][$i];
                    $tyear=($blockPeriod['tyear'][$i]=="every")?date("Y"):$blockPeriod['tyear'][$i];
                    $fromDate = $fyear . "-" . $blockPeriod['fmonth'][$i] . "-" . $blockPeriod['fday'][$i];
                    $toDate = $tyear . "-" . $blockPeriod['tmonth'][$i] . "-" . $blockPeriod['tday'][$i];
                    $fromTimeDate = strtotime($fromDate);
                    $toTimeDate = strtotime($toDate);
                    if ($fromTimeDate > $toTimeDate) {
                        continue;
                    }
                    while ($fromTimeDate <= $toTimeDate) {
                        $blockDates[] = $fromTimeDate;
                        $fromTimeDate = strtotime('+1 day', $fromTimeDate);
                    }
                endfor;
            }
            return $blockDates;
        }

        /**
         * Function for get icon html
         * @param string $key
         * @return string
         */
        public function getIconHtml($key) {
            $icon = $this->getSetting($key);
            $iconHtml = "";
            if ($icon != "" && is_numeric($icon)) {
                $imageData = wp_get_attachment_image_src($icon, 'full', true);
                if ($imageData && count($imageData) > 0) {
                    $iconHtml = '<span class="rp_icon"><img src="' . $imageData[0] . '" alt="" /></span>';
                }
            }
            return $iconHtml;
        }

        /**
         * Function for format date
         * @param int $time
         * @return string
         */
        public function getFormatedDate($time){
            $date=stripslashes($this->getSetting('date_format'));
            return date_i18n("$date", $time);
        }

    }

}
