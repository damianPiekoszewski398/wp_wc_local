<?php

if(!defined('ABSPATH')){
    exit;
}
if(!class_exists('Rpesp_Admin')){

    class Rpesp_Admin extends Rpesp_Main {

	/**
	 * columns for import export
	 * 
	 * @var array 
	 */
	public static $import_export_cols_comman = array();
	public static $import_export_cols_outofstock = array();
	public static $import_export_cols_backorder = array();

	public function __construct() {
	    /* call parent class construct */
	    parent::__construct();

	    self::$import_export_cols_comman = array(
		'deliverytime_inday'=>esc_html__('Delivery Time(In Day)',self::$textdomain),
		'carrier_deliverytime_inday'=>esc_html__('Delivery Time To Carrier (In Day)',self::$textdomain),
		'deliverytime_text'=>esc_html__('Delivery Time Text',self::$textdomain),
		'alt_deliverytime_text'=>esc_html__('Alternative Delivery Time Text',self::$textdomain),
		'deliverytime_order_text'=>esc_html__('Delivery Time Order Text',self::$textdomain),
	    );
	    self::$import_export_cols_outofstock = array(
		'deliverytime_inday_outofstock'=>esc_html__('Delivery Time(In Day) For Outofstock',self::$textdomain),
		'carrier_deliverytime_inday_outofstock'=>esc_html__('Delivery Time To Carrier(In Day) For Outofstock',self::$textdomain),
		'deliverytime_text_outofstock'=>esc_html__('Delivery Time Text For Outofstock',self::$textdomain),
	    );
	    self::$import_export_cols_backorder = array(
		'deliverytime_inday_backorder'=>esc_html__('Delivery Time(In Day) For Backorder',self::$textdomain),
		'carrier_deliverytime_inday_backorder'=>esc_html__('Delivery Time To Carrier(In Day) For Backorder',self::$textdomain),
		'deliverytime_text_backorder'=>esc_html__('Delivery Time Text For Backorder',self::$textdomain),
		'alt_deliverytime_text_backorder'=>esc_html__('Alternative Delivery Time Text For Backorder',self::$textdomain),
	    );

	    /* plugin admin menu hook */
	    add_action("admin_menu",array( $this,"adminMenu" ));

	    
		/* hook for register admin style and js */
		add_action("admin_enqueue_scripts",array( $this,"adminEnqueueScripts" ));
	    


	    if($this->isEnable() === true && ($this->getSetting('enable_for') == 0 || $this->getSetting('hide_product_setting') == 0)){
		$this->registerAdminHooks();
	    }
	}

	/**
	 * Function for register admin hooks
	 */
	public function registerAdminHooks() {

	    /* hook for add product delivery setting on product page */
	    add_filter('woocommerce_product_write_panel_tabs',array( $this,'addTabOnAdminProductPage' ));

	    /* hook for display setting on product page */
	    add_filter('woocommerce_product_data_panels',array( $this,'settingOnProductPage' ));

	    /* hook for save product meta */
	    add_action('woocommerce_process_product_meta',array( $this,'saveMeta' ),10,1);

	    /* hook for diplay delivery setting in variation screen */
	    add_action('woocommerce_product_after_variable_attributes',array( $this,'settingOnVariation' ),10,3);

	    /* hook for save delivery setting for variation */
	    add_action('woocommerce_save_product_variation',array( $this,'saveVariationSettings' ),10,1);

	    /* hook for display delivery setting on bulk edit screen */
	    add_action('woocommerce_product_bulk_edit_end',array( $this,'bulkEditSettings' ));

	    /* hook for save bulk edit settings */
	    add_action('woocommerce_product_bulk_edit_save',array( $this,'saveBulkEditSettings' ));

	    /* hook for get value for quick edit fields */
	    add_action('manage_product_posts_custom_column',array( $this,'quickEditValues' ));

	    /* hook for display setting on quick edit screen */
	    add_action('woocommerce_product_quick_edit_end',array( $this,'quickEditSettings' ));

	    /* hook for save quick edit settings */
	    add_action('woocommerce_product_quick_edit_save',array( $this,'saveQuickEditSettings' ));

	    /* hook for add export columns */
	    add_filter('woocommerce_product_export_column_names',array( $this,'exportColumn' ));

	    /* hook for add export columns */
	    add_filter('woocommerce_product_export_product_default_columns',array( $this,'exportColumn' ));
	    
	    

	    /* hook for add export data */
	    foreach(self::$import_export_cols_comman as $key=> $value):
		if($key == "carrier_deliverytime_inday" && !$this->getSetting("enable_carrier")){
		    continue;
		}
		add_filter('woocommerce_product_export_product_column_' . $key,array( $this,'addExportData' ),10,3);
	    endforeach;

	    if(!$this->getSetting("hide_out_of_stock")):
		/* hook for add export data */
		foreach(self::$import_export_cols_outofstock as $key=> $value):
		    if($key == "carrier_deliverytime_inday_outofstock" && !$this->getSetting("enable_carrier")){
			continue;
		    }
		    add_filter('woocommerce_product_export_product_column_' . $key,array( $this,'addExportData' ),10,3);
		endforeach;
	    endif;

	    /* hook for add export data */
	    if(!$this->getSetting("hide_backorder")):
		foreach(self::$import_export_cols_backorder as $key=> $value):
		    if($key == "carrier_deliverytime_inday_backorder" && !$this->getSetting("enable_carrier")){
			continue;
		    }
		    add_filter('woocommerce_product_export_product_column_' . $key,array( $this,'addExportData' ),10,3);
		endforeach;
	    endif;


	    /* hook for add import columns  */
	    add_filter('woocommerce_csv_product_import_mapping_options',array( $this,'addColumnToImporter' ));

	    /* hook for add columns to import mapping screen  */
	    add_filter('woocommerce_csv_product_import_mapping_default_columns',array( $this,'addColumnToMappingScreen' ));

	    /* hook for import data  */
	    add_filter('woocommerce_product_import_pre_insert_product_object',array( $this,'processImport' ),10,2);
	}
	
	
	

	/**
	 * Function for add setting tab on woocommerce product edit page
	 */
	public function addTabOnAdminProductPage() {
	    $style = '';
	    echo '<li class="rpesp_tab rpestimation_options"><a href="#rpesp_product_data" ' . $style . '>  ' . esc_html__(self::$plugin_title,self::$textdomain) . '</a></li>';
	}

	/**
	 * Function for display setting on product page
	 */
	public function settingOnProductPage() {
	    global $thepostid,$post;

	    if(!$thepostid){
		$thepostid = $post->ID;
	    }

	    /* include product setting file */
	    include self::$plugin_dir . "view/admin/product/settings.php";
	}

	/**
	 * Function for save product meta
	 * 
	 * @param int $postid
	 */
	public function saveMeta($postid) {
	    $saveMeta = array();
	    if(isset($_POST['esttime'])){
		$saveMeta['esttime'] = $_POST['esttime'];
	    }
	    if(isset($_POST['carrier_esttime'])){
		$saveMeta['carrier_esttime'] = $_POST['carrier_esttime'];
	    }
	    if(isset($_POST['esttext'])){
		$saveMeta['esttext'] = $_POST['esttext'];
	    }
	    if(isset($_POST['esttext'])){
		$saveMeta['alt_esttext'] = $_POST['alt_esttext'];
	    }
	    if(isset($_POST['esttext_orderpage'])){
		$saveMeta['esttext_orderpage'] = $_POST['esttext_orderpage'];
	    }
	    if(isset($_POST['esttime_outofstock'])){
		$saveMeta['esttime_outofstock'] = $_POST['esttime_outofstock'];
	    }
	    if(isset($_POST['carrier_esttime_outofstock'])){
		$saveMeta['carrier_esttime_outofstock'] = $_POST['carrier_esttime_outofstock'];
	    }
	    if(isset($_POST['esttext_outofstock'])){
		$saveMeta['esttext_outofstock'] = $_POST['esttext_outofstock'];
	    }
	    if(isset($_POST['esttime_backorder'])){
		$saveMeta['esttime_backorder'] = $_POST['esttime_backorder'];
	    }
	    if(isset($_POST['carrier_esttime_backorder'])){
		$saveMeta['carrier_esttime_backorder'] = $_POST['carrier_esttime_backorder'];
	    }
	    if(isset($_POST['esttext_backorder'])){
		$saveMeta['esttext_backorder'] = $_POST['esttext_backorder'];
	    }
	    if(isset($_POST['alt_esttext_backorder'])){
		$saveMeta['alt_esttext_backorder'] = $_POST['alt_esttext_backorder'];
	    }
	    if(!empty($saveMeta)){
		update_post_meta($postid,self::$meta_key,$saveMeta);
	    }
	}

	/**
	 * Function for display variation meta
	 * 
	 * @param array $loop
	 * @param array $variation_data
	 * @param array $variation
	 */
	public function settingOnVariation($loop,$variation_data,$variation) {
	    /* include product varition setting file */
	    include self::$plugin_dir . 'view/admin/product/variation/settings.php';
	}

	/**
	 * Function for save variation meta
	 * 
	 * @param int $postid
	 */
	public function saveVariationSettings($postid) {
	    $saveMeta = array();
	    if(isset($_POST['esttime_shipping']) && isset($_POST['esttime_shipping'][$postid])){
		$saveMeta['esttime'] = $_POST['esttime_shipping'][$postid];
	    }
	    if(isset($_POST['carrier_esttime_shipping']) && isset($_POST['carrier_esttime_shipping'][$postid])){
		$saveMeta['carrier_esttime'] = $_POST['carrier_esttime_shipping'][$postid];
	    }
	    if(isset($_POST['esttext_shipping']) && isset($_POST['esttext_shipping'][$postid])){
		$saveMeta['esttext'] = $_POST['esttext_shipping'][$postid];
	    }
	    if(isset($_POST['alt_esttext_shipping']) && isset($_POST['alt_esttext_shipping'][$postid])){
		$saveMeta['alt_esttext'] = $_POST['alt_esttext_shipping'][$postid];
	    }
	    if(isset($_POST['esttext_orderpage_shipping']) && isset($_POST['esttext_orderpage_shipping'][$postid])){
		$saveMeta['esttext_orderpage'] = $_POST['esttext_orderpage_shipping'][$postid];
	    }
	    if(isset($_POST['esttime_outofstock_shipping']) && isset($_POST['esttime_outofstock_shipping'][$postid])){
		$saveMeta['esttime_outofstock'] = $_POST['esttime_outofstock_shipping'][$postid];
	    }
	    if(isset($_POST['carrier_esttime_outofstock_shipping']) && isset($_POST['carrier_esttime_outofstock_shipping'][$postid])){
		$saveMeta['carrier_esttime_outofstock'] = $_POST['carrier_esttime_outofstock_shipping'][$postid];
	    }
	    if(isset($_POST['esttext_outofstock_shipping']) && isset($_POST['esttext_outofstock_shipping'][$postid])){
		$saveMeta['esttext_outofstock'] = $_POST['esttext_outofstock_shipping'][$postid];
	    }
	    if(isset($_POST['esttime_backorder_shipping']) && isset($_POST['esttime_backorder_shipping'][$postid])){
		$saveMeta['esttime_backorder'] = $_POST['esttime_backorder_shipping'][$postid];
	    }
	    if(isset($_POST['carrier_esttime_backorder_shipping']) && isset($_POST['carrier_esttime_backorder_shipping'][$postid])){
		$saveMeta['carrier_esttime_backorder'] = $_POST['carrier_esttime_backorder_shipping'][$postid];
	    }
	    if(isset($_POST['esttext_backorder_shipping']) && isset($_POST['esttext_backorder_shipping'][$postid])){
		$saveMeta['esttext_backorder'] = $_POST['esttext_backorder_shipping'][$postid];
	    }
	    if(isset($_POST['alt_esttext_backorder_shipping']) && isset($_POST['alt_esttext_backorder_shipping'][$postid])){
		$saveMeta['alt_esttext_backorder'] = $_POST['alt_esttext_backorder_shipping'][$postid];
	    }

	    if(!empty($saveMeta)){
		update_post_meta($postid,self::$meta_key,$saveMeta);
	    }
	}

	/**
	 * Function for display bulk edit settings
	 */
	public function bulkEditSettings() {
	    include self::$plugin_dir . "view/admin/product/bulkedit/settings.php";
	}

	/**
	 * Save bulk edit settings
	 * 
	 * @param array $product
	 */
	public function saveBulkEditSettings($product) {
	    $product_id = $product->id;
	    if($product_id > 0){
		$saveMeta = get_post_meta($product_id,self::$meta_key,true);
		if(empty($saveMeta) || !is_array($saveMeta)){
		    $saveMeta = array();
		}
		if(isset($_REQUEST['est_delivery_time']) && !empty($_REQUEST['est_delivery_time'])){
		    $saveMeta['esttime'] = $_REQUEST['est_delivery_time'];
		}
		if(isset($_REQUEST['carrier_est_delivery_time']) && !empty($_REQUEST['carrier_est_delivery_time'])){
		    $saveMeta['carrier_esttime'] = $_REQUEST['carrier_est_delivery_time'];
		}
		if(isset($_REQUEST['est_delivery_text']) && !empty($_REQUEST['est_delivery_text'])){
		    $saveMeta['esttext'] = $_REQUEST['est_delivery_text'];
		}
		if(isset($_REQUEST['alt_est_delivery_text']) && !empty($_REQUEST['alt_est_delivery_text'])){
		    $saveMeta['alt_esttext'] = $_REQUEST['alt_est_delivery_text'];
		}
		if(isset($_REQUEST['est_order_delivery_text']) && !empty($_REQUEST['est_order_delivery_text'])){
		    $saveMeta['esttext_orderpage'] = $_REQUEST['est_order_delivery_text'];
		}
		if(isset($_REQUEST['est_delivery_time_outofstock']) && !empty($_REQUEST['est_delivery_time_outofstock'])){
		    $saveMeta['esttime_outofstock'] = $_REQUEST['est_delivery_time_outofstock'];
		}
		if(isset($_REQUEST['carrier_est_delivery_time_outofstock']) && !empty($_REQUEST['carrier_est_delivery_time_outofstock'])){
		    $saveMeta['carrier_esttime_outofstock'] = $_REQUEST['carrier_est_delivery_time_outofstock'];
		}
		if(isset($_REQUEST['est_delivery_text_outofstock']) && !empty($_REQUEST['est_delivery_text_outofstock'])){
		    $saveMeta['esttext_outofstock'] = $_REQUEST['est_delivery_text_outofstock'];
		}
		if(isset($_REQUEST['est_delivery_time_backorder']) && !empty($_REQUEST['est_delivery_time_backorder'])){
		    $saveMeta['esttime_backorder'] = $_REQUEST['est_delivery_time_backorder'];
		}
		if(isset($_REQUEST['carrier_est_delivery_time_backorder']) && !empty($_REQUEST['carrier_est_delivery_time_backorder'])){
		    $saveMeta['carrier_esttime_backorder'] = $_REQUEST['carrier_est_delivery_time_backorder'];
		}
		if(isset($_REQUEST['est_delivery_text_backorder']) && !empty($_REQUEST['est_delivery_text_backorder'])){
		    $saveMeta['esttext_backorder'] = $_REQUEST['est_delivery_text_backorder'];
		}
		if(isset($_REQUEST['alt_est_delivery_text_backorder']) && !empty($_REQUEST['alt_est_delivery_text_backorder'])){
		    $saveMeta['alt_esttext_backorder'] = $_REQUEST['alt_est_delivery_text_backorder'];
		}
		update_post_meta($product_id,self::$meta_key,$saveMeta);
	    }
	}

	/**
	 * Function for get value for quick edit fields
	 * 
	 * @global array $post
	 * @param string $column
	 */
	public function quickEditValues($column) {
	    global $post;

	    $productId = $post->ID;
	    $estMeta = get_post_meta($productId,self::$meta_key,true);
	    if($column == 'name'){
		include self::$plugin_dir . "view/admin/product/quickedit/values.php";
	    }
	}

	/**
	 * Display quick edit settings
	 */
	public function quickEditSettings() {
	    include self::$plugin_dir . "view/admin/product/quickedit/settings.php";
	}

	/**
	 * Save quick edit settings
	 * 
	 * @param array $product
	 */
	public function saveQuickEditSettings($product) {
	    $productId = $product->id;

	    if($productId > 0){
		$saveMeta = array();
		if(isset($_REQUEST['est_delivery_time'])){
		    $saveMeta['esttime'] = $_POST['est_delivery_time'];
		}
		if(isset($_REQUEST['est_delivery_text'])){
		    $saveMeta['esttext'] = $_POST['est_delivery_text'];
		}
		if(isset($_REQUEST['alt_est_delivery_text'])){
		    $saveMeta['alt_esttext'] = $_POST['alt_est_delivery_text'];
		}
		if(isset($_REQUEST['est_order_delivery_text'])){
		    $saveMeta['esttext_orderpage'] = $_POST['est_order_delivery_text'];
		}
		if(isset($_POST['carrier_est_delivery_time'])){
		    $saveMeta['carrier_esttime'] = $_POST['carrier_est_delivery_time'];
		}
		if(isset($_POST['est_delivery_time_outofstock'])){
		    $saveMeta['esttime_outofstock'] = $_POST['est_delivery_time_outofstock'];
		}
		if(isset($_POST['carrier_est_delivery_time_outofstock'])){
		    $saveMeta['carrier_esttime_outofstock'] = $_POST['carrier_est_delivery_time_outofstock'];
		}

		if(isset($_POST['est_delivery_text_outofstock'])){
		    $saveMeta['esttext_outofstock'] = $_POST['est_delivery_text_outofstock'];
		}
		if(isset($_POST['est_delivery_time_backorder'])){
		    $saveMeta['esttime_backorder'] = $_POST['est_delivery_time_backorder'];
		}
		if(isset($_POST['carrier_est_delivery_time_backorder'])){
		    $saveMeta['carrier_esttime_backorder'] = $_POST['carrier_est_delivery_time_backorder'];
		}
		if(isset($_POST['est_delivery_text_backorder'])){
		    $saveMeta['esttext_backorder'] = $_POST['est_delivery_text_backorder'];
		}
		if(isset($_POST['alt_est_delivery_text_backorder'])){
		    $saveMeta['alt_esttext_backorder'] = $_POST['alt_est_delivery_text_backorder'];
		}
		update_post_meta($productId,self::$meta_key,$saveMeta);
	    }
	}

	/**
	 * Function for add columns to export
	 * 
	 * @param array $columns
	 * 
	 * @return array
	 */
	public function exportColumn($columns) {

	    foreach(self::$import_export_cols_comman as $col=> $text):
		if($col == "carrier_deliverytime_inday" && !$this->getSetting("enable_carrier")){
		    continue;
		}
		$columns[$col] = $text;
	    endforeach;

	    if(!$this->getSetting("hide_out_of_stock")):

		foreach(self::$import_export_cols_outofstock as $col=> $text):
		    if($col == "carrier_deliverytime_inday_outofstock" && !$this->getSetting("enable_carrier")){
			continue;
		    }
		    $columns[$col] = $text;
		endforeach;
	    endif;


	    if(!$this->getSetting("hide_backorder")):
		foreach(self::$import_export_cols_backorder as $col=> $text):
		    if($col == "carrier_deliverytime_inday_backorder" && !$this->getSetting("enable_carrier")){
			continue;
		    }
		    $columns[$col] = $text;
		endforeach;
	    endif;

	    return $columns;
	}

	/**
	 * Function for add export data
	 * 
	 * @param string $value
	 * @param object $product
	 * @param string $key
	 * 
	 * @return string
	 */
	public function addExportData($value,$product,$key) {
	    $productMeta = $product->get_meta(self::$meta_key,true,'edit');
	    if(!empty($productMeta)){
		switch( $key ) {
		    case 'deliverytime_inday':
			$value = isset($productMeta['esttime']) ? $productMeta['esttime'] : '';
			break;
		    case 'carrier_deliverytime_inday':
			$value = isset($productMeta['carrier_esttime']) ? $productMeta['carrier_esttime'] : '';
			break;
		    case 'deliverytime_inday_outofstock':
			$value = isset($productMeta['esttime_outofstock']) ? $productMeta['esttime_outofstock'] : '';
			break;
		    case 'carrier_deliverytime_inday_outofstock':
			$value = isset($productMeta['carrier_esttime_outofstock']) ? $productMeta['carrier_esttime_outofstock'] : '';
			break;
		    case 'deliverytime_inday_backorder':
			$value = isset($productMeta['esttime_backorder']) ? $productMeta['esttime_backorder'] : '';
			break;
		    case 'carrier_deliverytime_inday_backorder':
			$value = isset($productMeta['carrier_esttime_backorder']) ? $productMeta['carrier_esttime_backorder'] : '';
			break;
		    case 'deliverytime_text':
			$value = isset($productMeta['esttext']) ? $productMeta['esttext'] : '';
			break;
		    case 'alt_deliverytime_text':
			$value = isset($productMeta['alt_esttext']) ? $productMeta['alt_esttext'] : '';
			break;
		    case 'deliverytime_text_outofstock':
			$value = isset($productMeta['esttext_outofstock']) ? $productMeta['esttext_outofstock'] : '';
			break;
		    case 'deliverytime_text_backorder':
			$value = isset($productMeta['esttext_backorder']) ? $productMeta['esttext_backorder'] : '';
			break;
		    case 'alt_deliverytime_text_backorder':
			$value = isset($productMeta['alt_esttext_backorder']) ? $productMeta['alt_esttext_backorder'] : '';
			break;
		    case 'deliverytime_order_text':
			$value = isset($productMeta['esttext_orderpage']) ? $productMeta['esttext_orderpage'] : '';
			break;
		}
	    }
	    return $value;
	}

	/**
	 * Function for add columns to import
	 * 
	 * @param array $columns
	 * 
	 * @return array
	 */
	public function addColumnToImporter($columns) {

	    foreach(self::$import_export_cols_comman as $col=> $text):
		if($col == "carrier_deliverytime_inday" && !$this->getSetting("enable_carrier")){
		    continue;
		}
		$columns[$col] = $text;
	    endforeach;

	    if(!$this->getSetting("hide_out_of_stock")):

		foreach(self::$import_export_cols_outofstock as $col=> $text):
		    if($col == "carrier_deliverytime_inday_outofstock" && !$this->getSetting("enable_carrier")){
			continue;
		    }
		    $columns[$col] = $text;
		endforeach;
	    endif;


	    if(!$this->getSetting("hide_backorder")):
		foreach(self::$import_export_cols_backorder as $col=> $text):
		    if($col == "carrier_deliverytime_inday_backorder" && !$this->getSetting("enable_carrier")){
			continue;
		    }
		    $columns[$col] = $text;
		endforeach;
	    endif;


	    return $columns;
	}

	/**
	 * Function for add columns to import mapping screen
	 * 
	 * @param array $columns
	 * 
	 * @return array
	 */
	public function addColumnToMappingScreen($columns) {

	    foreach(self::$import_export_cols_comman as $col=> $text):
		if($col == "carrier_deliverytime_inday" && !$this->getSetting("enable_carrier")){
		    continue;
		}
		$columns[$text] = $col;
	    endforeach;

	    if(!$this->getSetting("hide_out_of_stock")):

		foreach(self::$import_export_cols_outofstock as $col=> $text):
		    if($col == "carrier_deliverytime_inday_outofstock" && !$this->getSetting("enable_carrier")){
			continue;
		    }
		    $columns[$text] = $col;
		endforeach;
	    endif;


	    if(!$this->getSetting("hide_backorder")):
		foreach(self::$import_export_cols_backorder as $col=> $text):
		    if($col == "carrier_deliverytime_inday_backorder" && !$this->getSetting("enable_carrier")){
			continue;
		    }
		    $columns[$text] = $col;
		endforeach;
	    endif;



	    return $columns;
	}

	/**
	 * Save import data
	 * 
	 * @param object $object
	 * @param array $data
	 * 
	 * @return object
	 */
	public function processImport($object,$data) {

	    $metaArray = $object->get_meta(self::$meta_key,true,'edit');
	    if(empty($metaArray) || !is_array($metaArray)){
		$metaArray = array();
	    }
	    if(isset($data['deliverytime_inday']) && !empty($data['deliverytime_inday']) && is_numeric($data['deliverytime_inday'])){
		$metaArray['esttime'] = $data['deliverytime_inday'];
	    }
	    if(isset($data['carrier_deliverytime_inday']) && !empty($data['carrier_deliverytime_inday']) && is_numeric($data['carrier_deliverytime_inday'])){
		$metaArray['carrier_esttime'] = $data['carrier_deliverytime_inday'];
	    }
	    if(isset($data['deliverytime_inday_outofstock']) && !empty($data['deliverytime_inday_outofstock']) && is_numeric($data['deliverytime_inday_outofstock'])){
		$metaArray['esttime_outofstock'] = $data['deliverytime_inday_outofstock'];
	    }
	    if(isset($data['carrier_deliverytime_inday_outofstock']) && !empty($data['carrier_deliverytime_inday_outofstock']) && is_numeric($data['carrier_deliverytime_inday_outofstock'])){
		$metaArray['carrier_esttime_outofstock'] = $data['carrier_deliverytime_inday_outofstock'];
	    }
	    if(isset($data['deliverytime_inday_backorder']) && !empty($data['deliverytime_inday_backorder']) && is_numeric($data['deliverytime_inday_backorder'])){
		$metaArray['esttime_backorder'] = $data['deliverytime_inday_backorder'];
	    }
	    if(isset($data['carrier_deliverytime_inday_backorder']) && !empty($data['carrier_deliverytime_inday_backorder']) && is_numeric($data['carrier_deliverytime_inday_backorder'])){
		$metaArray['carrier_esttime_backorder'] = $data['carrier_deliverytime_inday_backorder'];
	    }
	    if(isset($data['deliverytime_text']) && !empty($data['deliverytime_text'])){
		$metaArray['esttext'] = $data['deliverytime_text'];
	    }
	    if(isset($data['alt_deliverytime_text']) && !empty($data['alt_deliverytime_text'])){
		$metaArray['alt_esttext'] = $data['alt_deliverytime_text'];
	    }
	    if(isset($data['deliverytime_text_outofstock']) && !empty($data['deliverytime_text_outofstock'])){
		$metaArray['esttext_outofstock'] = $data['deliverytime_text_outofstock'];
	    }
	    if(isset($data['deliverytime_text_backorder']) && !empty($data['deliverytime_text_backorder'])){
		$metaArray['esttext_backorder'] = $data['deliverytime_text_backorder'];
	    }
	    if(isset($data['alt_deliverytime_text_backorder']) && !empty($data['alt_deliverytime_text_backorder'])){
		$metaArray['alt_esttext_backorder'] = $data['alt_deliverytime_text_backorder'];
	    }
	    if(isset($data['deliverytime_order_text']) && !empty($data['deliverytime_order_text'])){
		$metaArray['esttext_orderpage'] = $data['deliverytime_order_text'];
	    }
	    if(!empty($metaArray)){
		$object->update_meta_data(self::$meta_key,$metaArray);
	    }

	    return $object;
	}

	/**
	 * Function for register admin js and css
	 */
	public function adminEnqueueScripts() {
	    wp_enqueue_media();
	    wp_enqueue_script('jquery');
	    wp_enqueue_style('wp-color-picker');
	    wp_enqueue_script('rpesp-admin',self::$plugin_url . "assets/js/admin.js",array( 'wp-color-picker' ));
	    wp_enqueue_style('rpesp-admin',self::$plugin_url . "assets/css/admin.css");
	}

	/**
	 * Function for admin menu
	 */
	public function adminMenu() {
	    $wc_page = 'woocommerce';
	    add_submenu_page($wc_page,esc_html__(self::$plugin_title,self::$textdomain),esc_html__(self::$plugin_title,self::$textdomain),"install_plugins",self::$plugin_slug,array( $this,"settingPage" ));
	}

	/**
	 * For get image input
	 * @param string $name
	 * @param string $buttonText
	 * @param string $id
	 * @param string $value
	 */
	public function getImageInput($name,$buttonText,$id,$value) {
	    include self::$plugin_dir . 'view/admin/imageinput.php';
	}

	/**
	 * Plugin general setting page
	 */
	public function settingPage() {
	    /* save plugin setting */
	    $settingSaves = false;
	    if(isset($_POST[self::$textdomain])){
		$this->saveSetting();
		$settingSaves = true;
	    }

	    /* include setting file */
	    include_once self::$plugin_dir . "view/admin/settings.php";
	}

    }

}
