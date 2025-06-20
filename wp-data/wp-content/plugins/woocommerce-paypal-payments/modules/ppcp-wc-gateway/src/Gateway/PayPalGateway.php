<?php
/**
 * The PayPal Payment Gateway
 *
 * @package WooCommerce\PayPalCommerce\WcGateway\Gateway
 */

declare(strict_types=1);

namespace WooCommerce\PayPalCommerce\WcGateway\Gateway;

use Exception;
use Psr\Log\LoggerInterface;
use WC_Order;
use WC_Payment_Tokens;
use WooCommerce\PayPalCommerce\ApiClient\Endpoint\OrderEndpoint;
use WooCommerce\PayPalCommerce\ApiClient\Entity\OrderStatus;
use WooCommerce\PayPalCommerce\ApiClient\Entity\PaymentToken;
use WooCommerce\PayPalCommerce\ApiClient\Exception\PayPalApiException;
use WooCommerce\PayPalCommerce\Onboarding\Environment;
use WooCommerce\PayPalCommerce\Onboarding\State;
use WooCommerce\PayPalCommerce\Session\SessionHandler;
use WooCommerce\PayPalCommerce\WcSubscriptions\FreeTrialHandlerTrait;
use WooCommerce\PayPalCommerce\WcSubscriptions\Helper\SubscriptionHelper;
use WooCommerce\PayPalCommerce\Vaulting\PaymentTokenRepository;
use WooCommerce\PayPalCommerce\WcGateway\Exception\GatewayGenericException;
use WooCommerce\PayPalCommerce\WcGateway\Exception\PayPalOrderMissingException;
use WooCommerce\PayPalCommerce\WcGateway\FundingSource\FundingSourceRenderer;
use WooCommerce\PayPalCommerce\WcGateway\Gateway\PayUponInvoice\PayUponInvoiceGateway;
use WooCommerce\PayPalCommerce\WcGateway\Processor\OrderMetaTrait;
use WooCommerce\PayPalCommerce\WcGateway\Processor\OrderProcessor;
use WooCommerce\PayPalCommerce\WcGateway\Processor\PaymentsStatusHandlingTrait;
use WooCommerce\PayPalCommerce\WcGateway\Processor\RefundProcessor;
use WooCommerce\PayPalCommerce\WcGateway\Processor\TransactionIdHandlingTrait;
use WooCommerce\PayPalCommerce\WcGateway\Settings\Settings;
use WooCommerce\PayPalCommerce\WcGateway\Settings\SettingsRenderer;
use WooCommerce\PayPalCommerce\Vendor\Psr\Container\ContainerInterface;

/**
 * Class PayPalGateway
 */
class PayPalGateway extends \WC_Payment_Gateway {

	use ProcessPaymentTrait, FreeTrialHandlerTrait, GatewaySettingsRendererTrait, OrderMetaTrait, TransactionIdHandlingTrait, PaymentsStatusHandlingTrait;

	const ID                            = 'ppcp-gateway';
	const INTENT_META_KEY               = '_ppcp_paypal_intent';
	const ORDER_ID_META_KEY             = '_ppcp_paypal_order_id';
	const ORDER_PAYMENT_MODE_META_KEY   = '_ppcp_paypal_payment_mode';
	const ORDER_PAYMENT_SOURCE_META_KEY = '_ppcp_paypal_payment_source';
	const FEES_META_KEY                 = '_ppcp_paypal_fees';
	const REFUND_FEES_META_KEY          = '_ppcp_paypal_refund_fees';
	const REFUNDS_META_KEY              = '_ppcp_refunds';
	const THREE_D_AUTH_RESULT_META_KEY  = '_ppcp_paypal_3DS_auth_result';
	const FRAUD_RESULT_META_KEY         = '_ppcp_paypal_fraud_result';

	/**
	 * The Settings Renderer.
	 *
	 * @var SettingsRenderer
	 */
	protected $settings_renderer;

