<?php

/**
 * Class WOOMULTI_CURRENCY_Plugin_Paid_Memberships_Pro
 * Paid Memberships Pro
 * Author Paid Memberships Pro
 * This plugin has custom currency setting, need more wc filter to change display currency
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WOOMULTI_CURRENCY_Plugin_Paid_Memberships_Pro {
	protected $settings;

	public function __construct() {
		$this->settings = WOOMULTI_CURRENCY_Data::get_ins();
		add_filter( 'pmpro_format_price', array( $this, 'pmpro_format_price' ), 10, 4 );
	}

	public function pmpro_format_price( $formatted, $price, $pmpro_currency, $pmpro_currency_symbol ) {
		$r_price = wmc_get_price( $price );
//		$formatted = wc_price( $r_price, array( 'currency' => $this->settings->get_current_currency() ) );
		$formatted = $pmpro_currency_symbol . number_format( $r_price, pmpro_get_decimal_place() );;

		return $formatted;
	}
}