<?php
/**
 * File that define P24_Card_Html.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;


/**
 * Class P24_Card_Html
 */
class P24_Card_Html {

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
		add_action( 'woocommerce_checkout_after_order_review', array( $this, 'extend_checkout_page_form' ) );
		add_action( 'woocommerce_after_checkout_form', array( $this, 'extend_checkout_page_bottom' ) );
	}

	/**
	 * Get additional HTML code for checkout form.
	 */
	public function extend_checkout_page_form() {
		echo '<input type="hidden" id="p24-card-combined-field" name="p24-card-combined-field">';
	}

	/**
	 * Extend_checkout_page_bottom.
	 *
	 * Check and embed code for modal if needed on checkout page.
	 */
	public function extend_checkout_page_bottom() {
		$config = $this->core->get_config_for_currency();
		$config->access_mode_to_strict();
		if ( $config->get_p24_payinshop() ) {
			$my_account_link = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
			echo '<span id="p24-link-to-my-account" data-link="' . esc_html( $my_account_link ) . '"></span>';
			$display_terms = $config->get_p24_acceptinshop();
			self::echo_modal_html( $display_terms );
		}
	}

	/**
	 * Get HTML code for modal
	 *
	 * @param bool $need_terms If checkbox to show terms is needed. Default to false.
	 *
	 * @return string
	 */
	public static function get_modal_html( $need_terms = false ) {
		$terms               = $need_terms ? '1' : '0';
		$translation_element = self::get_translation_element();
		return <<<RET
        {$translation_element}
        <div id="P24FormAreaHolder" style="display: none">
            <div id="P24FormArea" class="popup" data-terms="{$terms}"></div>
        </div>
RET;
	}

	/**
	 * Echo HTML code for modal
	 *
	 * @param bool $need_terms If checkbox to show terms is needed. Default to false.
	 */
	public static function echo_modal_html( $need_terms = false ) {
		echo self::get_modal_html( $need_terms ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get input with translations.
	 *
	 * @return string
	 */
	public static function get_translation_element() {
		$translate = array(
			'name'              => __( 'Imię i nazwisko', 'przelewy24' ),
			'nr'                => __( 'Numer karty', 'przelewy24' ),
			'cvv'               => __( 'CVV', 'przelewy24' ),
			'dt'                => __( 'Data ważności', 'przelewy24' ),
			'pay'               => __( 'Zapłać', 'przelewy24' ),
			'3ds'               => __( 'Kliknij tutaj aby kontynuować zakupy', 'przelewy24' ),
			'registerCardLabel' => __( 'Zapisz kartę', 'przelewy24' ),
			'description'       => __( 'Zarejestruj i zapłać', 'przelewy24' ),
			'termsPrefix'       => __( 'Tak, przeczytałem i akceptuję', 'przelewy24' ),
			'termsLinkLabel'    => __( 'regulamin Przelewy24', 'przelewy24' ),
		);

		return <<<CODE
        <input type="hidden" id="p24_dictionary" value='{"registerCardLabel":"{$translate['registerCardLabel']}","description":"{$translate['description']}", "cardHolderLabel":"{$translate['name']}", "cardNumberLabel":"{$translate['nr']}", "cvvLabel":"{$translate['cvv']}", "expDateLabel":"{$translate['dt']}", "acceptTermsLabelPrefix":"{$translate['termsPrefix']}", "acceptTermsLinkLabel":"{$translate['termsLinkLabel']}", "payButtonCaption":"{$translate['pay']}", "threeDSAuthMessage":"{$translate['3ds']}"}'>
CODE;
	}
}