	/**
	 * The funding source renderer.
	 *
	 * @var FundingSourceRenderer
	 */
	protected $funding_source_renderer;

	/**
	 * The processor for orders.
	 *
	 * @var OrderProcessor
	 */
	protected $order_processor;

	/**
	 * The settings.
	 *
	 * @var ContainerInterface
	 */
	protected $config;

	/**
	 * The Session Handler.
	 *
	 * @var SessionHandler
	 */
	protected $session_handler;

	/**
	 * The Refund Processor.
	 *
	 * @var RefundProcessor
	 */
	private $refund_processor;

	/**
	 * The state.
	 *
	 * @var State
	 */
	protected $state;

	/**
	 * Service able to provide transaction url for an order.
	 *
	 * @var TransactionUrlProvider
	 */
	protected $transaction_url_provider;

	/**
	 * The subscription helper.
	 *
	 * @var SubscriptionHelper
	 */
	protected $subscription_helper;

	/**
	 * The payment token repository.
	 *
	 * @var PaymentTokenRepository
	 */
	protected $payment_token_repository;

	/**
	 * Whether the plugin is in onboarded state.
	 *
	 * @var bool
	 */
	private $onboarded;

	/**
	 * ID of the current PPCP gateway settings page, or empty if it is not such page.
	 *
	 * @var string
	 */
	protected $page_id;

	/**
	 * The environment.
	 *
	 * @var Environment
	 */
	protected $environment;

	/**
	 * The logger.
	 *
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * The api shop country.
	 *
	 * @var string
	 */
	protected $api_shop_country;

	/**
	 * The order endpoint.
	 *
	 * @var OrderEndpoint
	 */
	private $order_endpoint;

	/**
	 * The function return the PayPal checkout URL for the given order ID.
	 *
	 * @var callable(string):string
	 */
	private $paypal_checkout_url_factory;

