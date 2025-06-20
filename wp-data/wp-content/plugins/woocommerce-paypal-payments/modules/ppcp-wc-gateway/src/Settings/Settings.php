<?php
/**
 * The settings object.
 *
 * @package WooCommerce\PayPalCommerce\WcGateway\Settings
 */

declare(strict_types=1);

namespace WooCommerce\PayPalCommerce\WcGateway\Settings;

use WooCommerce\PayPalCommerce\WcGateway\Exception\NotFoundException;
use WooCommerce\PayPalCommerce\Vendor\Psr\Container\ContainerInterface;

/**
 * Class Settings
 */
class Settings implements ContainerInterface {

	const KEY               = 'woocommerce-ppcp-settings';
	const CONNECTION_TAB_ID = 'ppcp-connection';
	const PAY_LATER_TAB_ID  = 'ppcp-pay-later';

	/**
	 * The settings.
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * The list of selected default button locations.
	 *
	 * @var string[]
	 */
	protected $default_button_locations;

	/**
	 * The list of selected default pay later button locations.
	 *
	 * @var string[]
	 */
	protected $default_pay_later_button_locations;

	/**
	 * The list of selected default pay later messaging locations.
	 *
	 * @var string[]
	 */
	protected $default_pay_later_messaging_locations;

	/**
	 * The default ACDC gateway title.
	 *
	 * @var string
	 */
	protected $default_dcc_gateway_title;

	/**
	 * Settings constructor.
	 *
	 * @param string[] $default_button_locations The list of selected default button locations.
	 * @param string   $default_dcc_gateway_title The default ACDC gateway title.
	 * @param string[] $default_pay_later_button_locations The list of selected default pay later button locations.
	 * @param string[] $default_pay_later_messaging_locations The list of selected default pay later messaging locations.
	 */
	public function __construct(
		array $default_button_locations,
		string $default_dcc_gateway_title,
		array $default_pay_later_button_locations,
		array $default_pay_later_messaging_locations
	) {
		$this->default_button_locations              = $default_button_locations;
		$this->default_dcc_gateway_title             = $default_dcc_gateway_title;
		$this->default_pay_later_button_locations    = $default_pay_later_button_locations;
		$this->default_pay_later_messaging_locations = $default_pay_later_messaging_locations;
	}

	/**
	 * Returns the value for an id.
	 *
	 * @param string $id The value identificator.
	 *
	 * @return mixed
	 * @throws NotFoundException When nothing was found.
	 */
	public function get( $id ) {
		if ( ! $this->has( $id ) ) {
			throw new NotFoundException();
		}
		return $this->settings[ $id ];
	}

	/**
	 * Whether a value exists.
	 *
	 * @param string $id The value identificator.
	 *
	 * @return bool
	 */
	public function has( $id ) {
		$this->load();
		return array_key_exists( $id, $this->settings );
	}

	/**
	 * Sets a value.
	 *
	 * @param string $id The value identificator.
	 * @param mixed  $value The value.
	 */
	public function set( $id, $value ) {
		$this->load();
		$this->settings[ $id ] = $value;
	}

	/**
	 * Stores the settings to the database.
	 */
	public function persist() {

		return update_option( self::KEY, $this->settings );
	}


