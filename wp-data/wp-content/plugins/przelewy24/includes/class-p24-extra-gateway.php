<?php
/**
 * File that define P24_Extra_Gateway class.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;


/**
 * The class for extra gateway.
 */
class P24_Extra_Gateway extends P24_Gateway_Common {

	/**
	 * Internal id.
	 *
	 * @var string
	 */
	private $internal_id;

	/**
	 * The id of BLIK method on Przelewy24 page.
	 */
	const BLIK_METHOD = '181';

	/**
	 * The id numbers of card method on Przelewy24 page.
	 */
	const CARD_METHODS = array( '218', '241', '242' );

	/**
	 * The name of form field on checkout page.
	 */
	const BLIK_CODE_INPUT_NAME = 'p24-blik-code';

	/**
	 * The key the blik code is stored in the database.
	 */
	const BLIK_CODE_META_KEY = '_p24_blik_code';

	/**
	 * The name of form field on checkout page.
	 */
	const CARD_COMBINED_INPUT_FIELD_NAME = 'p24-card-combined-field';

	/**
	 * The key the blik code is stored in the database.
	 */
	const CARD_COMBINED_FIELD_META_KEY = '_p24_card_combined_field';

	/**
	 * Generator.
	 *
	 * @var Przelewy24Generator
	 */
	private $generator;

	/**
	 * Main_gateway.
	 *
	 * @var WC_Gateway_Przelewy24
	 */
	private $main_gateway;

	/**
	 * P24_Extra_Gateway constructor.
	 *
	 * @param string                $id Id of payment method.
	 * @param string                $title Title of payment method.
	 * @param Przelewy24Generator   $generator Przelewy24 generator.
	 * @param WC_Gateway_Przelewy24 $main_gateway Main Prelewy24 payment gateway.
	 * @param string                $icon Icon url.
	 */
	public function __construct( $id, $title, $generator, $main_gateway, $icon ) {
		$this->internal_id  = (string) $id;
		$this->id           = WC_Gateway_Przelewy24::PAYMENT_METHOD . '_extra_' . $id;
		$this->generator    = $generator;
		$this->main_gateway = $main_gateway;
		$this->icon         = $icon;
		$this->title        = (string) $title;

		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'print_receipt' ) );
	}

	/**
	 * Get title.
	 *
	 * @return mixed
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Aditional conntent on print receipt page.
	 *
	 * @param int $order_id If of order.
	 */
	public function print_receipt( $order_id ) {
		$order_id = (int) $order_id;
		$order    = new WC_Order( (int) $order_id );

		$settings_accessor = $this->main_gateway->get_settings_from_internal_formatted( $order->get_currency() );
		$hasher            = new P24_Hasher( $settings_accessor );
		$hashed_order_id   = $hasher->hash( $order_id );

		$is_blik = self::BLIK_METHOD === $this->internal_id;
		if ( $is_blik ) {
			$blik_code = $order->get_meta( self::BLIK_CODE_META_KEY );
		} else {
			$blik_code = false;
		}
		if ( $blik_code ) {
			$legacy_auto_submit = false;
		} else {
			$legacy_auto_submit = true;
		}

		$ajax_url = add_query_arg( array( 'wc-api' => 'wc_gateway_przelewy24' ), home_url( '/' ) );
		echo $this->generator->generate_przelewy24_form( $order, $legacy_auto_submit ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo "<div id='p24-additional-order-data' data-order-id='$order_id' data-hashed-order-id='$hashed_order_id' data-ajax-url='$ajax_url'></div>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $blik_code ) {
			echo P24_Blik_Html::get_modal_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( in_array( $this->internal_id, self::CARD_METHODS, true ) ) {
			$combined_card_data = $order->get_meta( self::CARD_COMBINED_FIELD_META_KEY );
			echo "<div id='p24-combined-cart-data' data-combined='$combined_card_data'></div>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id Id of orer.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );
		/* This is the default place to reduce stock levels. It is safe to call function below multiple times. */
		wc_maybe_reduce_stock_levels( $order );
		wp_verify_nonce( null ); /* There is no nonce in request. */
		$save_meta = false;
		if ( isset( $_POST[ self::BLIK_CODE_INPUT_NAME ] ) ) {
			$blik_code = sanitize_text_field( wp_unslash( $_POST[ self::BLIK_CODE_INPUT_NAME ] ) );
			$order->update_meta_data( self::BLIK_CODE_META_KEY, $blik_code );
			$save_meta = true;
		}
		if ( isset( $_POST[ self::CARD_COMBINED_INPUT_FIELD_NAME ] ) ) {
			$combined_data = sanitize_text_field( wp_unslash( $_POST[ self::CARD_COMBINED_INPUT_FIELD_NAME ] ) );
			$order->update_meta_data( self::CARD_COMBINED_FIELD_META_KEY, $combined_data );
			$save_meta = true;
		}
		if ( $save_meta ) {
			$order->update_meta_data( P24_Core::CHOSEN_TIMESTAMP_META_KEY, time() );
			$order->update_meta_data( P24_Core::P24_METHOD_META_KEY, $this->internal_id );
			$order->save_meta_data();
		}

		$core           = $this->main_gateway->get_core();
		$email_enforcer = $core->get_email_enforcer();
		$email_enforcer->try_execute_early_mails( $order );

		do_action( 'wc_extra_gateway_przelewy24_process_payment', $order );

		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_payment_url( $order ),
		);
	}

	/**
	 * Check if gateway is valid for provided price.
	 *
	 * @param float $price Price to check.
	 * @return bool
	 */
	public function is_valid_for_price( $price ) {
		return self::is_valid_for_price_static( $price, $this->internal_id );
	}
}