	/**
	 * PayPalGateway constructor.
	 *
	 * @param SettingsRenderer        $settings_renderer The Settings Renderer.
	 * @param FundingSourceRenderer   $funding_source_renderer The funding source renderer.
	 * @param OrderProcessor          $order_processor The Order Processor.
	 * @param ContainerInterface      $config The settings.
	 * @param SessionHandler          $session_handler The Session Handler.
	 * @param RefundProcessor         $refund_processor The Refund Processor.
	 * @param State                   $state The state.
	 * @param TransactionUrlProvider  $transaction_url_provider Service providing transaction view URL based on order.
	 * @param SubscriptionHelper      $subscription_helper The subscription helper.
	 * @param string                  $page_id ID of the current PPCP gateway settings page, or empty if it is not such page.
	 * @param Environment             $environment The environment.
	 * @param PaymentTokenRepository  $payment_token_repository The payment token repository.
	 * @param LoggerInterface         $logger The logger.
	 * @param string                  $api_shop_country The api shop country.
	 * @param OrderEndpoint           $order_endpoint The order endpoint.
	 * @param callable(string):string $paypal_checkout_url_factory The function return the PayPal checkout URL for the given order ID.
	 * @param string                  $place_order_button_text The text for the standard "Place order" button.
	 */
	public function __construct(
		SettingsRenderer $settings_renderer,
		FundingSourceRenderer $funding_source_renderer,
		OrderProcessor $order_processor,
		ContainerInterface $config,
		SessionHandler $session_handler,
		RefundProcessor $refund_processor,
		State $state,
		TransactionUrlProvider $transaction_url_provider,
		SubscriptionHelper $subscription_helper,
		string $page_id,
		Environment $environment,
		PaymentTokenRepository $payment_token_repository,
		LoggerInterface $logger,
		string $api_shop_country,
		OrderEndpoint $order_endpoint,
		callable $paypal_checkout_url_factory,
		string $place_order_button_text
	) {
		$this->id                          = self::ID;
		$this->settings_renderer           = $settings_renderer;
		$this->funding_source_renderer     = $funding_source_renderer;
		$this->order_processor             = $order_processor;
		$this->config                      = $config;
		$this->session_handler             = $session_handler;
		$this->refund_processor            = $refund_processor;
		$this->state                       = $state;
		$this->transaction_url_provider    = $transaction_url_provider;
		$this->subscription_helper         = $subscription_helper;
		$this->page_id                     = $page_id;
		$this->environment                 = $environment;
		$this->onboarded                   = $state->current_state() === State::STATE_ONBOARDED;
		$this->payment_token_repository    = $payment_token_repository;
		$this->logger                      = $logger;
		$this->api_shop_country            = $api_shop_country;
		$this->paypal_checkout_url_factory = $paypal_checkout_url_factory;
		$this->order_button_text           = $place_order_button_text;

		if ( $this->onboarded ) {
			$this->supports = array( 'refunds', 'tokenization' );
		}
		if ( $this->config->has( 'enabled' ) && $this->config->get( 'enabled' ) ) {
			$this->supports = array(
				'refunds',
				'products',
			);

			if (
				( $this->config->has( 'vault_enabled' ) && $this->config->get( 'vault_enabled' ) )
				|| ( $this->config->has( 'subscriptions_mode' ) && $this->config->get( 'subscriptions_mode' ) === 'subscriptions_api' )
			) {
				array_push(
					$this->supports,
					'tokenization',
					'subscriptions',
					'subscription_cancellation',
					'subscription_suspension',
					'subscription_reactivation',
					'subscription_amount_changes',
					'subscription_date_changes',
					'subscription_payment_method_change',
					'subscription_payment_method_change_customer',
					'subscription_payment_method_change_admin',
					'multiple_subscriptions'
				);
			} elseif ( $this->config->has( 'vault_enabled_dcc' ) && $this->config->get( 'vault_enabled_dcc' ) ) {
				$this->supports[] = 'tokenization';
			}
		}

        $market = isset($_GET['market'])?$_GET['market']:false;
        if( isset( $_COOKIE['market'] ) && $market === false ) { $market = $_COOKIE['market']; }
        if( $market === false || $market === 'undefined' ) {
            $order = $this->session_handler->order();

            if( $order ) {
                $market = $order->get_meta( '_market', false );
            }
        }

        if( $market && $market == 'uk' ) {
            $this->config->set( 'merchant_email_sandbox', 'sales@globalparts.co.uk');
            $this->config->set( 'merchant_email_production', 'sales@globalparts.co.uk');
            $this->config->set( 'merchant_email', 'sales@globalparts.co.uk');

            $this->config->set( 'merchant_id_sandbox', '2HUEUJTCBD4H2');
            $this->config->set( 'merchant_id_production', '2HUEUJTCBD4H2');
            $this->config->set( 'merchant_id', '2HUEUJTCBD4H2');

            $this->config->set( 'client_id_sandbox', 'AZZjjgueRBKik67-VMWxevs1x4cIKKVSxq5nGU3jPNH0gZSsUy6XYmoXn9V-UmQP57ZH0S_6fSRzoDWU');
            $this->config->set( 'client_id_production', 'AZZjjgueRBKik67-VMWxevs1x4cIKKVSxq5nGU3jPNH0gZSsUy6XYmoXn9V-UmQP57ZH0S_6fSRzoDWU');
            $this->config->set( 'client_id', 'AZZjjgueRBKik67-VMWxevs1x4cIKKVSxq5nGU3jPNH0gZSsUy6XYmoXn9V-UmQP57ZH0S_6fSRzoDWU');

            $this->config->set( 'client_secret_sandbox', 'EBZbR2EDqwUm1ICTX9QNpeB3uFFjs59R673fL1d-H4Y9dM4cdTAbYPbKlNZYXCNh4SgIGewHGzgpezN4');
            $this->config->set( 'client_secret_production', 'EBZbR2EDqwUm1ICTX9QNpeB3uFFjs59R673fL1d-H4Y9dM4cdTAbYPbKlNZYXCNh4SgIGewHGzgpezN4');
            $this->config->set( 'client_secret', 'EBZbR2EDqwUm1ICTX9QNpeB3uFFjs59R673fL1d-H4Y9dM4cdTAbYPbKlNZYXCNh4SgIGewHGzgpezN4');
        } else {
            $this->config->set( 'merchant_email_sandbox', 'kontakt@globalparts.com.pl');
            $this->config->set( 'merchant_email_production', 'kontakt@globalparts.com.pl');
            $this->config->set( 'merchant_email', 'kontakt@globalparts.com.pl');

            $this->config->set( 'merchant_id_sandbox', 'IDZ232MA6JZM2');
            $this->config->set( 'merchant_id_production', 'IDZ232MA6JZM2');
            $this->config->set( 'merchant_id', 'IDZ232MA6JZM2');

            $this->config->set( 'client_id_sandbox', 'AXGm-wTjYLj_BHh8pSG8NwrkwJ99LA6K6M4a9LHQLNSE2BVK5NljgPlz0pYVIxLfS1V9Aq9qB70C9CHY');
            $this->config->set( 'client_id_production', 'AXGm-wTjYLj_BHh8pSG8NwrkwJ99LA6K6M4a9LHQLNSE2BVK5NljgPlz0pYVIxLfS1V9Aq9qB70C9CHY');
            $this->config->set( 'client_id', 'AXGm-wTjYLj_BHh8pSG8NwrkwJ99LA6K6M4a9LHQLNSE2BVK5NljgPlz0pYVIxLfS1V9Aq9qB70C9CHY');

            $this->config->set( 'client_secret_sandbox', 'EFO-yQuHgp_IaYCDeS1Jf6gTHAT6TukV3-cmxJeTmhYDdy_55c5lzuZhd3onvC7ZPQs6B2aDSteFjh2f');
            $this->config->set( 'client_secret_production', 'EFO-yQuHgp_IaYCDeS1Jf6gTHAT6TukV3-cmxJeTmhYDdy_55c5lzuZhd3onvC7ZPQs6B2aDSteFjh2f');
            $this->config->set( 'client_secret', 'EFO-yQuHgp_IaYCDeS1Jf6gTHAT6TukV3-cmxJeTmhYDdy_55c5lzuZhd3onvC7ZPQs6B2aDSteFjh2f');
        }

		$this->method_title       = $this->define_method_title();
		$this->method_description = $this->define_method_description();
		$this->title              = $this->config->has( 'title' ) ?
			$this->config->get( 'title' ) : $this->method_title;
		$this->description        = $this->config->has( 'description' ) ?
			$this->config->get( 'description' ) : $this->method_description;

		$funding_source = $this->session_handler->funding_source();
		if ( $funding_source ) {
			$order = $this->session_handler->order();
			if ( $order &&
				( $order->status()->is( OrderStatus::APPROVED ) || $order->status()->is( OrderStatus::COMPLETED ) )
			) {
				$this->title             = $this->funding_source_renderer->render_name( $funding_source );
				$this->description       = $this->funding_source_renderer->render_description( $funding_source );
				$this->order_button_text = null;
			}
		}

		$this->init_form_fields();
		$this->init_settings();

		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'process_admin_options',
			)
		);

		$this->order_endpoint = $order_endpoint;
	}

	/**
	 * Return the gateway's title.
	 *
	 * @return string
	 */
	public function get_title() {
		if ( is_admin() ) {
			// $theorder and other things for retrieving the order or post info are not available
			// in the constructor, so must do it here.
			global $theorder;
			if ( $theorder instanceof WC_Order ) {
				if ( $theorder->get_payment_method() === self::ID ) {
					$payment_method_title = $theorder->get_payment_method_title();
					if ( $payment_method_title ) {
						$this->title = $payment_method_title;
					}
				}
			}
		}

		return parent::get_title();
	}

	/**
	 * Whether the Gateway needs to be setup.
	 *
	 * @return bool
	 */
	public function needs_setup(): bool {
		return ! $this->onboarded;
	}

	/**
	 * Initializes the form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-paypal-payments' ),
				'type'        => 'checkbox',
				'desc_tip'    => true,
				'description' => __( 'In order to use PayPal or Advanced Card Processing, you need to enable the Gateway.', 'woocommerce-paypal-payments' ),
				'label'       => __( 'Enable PayPal features for your store', 'woocommerce-paypal-payments' ),
				'default'     => 'no',
			),
			'ppcp'    => array(
				'type' => 'ppcp',
			),
		);

		$should_show_enabled_checkbox = $this->is_paypal_tab() && ( $this->config->has( 'merchant_email' ) && $this->config->get( 'merchant_email' ) );
		if ( ! $should_show_enabled_checkbox ) {
			unset( $this->form_fields['enabled'] );
		}
	}

	/**
	 * Defines the method title. If we are on the credit card tab in the settings, we want to change this.
	 *
	 * @return string
	 */
	private function define_method_title(): string {
		if ( $this->is_connection_tab() ) {
			return __( 'Account Setup', 'woocommerce-paypal-payments' );
		}
		if ( $this->is_credit_card_tab() ) {
			return __( 'Advanced Card Processing', 'woocommerce-paypal-payments' );
		}
		if ( $this->is_pay_later_tab() ) {
			return __( 'PayPal Pay Later', 'woocommerce-paypal-payments' );
		}
		if ( $this->is_paypal_tab() ) {
			return __( 'Standard Payments', 'woocommerce-paypal-payments' );
		}
		if ( $this->is_pui_tab() ) {
			return __( 'Pay upon Invoice', 'woocommerce-paypal-payments' );
		}

		return __( 'PayPal', 'woocommerce-paypal-payments' );
	}

	/**
	 * Defines the method description. If we are on the credit card tab in the settings, we want to change this.
	 *
	 * @return string
	 */
	private function define_method_description(): string {
		if ( $this->is_connection_tab() ) {
			return '';
		}

		if ( $this->is_credit_card_tab() ) {
			return __(
				'Accept debit and credit cards, and local payment methods.',
				'woocommerce-paypal-payments'
			);
		}

		if ( $this->is_pay_later_tab() ) {
			return sprintf(
			// translators: %1$s is </ br> HTML tag and %2$s, %3$s are the opening and closing of HTML <i> tag.
				__( 'Let customers pay over time while you get paid up front — at no additional cost.%1$sPayPal’s pay later options are boosting merchant conversion rates and increasing cart sizes by 39%%. %2$s(PayPal Q2 Earnings-2021.)%3$s', 'woocommerce-paypal-payments' ),
				'</ br>',
				'<i>',
				'</ i>'
			);
		}

		if ( is_admin() ) {
			return __(
				'Accept PayPal, Pay Later and alternative payment types.',
				'woocommerce-paypal-payments'
			);
		}

		return __(
			'Pay via PayPal.',
			'woocommerce-paypal-payments'
		);
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended

	/**
	 * Determines, whether the current session is on the credit card tab in the admin settings.
	 *
	 * @return bool
	 */
	private function is_credit_card_tab() : bool {
		return is_admin()
			&& CreditCardGateway::ID === $this->page_id;

	}

	/**
	 * Whether we are on the PUI tab.
	 *
	 * @return bool
	 */
	private function is_pui_tab():bool {
		if ( 'DE' !== $this->api_shop_country ) {
			return false;
		}

		return is_admin() && PayUponInvoiceGateway::ID === $this->page_id;
	}

	/**
	 * Whether we are on the connection tab.
	 *
	 * @return bool true if is connection tab, otherwise false
	 */
	protected function is_connection_tab() : bool {
		return is_admin()
			&& Settings::CONNECTION_TAB_ID === $this->page_id;
	}

	/**
	 * Whether we are on the pay-later tab.
	 *
	 * @return bool true if is pay-later tab, otherwise false
	 */
	protected function is_pay_later_tab() : bool {
		return is_admin()
			&& Settings::PAY_LATER_TAB_ID === $this->page_id;
	}

	/**
	 * Whether we are on the PayPal settings tab.
	 *
	 * @return bool
	 */
	private function is_paypal_tab() : bool {
		return ! $this->is_credit_card_tab()
			&& is_admin()
			&& self::ID === $this->page_id;
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	/**
	 * Process payment for a WooCommerce order.
	 *
	 * @param int $order_id The WooCommerce order id.
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$wc_order = wc_get_order( $order_id );

		if ( ! is_a( $wc_order, WC_Order::class ) ) {
			return $this->handle_payment_failure(
				null,
				new GatewayGenericException( new Exception( 'WC order was not found.' ) )
			);
		}

        $market = $wc_order->get_meta( '_market' );
        if( $market && $market == 'uk' ) {
            $this->config->set( 'merchant_email_sandbox', 'sales@globalparts.co.uk');
            $this->config->set( 'merchant_email_production', 'sales@globalparts.co.uk');
            $this->config->set( 'merchant_email', 'sales@globalparts.co.uk');

            $this->config->set( 'merchant_id_sandbox', '2HUEUJTCBD4H2');
            $this->config->set( 'merchant_id_production', '2HUEUJTCBD4H2');
            $this->config->set( 'merchant_id', '2HUEUJTCBD4H2');

            $this->config->set( 'client_id_sandbox', 'AZZjjgueRBKik67-VMWxevs1x4cIKKVSxq5nGU3jPNH0gZSsUy6XYmoXn9V-UmQP57ZH0S_6fSRzoDWU');
            $this->config->set( 'client_id_production', 'AZZjjgueRBKik67-VMWxevs1x4cIKKVSxq5nGU3jPNH0gZSsUy6XYmoXn9V-UmQP57ZH0S_6fSRzoDWU');
            $this->config->set( 'client_id', 'AZZjjgueRBKik67-VMWxevs1x4cIKKVSxq5nGU3jPNH0gZSsUy6XYmoXn9V-UmQP57ZH0S_6fSRzoDWU');

            $this->config->set( 'client_secret_sandbox', 'EBZbR2EDqwUm1ICTX9QNpeB3uFFjs59R673fL1d-H4Y9dM4cdTAbYPbKlNZYXCNh4SgIGewHGzgpezN4');
            $this->config->set( 'client_secret_production', 'EBZbR2EDqwUm1ICTX9QNpeB3uFFjs59R673fL1d-H4Y9dM4cdTAbYPbKlNZYXCNh4SgIGewHGzgpezN4');
            $this->config->set( 'client_secret', 'EBZbR2EDqwUm1ICTX9QNpeB3uFFjs59R673fL1d-H4Y9dM4cdTAbYPbKlNZYXCNh4SgIGewHGzgpezN4');
        } else {
            $this->config->set( 'merchant_email_sandbox', 'kontakt@globalparts.com.pl');
            $this->config->set( 'merchant_email_production', 'kontakt@globalparts.com.pl');
            $this->config->set( 'merchant_email', 'kontakt@globalparts.com.pl');

            $this->config->set( 'merchant_id_sandbox', 'IDZ232MA6JZM2');
            $this->config->set( 'merchant_id_production', 'IDZ232MA6JZM2');
            $this->config->set( 'merchant_id', 'IDZ232MA6JZM2');

            $this->config->set( 'client_id_sandbox', 'AXGm-wTjYLj_BHh8pSG8NwrkwJ99LA6K6M4a9LHQLNSE2BVK5NljgPlz0pYVIxLfS1V9Aq9qB70C9CHY');
            $this->config->set( 'client_id_production', 'AXGm-wTjYLj_BHh8pSG8NwrkwJ99LA6K6M4a9LHQLNSE2BVK5NljgPlz0pYVIxLfS1V9Aq9qB70C9CHY');
            $this->config->set( 'client_id', 'AXGm-wTjYLj_BHh8pSG8NwrkwJ99LA6K6M4a9LHQLNSE2BVK5NljgPlz0pYVIxLfS1V9Aq9qB70C9CHY');

            $this->config->set( 'client_secret_sandbox', 'EFO-yQuHgp_IaYCDeS1Jf6gTHAT6TukV3-cmxJeTmhYDdy_55c5lzuZhd3onvC7ZPQs6B2aDSteFjh2f');
            $this->config->set( 'client_secret_production', 'EFO-yQuHgp_IaYCDeS1Jf6gTHAT6TukV3-cmxJeTmhYDdy_55c5lzuZhd3onvC7ZPQs6B2aDSteFjh2f');
            $this->config->set( 'client_secret', 'EFO-yQuHgp_IaYCDeS1Jf6gTHAT6TukV3-cmxJeTmhYDdy_55c5lzuZhd3onvC7ZPQs6B2aDSteFjh2f');
        }

        error_log( 'PAY_PAL GATEWAY PROCESS PAYMENT !' . print_r( $market, true ) );

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$funding_source = wc_clean( wp_unslash( $_POST['ppcp-funding-source'] ?? ( $_POST['funding_source'] ?? '' ) ) );

		if ( $funding_source ) {
			$wc_order->set_payment_method_title( $this->funding_source_renderer->render_name( $funding_source ) );
			$wc_order->save();
		}

		if ( 'card' !== $funding_source && $this->is_free_trial_order( $wc_order ) && ! $this->subscription_helper->paypal_subscription_id() ) {
			$user_id = (int) $wc_order->get_customer_id();
			$tokens  = $this->payment_token_repository->all_for_user_id( $user_id );
			if ( ! array_filter(
				$tokens,
				function ( PaymentToken $token ): bool {
					return isset( $token->source()->paypal );
				}
			) ) {
				return $this->handle_payment_failure( $wc_order, new Exception( 'No saved PayPal account.' ) );
			}

			$wc_order->payment_complete();

			return $this->handle_payment_success( $wc_order );
		}

		/**
		 * If customer has chosen change Subscription payment.
		 */
		if ( $this->subscription_helper->has_subscription( $order_id ) && $this->subscription_helper->is_subscription_change_payment() ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$saved_paypal_payment = wc_clean( wp_unslash( $_POST['saved_paypal_payment'] ?? '' ) );
			if ( $saved_paypal_payment ) {
				$payment_token = WC_Payment_Tokens::get( $saved_paypal_payment );
				if ( $payment_token ) {
					$wc_order->add_payment_token( $payment_token );
					$wc_order->save();

					return $this->handle_payment_success( $wc_order );
				}

				wc_add_notice( __( 'Could not change payment.', 'woocommerce-paypal-payments' ), 'error' );

				return array(
					'result'       => 'failure',
					'redirect'     => wc_get_checkout_url(),
					'errorMessage' => __( 'Could not change payment.', 'woocommerce-paypal-payments' ),
				);
			}
		}

		/**
		 * If the WC_Order is paid through the approved webhook.
		 */
		//phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['ppcp-resume-order'] ) && $wc_order->has_status( 'processing' ) ) {
			return $this->handle_payment_success( $wc_order );
		}
		//phpcs:enable WordPress.Security.NonceVerification.Recommended

		try {
			$paypal_subscription_id = WC()->session->get( 'ppcp_subscription_id' ) ?? '';
			if ( $paypal_subscription_id ) {
				$order = $this->session_handler->order();
				$this->add_paypal_meta( $wc_order, $order, $this->environment );

				$subscriptions = function_exists( 'wcs_get_subscriptions_for_order' ) ? wcs_get_subscriptions_for_order( $order_id ) : array();
				foreach ( $subscriptions as $subscription ) {
					$subscription->update_meta_data( 'ppcp_subscription', $paypal_subscription_id );
					$subscription->save();

					$subscription->add_order_note( "PayPal subscription {$paypal_subscription_id} added." );
				}

				$transaction_id = $this->get_paypal_order_transaction_id( $order );
				if ( $transaction_id ) {
					$this->update_transaction_id( $transaction_id, $wc_order );
				}

				$wc_order->payment_complete();

				return $this->handle_payment_success( $wc_order );
			}
			try {
				$this->order_processor->process( $wc_order );

				do_action( 'woocommerce_paypal_payments_before_handle_payment_success', $wc_order );

				return $this->handle_payment_success( $wc_order );
			} catch ( PayPalOrderMissingException $exc ) {
				$order = $this->order_processor->create_order( $wc_order );

				return array(
					'result'   => 'success',
					'redirect' => ( $this->paypal_checkout_url_factory )( $order->id() ),
				);
			}
		} catch ( PayPalApiException $error ) {
			$retry_keys_messages = array(
				'INSTRUMENT_DECLINED'   => __( 'Instrument declined.', 'woocommerce-paypal-payments' ),
				'PAYER_ACTION_REQUIRED' => __( 'Payer action required, possibly overcharge.', 'woocommerce-paypal-payments' ),
			);
			$retry_errors        = array_values(
				array_filter(
					array_keys( $retry_keys_messages ),
					function ( string $key ) use ( $error ): bool {
						return $error->has_detail( $key );
					}
				)
			);

			if ( $retry_errors ) {
				$retry_error_key = $retry_errors[0];

				$wc_order->update_status(
					'failed',
					$retry_keys_messages[ $retry_error_key ] . ' ' . $error->details()[0]->description ?? ''
				);

				$this->session_handler->increment_insufficient_funding_tries();
				if ( $this->session_handler->insufficient_funding_tries() >= 3 ) {
					return $this->handle_payment_failure(
						null,
						new Exception(
							__( 'Please use a different payment method.', 'woocommerce-paypal-payments' ),
							$error->getCode(),
							$error
						)
					);
				}

				return array(
					'result'   => 'success',
					'redirect' => ( $this->paypal_checkout_url_factory )( $this->session_handler->order()->id() ),
				);
			}

			return $this->handle_payment_failure(
				$wc_order,
				new Exception(
					Messages::generic_payment_error_message() . ' ' . $error->getMessage(),
					$error->getCode(),
					$error
				)
			);
		} catch ( Exception $error ) {
			return $this->handle_payment_failure( $wc_order, $error );
		}
	}

	/**
	 * Process refund.
	 *
	 * If the gateway declares 'refunds' support, this will allow it to refund.
	 * a passed in amount.
	 *
	 * @param  int    $order_id Order ID.
	 * @param  float  $amount Refund amount.
	 * @param  string $reason Refund reason.
	 * @return boolean True or false based on success, or a WP_Error object.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );
		if ( ! is_a( $order, \WC_Order::class ) ) {
			return false;
		}
		return $this->refund_processor->process( $order, (float) $amount, (string) $reason );
	}

	/**
	 * Return transaction url for this gateway and given order.
	 *
	 * @param \WC_Order $order WC order to get transaction url by.
	 *
	 * @return string
	 */
	public function get_transaction_url( $order ): string {
		$this->view_transaction_url = $this->transaction_url_provider->get_transaction_url_base( $order );

		return parent::get_transaction_url( $order );
	}

	/**
	 * Updates WooCommerce gateway option.
	 *
	 * @param string $key The option key.
	 * @param string $value The option value.
	 * @return bool was anything saved?
	 */
	public function update_option( $key, $value = '' ) {
		$ret = parent::update_option( $key, $value );

		if ( 'enabled' === $key ) {
			$this->config->set( 'enabled', 'yes' === $value );
			$this->config->persist();

			return true;
		}

		return $ret;
	}

	/**
	 * Returns the settings renderer.
	 *
	 * @return SettingsRenderer
	 */
	protected function settings_renderer(): SettingsRenderer {
		return $this->settings_renderer;
	}
}
