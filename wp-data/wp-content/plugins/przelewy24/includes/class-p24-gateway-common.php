<?php
/**
 * File that define P24_Gateway_Common class.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;

/**
 * Base class for gateways.
 */
class P24_Gateway_Common extends WC_Payment_Gateway {

	/**
	 * Check if method of provided id is valid for provided price.
	 *
	 * @param float  $price Price to check.
	 * @param string $id Id of payment method.
	 * @return bool
	 */
	protected static function is_valid_for_price_static( $price, $id ) {
		$price = round( $price, 2 );
		$id    = (string) $id;

		switch ( $id ) {
			case '72':
			case '129':
			case '136':
				return 300. <= $price && 10000. >= $price;
			case '227':
				return 10. <= $price && 3000. >= $price;
			case '266':
				return 0. < $price && 10000. >= $price;
			case '294':
				return 150. <= $price && 3000. >= $price;
			default:
				return 0. < $price;
		}
	}
}