	/**
	 * Loads the settings.
	 *
	 * @return bool
	 */
	private function load(): bool {

		if ( $this->settings ) {
			return false;
		}
		$this->settings = get_option( self::KEY, array() );

		$defaults = array(
			'title'                                    => __( 'PayPal', 'woocommerce-paypal-payments' ),
			'description'                              => __(
				'Pay via PayPal.',
				'woocommerce-paypal-payments'
			),
			'smart_button_locations'                   => $this->default_button_locations,
			'smart_button_enable_styling_per_location' => true,
			'pay_later_messaging_enabled'              => true,
			'pay_later_button_enabled'                 => true,
			'pay_later_button_locations'               => $this->default_pay_later_button_locations,
			'pay_later_messaging_locations'            => $this->default_pay_later_messaging_locations,
			'brand_name'                               => get_bloginfo( 'name' ),
			'dcc_gateway_title'                        => $this->default_dcc_gateway_title,
			'dcc_gateway_description'                  => __(
				'Pay with your credit card.',
				'woocommerce-paypal-payments'
			),
		);
		foreach ( $defaults as $key => $value ) {
			if ( isset( $this->settings[ $key ] ) ) {
				continue;
			}
			$this->settings[ $key ] = $value;
		}

//        $market         = isset($_GET['market'])?$_GET['market']:false;
//
//        if( isset( $_COOKIE['market'] ) && $market === false )
//        {
//            $market     = $_COOKIE['market'];
//        }
//
//        if( $market && $market == 'uk' ) {
//            $this->settings['merchant_email_sandbox'] = 'sales@globalparts.co.uk';
//            $this->settings['merchant_email_production'] = 'sales@globalparts.co.uk';
//            $this->settings['merchant_email'] = 'sales@globalparts.co.uk';
//
//            $this->settings['merchant_id_sandbox'] = '63KDSQJ7HJYWN';
//            $this->settings['merchant_id_production'] = '63KDSQJ7HJYWN';
//            $this->settings['merchant_id'] = '63KDSQJ7HJYWN';
//
//            $this->settings['client_id_sandbox'] = 'AV266WnuPTjz2lzUCmR7TQbzurghF8CKxVnnhsiqUSZNURKPt4MueIyyS3OTRAwhpmYImzr5LjGwz-Q4';
//            $this->settings['client_id_production'] = 'AV266WnuPTjz2lzUCmR7TQbzurghF8CKxVnnhsiqUSZNURKPt4MueIyyS3OTRAwhpmYImzr5LjGwz-Q4';
//            $this->settings['client_id'] = 'AV266WnuPTjz2lzUCmR7TQbzurghF8CKxVnnhsiqUSZNURKPt4MueIyyS3OTRAwhpmYImzr5LjGwz-Q4';
//
//            $this->settings['client_secret_sandbox'] = 'EAithQWfLR-EdAqGwR0q7Iq3ZkBFsSZS4WkBdf9k7k4whTqADtRMcHfGYnvDJFuxA1DJAVqggt_qiXNZ';
//            $this->settings['client_secret_production'] = 'EAithQWfLR-EdAqGwR0q7Iq3ZkBFsSZS4WkBdf9k7k4whTqADtRMcHfGYnvDJFuxA1DJAVqggt_qiXNZ';
//            $this->settings['client_secret'] = 'EAithQWfLR-EdAqGwR0q7Iq3ZkBFsSZS4WkBdf9k7k4whTqADtRMcHfGYnvDJFuxA1DJAVqggt_qiXNZ';
//        }

        $this->settings['merchant_email_sandbox'] = 'sales@globalparts.co.uk';
        $this->settings['merchant_email_production'] = 'sales@globalparts.co.uk';
        $this->settings['merchant_email'] = 'sales@globalparts.co.uk';

        $this->settings['merchant_id_sandbox'] = '2HUEUJTCBD4H2';
        $this->settings['merchant_id_production'] = '2HUEUJTCBD4H2';
        $this->settings['merchant_id'] = '2HUEUJTCBD4H2';

        $this->settings['client_id_sandbox'] = 'AZZjjgueRBKik67-VMWxevs1x4cIKKVSxq5nGU3jPNH0gZSsUy6XYmoXn9V-UmQP57ZH0S_6fSRzoDWU';
        $this->settings['client_id_production'] = 'AZZjjgueRBKik67-VMWxevs1x4cIKKVSxq5nGU3jPNH0gZSsUy6XYmoXn9V-UmQP57ZH0S_6fSRzoDWU';
        $this->settings['client_id'] = 'AZZjjgueRBKik67-VMWxevs1x4cIKKVSxq5nGU3jPNH0gZSsUy6XYmoXn9V-UmQP57ZH0S_6fSRzoDWU';

        $this->settings['client_secret_sandbox'] = 'EBZbR2EDqwUm1ICTX9QNpeB3uFFjs59R673fL1d-H4Y9dM4cdTAbYPbKlNZYXCNh4SgIGewHGzgpezN4';
        $this->settings['client_secret_production'] = 'EBZbR2EDqwUm1ICTX9QNpeB3uFFjs59R673fL1d-H4Y9dM4cdTAbYPbKlNZYXCNh4SgIGewHGzgpezN4';
        $this->settings['client_secret'] = 'EBZbR2EDqwUm1ICTX9QNpeB3uFFjs59R673fL1d-H4Y9dM4cdTAbYPbKlNZYXCNh4SgIGewHGzgpezN4';

		return true;
	}
}
