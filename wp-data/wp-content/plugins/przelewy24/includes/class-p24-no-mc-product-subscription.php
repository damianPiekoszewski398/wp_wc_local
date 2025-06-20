<?php
/**
 * File that define P24_Product_Simple class.
 *
 * @package Przelewy24
 */

defined( 'ABSPATH' ) || exit;


/**
 *
 *
 * The product is not aware of multiple currencies.
 * We need this class before we have configured multi currency.
 */
class P24_No_Mc_Product_Subscription extends WC_Product {

	const TYPE = 'p24_subscription';

	/**
	 * Product type for backward compatibility.
	 *
	 * Do not use.
	 *
	 * @var string
	 */
	public $product_type;

	/**
	 * Stores additional product data.
	 *
	 * @var array
	 */
	protected $extra_data = array(
		'days' => 1,
	);

	/**
	 * P24_Product_Simple constructor.
	 *
	 * @param int $product Id of product.
	 */
	public function __construct( $product = 0 ) {
		/* For backward compatibility only. */
		$this->product_type = self::TYPE;
		parent::__construct( $product );
	}

	/**
	 * Return product type.
	 *
	 * @return string
	 */
	public function get_type() {
		return self::TYPE;
	}

	/**
	 * Get days.
	 *
	 * @return int
	 */
	public function get_days() {
		$days = (int) $this->get_prop( 'days' );
		if ( $days < 1 ) {
			$days = 1;
		}

		return $days;
	}

	/**
	 * Set_days.
	 *
	 * @param int $days Number of days.
	 */
	public function set_days( $days ) {
		$days = (int) $days;
		if ( $days < 1 ) {
			$days = 1;
		}
		$this->set_prop( 'days', $days );
	}

	/**
	 * Check if product is possible to buy.
	 *
	 * @return bool
	 */
	private function possible_to_buy() {
		global $userdata;
		if ( ! $userdata ) {
			/* If the user in not registered, he cannot buy the product. */
			return false;
		} elseif ( ! $this->is_purchasable() ) {
			return false;
		} elseif ( ! $this->is_in_stock() ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @return string
	 */
	public function add_to_cart_url() {
		$possible_to_buy = $this->possible_to_buy();
		$url             = $possible_to_buy ? remove_query_arg(
			'added-to-cart',
			add_query_arg(
				array(
					'add-to-cart' => $this->get_id(),
				),
				( function_exists( 'is_feed' ) && is_feed() ) || ( function_exists( 'is_404' ) && is_404() ) ? $this->get_permalink() : ''
			)
		) : $this->get_permalink();
		return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
	}

	/**
	 * Get the add to cart button text.
	 *
	 * @return string
	 */
	public function add_to_cart_text() {
		$possible_to_buy = $this->possible_to_buy();
		$text            = $possible_to_buy ? __( 'Add to cart', 'woocommerce' ) : __( 'Read more', 'woocommerce' );

		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}

	/**
	 * Get the add to cart button text description - used in aria tags.
	 *
	 * @return string
	 */
	public function add_to_cart_description() {
		$possible_to_buy = $this->possible_to_buy();
		/* translators: %s: Product title */
		$text = $possible_to_buy ? __( 'Add &ldquo;%s&rdquo; to your cart', 'woocommerce' ) : __( 'Read more about &ldquo;%s&rdquo;', 'woocommerce' );

		return apply_filters( 'woocommerce_product_add_to_cart_description', sprintf( $text, $this->get_name() ), $this );
	}
}
