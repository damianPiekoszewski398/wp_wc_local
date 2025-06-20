<?php
/**
 * File that define P24_Check_Sums class.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;

/**
 * Helper to dectect HPOS.
 */
class P24_Hpos_Helper {

	/**
	 * Check if internal multi currency is active.
	 *
	 * @return bool
	 */
	private static function check_multi_currency() {
		$common_options = get_option( P24_Settings_Helper::OPTION_KEY_COMMON, array() );
		$mc             = $common_options['p24_multi_currency'] ?? 'no';

		return 'yes' === $mc;
	}

	/**
	 * Check if HPOS is possible to be activated.
	 *
	 * @return bool
	 */
	public static function check_if_possible() {
		if ( P24_Subscription_Config::is_active() ) {
			return false;
		}
		if ( self::check_multi_currency() ) {
			return false;
		}
		if ( P24_Status_Decorator::is_active() ) {
			return false;
		}

		return true;
	}
}
