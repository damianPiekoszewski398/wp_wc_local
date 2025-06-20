<?php
/**
 * File that define P24_External_Multi_Currency class.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class with support for external multi currency.
 */
interface P24_MC_Product_Common_Interface {

	/**
	 * Get currency of the object.
	 *
	 * @param string $context The name of context.
	 * @return mixed
	 */
	public function get_currency( $context = 'view' );

	/**
	 * Overwrite currency of product.
	 *
	 * @param string $new_currency New currency.
	 * @return void
	 */
	public function overwrite_currency( $new_currency );
}
