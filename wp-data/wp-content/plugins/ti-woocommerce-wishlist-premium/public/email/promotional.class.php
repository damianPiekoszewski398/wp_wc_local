<?php
/**
 * Wishlist Promotional create email
 *
 * @since             1.0.0
 * @package           TInvWishlist\Public
 * @subpackage          Email
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Wishlist Promotional create email
 */
class TInvWL_Public_Email_Promotional extends WC_Email {

	/**
	 * Content with HTML tags
	 *
	 * @var string
	 */
	public $content;

	/**
	 * Content without HTML tags
	 *
	 * @var string
	 */
	public $content_plain;

	/**
	 * Promotional Product
	 *
	 * @var \WC_Product_Simple
	 */
	public $product;

	/**
	 * Recipient user
	 *
	 * @var \WP_User
	 */
	public $user;

	/**
	 * Wishlists in which the product is be found, with HTML tags
	 *
	 * @var string
	 */
	public $wishlists;

	/**
	 * Wishlists in which the product is be found, without HTML tags
	 *
	 * @var string
	 */
	public $wishlists_plain;

	/**
	 * Link a first wishlist
	 *
	 * @var string
	 */
	public $wishlists_first;

	/**
	 * Coupon
	 *
	 * @var \WC_Coupon
	 */
	public $coupon;

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
	function __construct( $plugin_name, $version ) {
		$this->_name    = $plugin_name;
		$this->_version = $version;

		$this->settings_class = new TInvWL_Public_Email_Data_Promotional();

		$this->load_data();

		$this->set_templates();

		parent::__construct();
		add_filter( 'woocommerce_email_get_option', array( $this, 'get_option_tinvwl' ), 10, 4 );
		add_filter( 'woocommerce_email_enabled_' . $this->id, array( $this, 'enabled_tinvwl' ), 10, 1 );
	}


	/**
	 * Set template email
	 */
	function set_templates() {
		$emailtemplate = tinv_template_email( get_class( $this ) );
		$this->set_template( $emailtemplate );
	}

	/**
	 * Set template email
	 *
	 * @param string $emailtemplate Email template name.
	 */
	function set_template( $emailtemplate = '' ) {
		$this->template_html  = $this->loadtemplates( $this->template_name, $emailtemplate, false );
		$this->template_plain = $this->loadtemplates( $this->template_name, $emailtemplate, true );
	}

	/**
	 * Get current template path
	 *
	 * @param string $template Name template.
	 * @param string $emailtemplate Name skin template.
	 * @param boolean $plain Plain or HTML template?.
	 *
	 * @return string
	 */
	function loadtemplates( $template, $emailtemplate, $plain = false ) {
		$curtemplate   = tinv_template();
		$template_name = 'emails' . DIRECTORY_SEPARATOR . ( $plain ? 'plain' . DIRECTORY_SEPARATOR : '' ) . $template . $emailtemplate . '.php';
		if ( ! empty( $curtemplate ) ) {
			if ( file_exists( TINVWL_PATH . implode( DIRECTORY_SEPARATOR, array(
					'templates',
					$curtemplate,
					$template_name,
				) ) ) ) {
				return $template_name;
			}
		}
		if ( file_exists( TINVWL_PATH . implode( DIRECTORY_SEPARATOR, array( 'templates', $template_name ) ) ) ) {
			return $template_name;
		}

		return 'emails' . DIRECTORY_SEPARATOR . ( $plain ? 'plain' . DIRECTORY_SEPARATOR : '' ) . $template . '.php';
	}

	/**
	 * Update status email
	 *
	 * @param boolean $value Woocommerce status.
	 *
	 * @return boolean
	 */
	function enabled_tinvwl( $value ) {
		$option_name = str_replace( $this->_name . '_', '', $this->id );
		$_value      = tinv_get_option( $option_name, 'enabled' );
		if ( is_null( $_value ) ) {
			return $value;
		}

		return $_value;
	}

	/**
	 * It replaces the value to the value of the plugin
	 *
	 * @param mixed $value Set value.
	 * @param object $_this Object for validation id.
	 * @param mixed $_value New Value.
	 * @param string $key key field.
	 *
	 * @return mixed
	 */
	function get_option_tinvwl( $value, $_this, $_value, $key ) {
		if ( $this->id === $_this->id ) {
			$option_name = str_replace( $this->_name . '_', '', $this->id );
			$_value      = tinv_get_option( $option_name, $key );
			if ( is_null( $_value ) ) {
				return $value;
			}
			if ( is_bool( $_value ) ) {
				$_value = $_value ? 'yes' : 'no';
			}

			return $_value;
		}

		return $value;
	}

