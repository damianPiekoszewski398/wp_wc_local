<?php
/**
 * File that define P24_Product_LP_Course class.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;

/**
 * The class fix support of multiple currencies in LearnPress.
 */
class P24_Product_LP_Course extends WC_Product_LP_Course implements P24_MC_Product_Common_Interface {

	/**
	 * Convert_price.
	 *
	 * @param mixed $price The base price in LearnPress base currency.
	 * @return mixed
	 */
	private function convert_price( $price ) {
		$multi_currency = get_przelewy24_plugin_instance()->get_multi_currency_instance();
		$from           = learn_press_get_currency();
		$to             = $this->get_currency();
		if ( ! $to ) {
			$to = $multi_currency->get_active_currency();
		}
		return $multi_currency->convert_price( $price, $from, $to );
	}

	/**
	 * Get_price.
	 *
	 * @param string|null $context The name of context.
	 * @return mixed
	 */
	public function get_price( $context = 'view' ) {
		$native = parent::get_price( $context );
		$public = $this->convert_price( $native );

		return $public;
	}

	/**
	 * Get currency of the object.
	 *
	 * @param string $context The name of context.
	 * @return string
	 */
	public function get_currency( $context = 'view' ) {
		return $this->get_prop( P24_Product_Keys::KEY_CURRENCY, $context );
	}

	/**
	 * Overwrite currency of product.
	 *
	 * @param string $new_currency New currency.
	 * @return void
	 */
	public function overwrite_currency( $new_currency ) {
		$this->set_prop( P24_Product_Keys::KEY_CURRENCY, $new_currency );
	}
}
