<?php
/**
 * File that define P24_Email_Enforcer class.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class that enforce emails.
 */
class P24_Email_Enforcer {

	/**
	 * The P24_Core instance.
	 *
	 * @var P24_Core
	 */
	private $plugin_core;

	/**
	 * The constructor.
	 *
	 * @param P24_Core $plugin_core The P24_Core instance.
	 */
	public function __construct( P24_Core $plugin_core ) {
		$this->plugin_core = $plugin_core;
	}

	/**
	 * Try to execute early emails.
	 *
	 * @param WC_Order $order An order that may have early mails sent.
	 * @return void
	 */
	public function try_execute_early_mails( WC_Order $order ) {
		$currency = $order->get_currency();
		$config   = $this->plugin_core->get_config_for_currency( $currency );
		$config->access_mode_to_strict();

		$emails = WC()->mailer()->emails;

		if ( $config->get_admin_mail_early_on_new_order() ) {
			$emails['WC_Email_New_Order']->trigger( $order->get_id(), $order );
		}

		if ( $config->get_client_mail_on_hold_at_pending() ) {
			$emails['WC_Email_Customer_On_Hold_Order']->trigger( $order->get_id(), $order );
		}
	}
}
