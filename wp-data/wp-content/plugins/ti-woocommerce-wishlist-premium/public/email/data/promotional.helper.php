<?php
/**
 * Promotional email data email
 *
 * @since             1.9.2
 * @package           TInvWishlist\Public
 * @subpackage          Email
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Promotional create email
 */
class TInvWL_Public_Email_Data_Promotional {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	public $_name;

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $_version;

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	function __construct( $plugin_name = TINVWL_PREFIX, $version = TINVWL_VERSION ) {
		$this->_name    = $plugin_name;
		$this->_version = $version;
		$this->load_data();
		$this->init_form_fields();
	}

	/**
	 * Set email defaults
	 */
	function load_data() {
		$this->id          = $this->_name . '_promotional_email';
		$this->title       = __( 'Wishlist Promotional Email', 'ti-woocommerce-wishlist-premium' );
		$this->description = __( 'This email is sent to users to inform them about a promotion on a product that was added to their wishlist.', 'ti-woocommerce-wishlist-premium' );

		// These are the default heading and subject lines that can be overridden using the settings.
		$this->heading = __( 'There is a deal for you!', 'ti-woocommerce-wishlist-premium' );
		$this->subject = __( 'A product of your wishlist is on sale', 'ti-woocommerce-wishlist-premium' );

		// These define the locations of the templates that this email should use, we'll just use the new order template since this email is similar.
		$this->template_name = 'ti-promotional';
	}

	/**
	 * Initialise Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'       => array(
				'title'   => __( 'Enable/Disable', 'ti-woocommerce-wishlist-premium' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'ti-woocommerce-wishlist-premium' ),
				'default' => 'yes',
			),
			'subject'       => array(
				'title'       => __( 'Email Subject', 'ti-woocommerce-wishlist-premium' ),
				'type'        => 'text',
				'description' => sprintf( __( 'Defaults to <code>%s</code>', 'ti-woocommerce-wishlist-premium' ), $this->subject ),
				'default'     => $this->subject,
				'desc_tip'    => true,
			),
			'heading'       => array(
				'title'       => __( 'Email Heading', 'ti-woocommerce-wishlist-premium' ),
				'type'        => 'text',
				'description' => sprintf( __( 'Defaults to <code>%s</code>', 'ti-woocommerce-wishlist-premium' ), $this->heading ),
				'default'     => $this->heading,
				'desc_tip'    => true,
			),
			'content'       => array(
				'title'       => __( 'Email Content', 'ti-woocommerce-wishlist-premium' ),
				'type'        => 'textarea',
				'description' => __( 'This field lets you modify the main content of the HTML email. You can use the following placeholder: <code>{user_name}</code>, <code>{user_email}</code>, <code>{user_first_name}</code>, <code>{user_last_name}</code>, <code>{company}</code>, <code>{product_image}</code>, <code>{product_image_with_url}</code>, <code>{product_in_wishlists}</code>, <code>{product_name}</code>, <code>{product_name_with_url}</code>, <code>{product_price}</code>, <code>{product_price_regular}</code>, <code>{product_price_sale}</code>, <code>{product_url}</code>, <code>{coupon_code}</code>, <code>{coupon_amount}</code>, <code>{url_wishlist_with_product}</code>.', 'ti-woocommerce-wishlist-premium' ),
				'default'     => '<p>Hi {user_name}</p>
<p>A product of your wishlist is on sale!</p>
<p>{product_in_wishlists}</p>
<p>
    <table>
        <tr>
            <td>{product_image}</td>
            <td>{product_name}</td>
            <td>{product_price}</td>
        </tr>
    </table>
</p>
<p>Use this coupon code <b><a href="{wishlist_with_product}" >{coupon_code}</a></b> to obtain a wonderful discount!</p>',
				'css'         => 'width:80%;height:250px;resize:vertical;',
			),
			'content_plain' => array(
				'title'       => __( 'Email Content Plain', 'ti-woocommerce-wishlist-premium' ),
				'type'        => 'textarea',
				'description' => __( 'This field lets you modify the main content of the HTML email. You can use the following placeholder: <code>{user_name}</code>, <code>{user_email}</code>, <code>{user_first_name}</code>, <code>{user_last_name}</code>, <code>{company}</code>, <code>{product_image}</code>, <code>{product_in_wishlists}</code>, <code>{product_name}</code>, <code>{product_price}</code>, <code>{product_price_regular}</code>, <code>{product_price_sale}</code>, <code>{product_url}</code>, <code>{coupon_code}</code>, <code>{coupon_amount}</code>, <code>{url_wishlist_with_product}</code>.', 'ti-woocommerce-wishlist-premium' ),
				'default'     => 'Hi {user_name}
A product of your wishlist is on sale!

{product_name} {product_price}

Visit {wishlist_with_product} and use this coupon code
{coupon_code}
to obtain a wonderful discount!',
				'css'         => 'width:80%;height:250px;resize:vertical;',
			),
			'email_type'    => array(
				'title'       => __( 'Email type', 'ti-woocommerce-wishlist-premium' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'ti-woocommerce-wishlist-premium' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options(),
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Email type options.
	 *
	 * @return array
	 */
	public function get_email_type_options() {
		$types = array( 'plain' => __( 'Plain text', 'ti-woocommerce-wishlist-premium' ) );

		if ( class_exists( 'DOMDocument' ) ) {
			$types['html']      = __( 'HTML', 'ti-woocommerce-wishlist-premium' );
			$types['multipart'] = __( 'Multipart', 'ti-woocommerce-wishlist-premium' );
		}

		return $types;
	}
}
