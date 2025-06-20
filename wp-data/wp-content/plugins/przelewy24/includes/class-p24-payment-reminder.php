<?php
/**
 * File that define P24_Payment_Reminder.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;


/**
 * Class P24_Payment_Reminder
 */
class P24_Payment_Reminder {

	/**
	 * Plugin core.
	 *
	 * @var P24_Core The plugin core.
	 */
	private $core;

	/**
	 * P24_Blik_Html constructor.
	 *
	 * @param P24_Core $core The plugin core.
	 */
	public function __construct( $core ) {
		$this->core = $core;
		add_action( 'woocommerce_checkout_order_created', array( $this, 'on_creation' ), 20, 2 );
		add_action( 'p24_hook_execute_payment_reminder', array( $this, 'try_send_mail' ), 10, 1 );
	}

	/**
	 * Mark order as expecting payment.
	 *
	 * @param WC_Order $order The order.
	 */
	public function on_creation( $order ) {
		if ( ! $this->core->check_if_from_przelewy24( $order ) ) {
			/* Nothing to do. */
			return;
		}

		$order_currency = $order->get_currency();
		$config         = $this->core->get_config_for_currency( $order_currency );
		$config->access_mode_to_strict();

		if ( $config->get_send_payment_reminder() ) {
			self::schedule_event( $order, $config->get_payment_reminder_delay() );
		}
	}

	/**
	 * Try send an e-mail.
	 *
	 * @param int $order_id The order id.
	 * @return void
	 */
	public function try_send_mail( $order_id ) {
		$order = new WC_Order( $order_id );

		if ( ! $this->core->check_if_from_przelewy24( $order ) ) {
			/* Nothing to do. */
			return;
		} elseif ( $order->is_paid() ) {
			/* Nothing to do. */
			return;
		} elseif ( ! $order->needs_payment() ) {
			/* Nothing to do. */
			return;
		}

		woocommerce_p24_email_send_order_payment_reminder( $order );
	}

	/**
	 * Shedule event.
	 *
	 * @param WC_Order $order The order.
	 * @param int      $minutes Minutes of deley.
	 * @return void
	 */
	private static function schedule_event( $order, $minutes ) {
		$in_15_minutes = time() + 60 * $minutes;
		/* Yes, a static call as a string. An array is not supported by the WordPress. */
		wp_schedule_single_event( $in_15_minutes, 'p24_hook_execute_payment_reminder', array( $order->get_id() ) );
	}
}