	/**
	 * Set email defaults
	 */
	function load_data() {

		$this->id          = $this->settings_class->id;
		$this->title       = $this->settings_class->title;
		$this->description = $this->settings_class->description;

		$this->heading = $this->settings_class->heading;
		$this->subject = $this->settings_class->subject;


		$this->template_name = $this->settings_class->template_name;


		// Trigger on new paid orders.
		add_action( 'tinvwl_send_promotional', array( $this, 'trigger' ), 10, 4 );

		// This sets the recipient to the settings defined below in init_form_fields().
		$this->customer_email = true;
		$this->manual         = true;

		$this->wishlists       = '';
		$this->wishlists_plain = '';
		$this->coupon          = null;
	}


	/**
	 * Run method send mail
	 *
	 * @param object $product Promotion product.
	 * @param integer $user_id User ID Promotional.
	 * @param array $wishlists An array of wishlists in which the product is be found.
	 * @param string $coupon Personal coupon code.
	 *
	 * @return boolean
	 */
	function trigger( $product, $user_id, $wishlists = array(), $coupon = null ) {
		if ( empty( $product ) || empty( $user_id ) ) {
			return false;
		}

		wc()->frontend_includes();

		$this->heading = $this->get_option( 'heading' );
		$this->subject = $this->get_option( 'subject' );

		$this->content       = apply_filters( 'tinvwl_prepare_promotional_content', $this->get_option( 'content' ) );
		$this->content_plain = apply_filters( 'tinvwl_prepare_promotional_content_plain', $this->get_option( 'content_plain' ) );

		// Prepare Product.
		$this->product = $product;

		// Prepare Coupon.
		if ( ! empty( $coupon ) ) {
			$this->coupon = new WC_Coupon( $coupon );
		}

		// Prepare User.
		$user                  = get_user_by( 'id', $user_id );
		$notification_settings = get_user_meta( $user_id, '_tinvwl_notifications', true );
		$global_notifications  = tinv_get_option( 'global_notifications', 'enable_notifications' );
		$notifications_allowed = ( isset( $notification_settings['promotional'] ) && ! empty( $notification_settings['promotional'] ) && 'unsubscribed' === $notification_settings['promotional'] ) || ( ! isset( $notification_settings['promotional'] ) && ! $global_notifications ) ? false : true;

		if ( ! $user || ! $user->exists() || ! $notifications_allowed ) {
			return false;
		}
		$this->user      = $user;
		$this->recipient = $user->user_email;

		// Prepare Wishlists.
		$this->wishlists_first = '';
		if ( ! empty( $wishlists ) ) {
			$wl        = new TInvWL_Wishlist( $this->_name );
			$wishlists = $wl->get( array(
				'ID' => $wishlists,
			) );
			if ( ! empty( $wishlists ) ) {
				$wishlists_plain = array();
				foreach ( $wishlists as &$wishlist ) {
					$link = tinv_url_wishlist( $wishlist['share_key'] );
					if ( empty( $this->wishlists_first ) ) {
						$this->wishlists_first = $link;
					}
					$wishlists_plain[] = sprintf( '"%s" Link: %s', $wishlist['title'], esc_url( $link ) );
					$wishlist          = sprintf( '<a href="%s">%s</a>', esc_url( $link ), esc_html( $wishlist['title'] ) );
				}
				$this->wishlists_plain = implode( "\n\n", $wishlists_plain );
				$this->wishlists       = '<ul><li>' . implode( '</li> <li>', $wishlists ) . '</li></ul>';
			}
		}

		//Placeholders
		$this->placeholders['{user_name}']       = $this->user->user_login;
		$this->placeholders['{user_email}']      = $this->user->user_email;
		$this->placeholders['{user_first_name}'] = $this->user->billing_first_name;
		$this->placeholders['{user_last_name}']  = $this->user->billing_last_name;
		$this->placeholders['{company}']         = $this->user->billing_company;

		$price_func = 'get_price_html';

		$display_regular_price = $display_regular_price_plain = wc_get_price_to_display( $this->product, array( 'price' => $this->product->get_regular_price() ) );
		$display_sale_price    = $display_sale_price_plain = wc_get_price_to_display( $this->product, array( 'price' => $this->product->get_sale_price() ) );
		$display_regular_price = wc_price( $display_regular_price );
		$display_sale_price    = wc_price( $display_sale_price );
		if ( (float) $this->product->get_price() > 0 ) {
			if ( $this->product->is_on_sale() && $this->product->get_regular_price() ) {
				$display_regular_price = '<strike>' . $display_regular_price . '</strike>';
			}
		} elseif ( (float) $this->product->get_price() == 0 ) { // WPCS: loose comparison ok.
			if ( $this->product->is_on_sale() && $this->get_regular_price() ) {
				$display_regular_price = '<strike>' . $display_regular_price . '</strike>';
				$display_sale_price    = __( 'Free!', 'ti-woocommerce-wishlist-premium' );
			} else {
				$display_regular_price = '<span class="amount">' . __( 'Free!', 'ti-woocommerce-wishlist-premium' ) . '</span>';
			}
		}
		if ( $display_regular_price_plain === $display_sale_price_plain ) {
			$display_sale_price_plain = '';
		}
		if ( $display_regular_price === $display_sale_price ) {
			$display_sale_price = '';
		}
		$this->placeholders['{product_name}']           = is_callable( array(
			$this->product,
			'get_name'
		) ) ? $this->product->get_name() : $this->product->get_title();
		$this->placeholders['{product_name_with_url}']  = sprintf( '<a href="%s" style="text-decoration:none;">%s</a>', $this->product->get_permalink(), is_callable( array(
			$this->product,
			'get_name'
		) ) ? $this->product->get_name() : $this->product->get_title() );
		$this->placeholders['{product_price}']          = $this->product->$price_func();
		$this->placeholders['{product_price_regular}']  = $display_regular_price;
		$this->placeholders['{product_price_sale}']     = $display_sale_price;
		$this->placeholders['{product_image}']          = apply_filters( 'tinvwl_email_wishlist_item_thumbnail', '<img src="' . ( $this->product->get_image_id() ? current( wp_get_attachment_image_src( $this->product->get_image_id(), 'thumbnail' ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'ti-woocommerce-wishlist-premium' ) . '" width="180" />' );
		$this->placeholders['{product_image_with_url}'] = sprintf( '<a href="%s" style="text-decoration:none;">%s</a>', $this->product->get_permalink(), $this->product->get_image() );
		$this->placeholders['{product_url}']            = $this->product->get_permalink();
		$this->placeholders['{product_in_wishlists}']   = $this->wishlists;
		$this->placeholders['{wishlist_with_product}']  = $this->placeholders['{url_wishlist_with_product}'] = $this->wishlists_first;
		$this->placeholders['{coupon_code}']            = empty( $this->coupon ) ? '' : $this->coupon->get_code();
		$this->placeholders['{coupon_amount}']          = empty( $this->coupon ) ? '' : $this->coupon->get_amount();
		$this->placeholders['{coupon_value}']           = empty( $this->coupon ) ? '' : $this->coupon->get_discount_amount( $this->product->get_price() );

		// Allow modify placeholders from 3rd party code.
		$this->placeholders = apply_filters( 'tinvwl_email_wishlist_placeholders', $this->placeholders, $this );

		$result = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		if ( is_wp_error( $result ) ) {
			do_action( 'tinvwl_send_promotional_error', $this->get_recipient(), $this->user, $this->product, $result->get_error_message() );
		} else {
			do_action( 'tinvwl_send_promotional_successfully', $this->get_recipient(), $this->user, $this->product );
		}
	}

	/**
	 * Get content html function
	 *
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		tinv_wishlist_template( $this->template_html, apply_filters( 'tinvwl_promotional_email_data_template_html', array(
			'email_heading' => $this->get_heading(),
			'blogname'      => $this->get_blogname(),
			'content'       => $this->format_string( $this->content ),
			'sent_to_admin' => true,
			'plain_text'    => false,
			'email'         => $this,
		) ) );

		return ob_get_clean();
	}

	/**
	 * Get content plain function
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		tinv_wishlist_template( $this->template_plain, apply_filters( 'tinvwl_promotional_email_data_template_plain', array(
			'email_heading' => $this->get_heading(),
			'blogname'      => $this->get_blogname(),
			'content'       => $this->format_string( $this->content_plain ),
			'sent_to_admin' => true,
			'plain_text'    => true,
			'email'         => $this,
		) ) );

		return ob_get_clean();
	}

	/**
	 * Save value to plugin
	 */
	function process_admin_options() {
		parent::process_admin_options();

		$option_name = str_replace( $this->_name . '_', '', $this->id );
		$post_data   = $this->get_post_data();

		foreach ( $this->get_form_fields() as $key => $field ) {
			try {
				$value = $this->get_field_value( $key, $field, $post_data );
				if ( 'checkbox' === $this->get_field_type( $field ) ) {
					$value = 'yes' === $value;
				}
				tinv_update_option( $option_name, $key, $value );
			} catch ( Exception $e ) {
				$this->add_error( $e->getMessage() );
			}
		}
		$option_name = str_replace( $this->_name . '_', '', $this->id );
		tinv_update_option( $option_name . '_tmp', '', array() );
	}

	/**
	 * Initialise Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = $this->settings_class->form_fields;
	}
}
