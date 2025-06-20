<?php
/**
 * Admin products page class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Admin product page class
 */
class TInvWL_Admin_Product extends TInvWL_Admin_BaseSection {

	/**
	 * Priority for admin menu
	 *
	 * @var integer
	 */
	public $priority = 10;

	/**
	 * Menu array
	 *
	 * @return array
	 */
	function menu() {
		return array(
			'title'      => __( 'Product Analytics', 'ti-woocommerce-wishlist-premium' ),
			'method'     => array( $this, '_print_' ),
			'slug'       => 'product',
			'capability' => 'tinvwl_product_analytics',
			'roles'      => array( 'administrator', 'shop_manager' ),
		);
	}

	/**
	 * General page products
	 *
	 * @param integer $id Id parameter.
	 * @param string $cat Category parameter.
	 */
	function _print_general( $id = 0, $cat = '' ) {
		$data = array(
			'_header' => __( 'Product Analytics', 'ti-woocommerce-wishlist-premium' ),
			'table'   => new TInvWL_Admin_Product_Table( $this->_name, $this->_version ),
		);
		$data = apply_filters( 'tinvwl_product_general', $data );
		TInvWL_View::render( 'product', $data );
	}

	/**
	 * Print users wishlists by product
	 */
	function _print_product_users() {
		$get     = filter_input_array( INPUT_GET, array(
			'product_id'   => FILTER_VALIDATE_INT,
			'variation_id' => FILTER_VALIDATE_INT,
		) );
		$wlp     = new TInvWL_Product( array() );
		$product = $wlp->product_data( $get['product_id'], $get['variation_id'] );

		$header         = '';
		$variation_data = $product->is_type( 'variation' ) ? wc_get_product_variation_attributes( $product->get_id() ) : array();
		if ( ! empty( $variation_data ) ) {
			$variation_data = implode( ', ', $variation_data );
			$header         = sprintf( __( 'Popular Product "%1$s (%2$s)"', 'ti-woocommerce-wishlist-premium' ), is_callable( array(
				$product,
				'get_name'
			) ) ? $product->get_name() : $product->get_title(), $variation_data );
		} else {
			$header = sprintf( __( 'Popular Product "%s"', 'ti-woocommerce-wishlist-premium' ), is_callable( array(
				$product,
				'get_name'
			) ) ? $product->get_name() : $product->get_title() );
		}

		$data = array(
			'_header' => $header,
			'table'   => new TInvWL_Admin_Product_UserTable( $product, $this->_name, $this->_version ),
		);
		$data = apply_filters( 'tinvwl_product_users', $data );
		TInvWL_View::render( 'product-users', $data );
	}

	/**
	 * Prepair send promotional email
	 */
	function _print_product_promotional() {
		$nonce       = filter_input( INPUT_GET, '_tinvwl_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$redirect_to = filter_input( INPUT_GET, 'redirect_to', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$data        = array_filter( filter_input_array( INPUT_GET, array(
			'product_id'   => FILTER_VALIDATE_INT,
			'variation_id' => FILTER_VALIDATE_INT,
		) ) );
		$back_link   = wp_get_referer();
		if ( empty( $back_link ) ) {
			$back_link = $this->admin_url( 'product', $redirect_to, $data );
		}
		if ( ! wp_verify_nonce( $nonce, sprintf( '%s-%s', $this->_name, 'promotional' ) ) ) {
			TInvWL_View::set_redirect( $back_link );

			return TInvWL_View::render( 'index' );
		}
		if ( 'users' == $redirect_to ) { // WPCS: loose comparison ok.
			$users = filter_input( INPUT_GET, 'user_id', FILTER_VALIDATE_INT, array(
				'flags' => FILTER_FORCE_ARRAY,
			) );
			$users = array_filter( (array) $users );
			if ( ! empty( $users ) ) {
				$data['user_id'] = $users;
			} else {
				TInvWL_View::set_error( __( 'Not Found Users for Promotional!', 'ti-woocommerce-wishlist-premium' ), 145 );
				TInvWL_View::set_redirect( $back_link );

				return TInvWL_View::render( 'index' );
			}
		}
		$hiden_fields   = array();
		$hiden_fields[] = TInvWL_Form::_text( array( 'name' => '_tinvwl_nonce', 'type' => 'hidden' ), $nonce );
		foreach ( $data as $key => $value ) {
			$hiden_fields[] = TInvWL_Form::_text( array( 'name' => $key, 'type' => 'hidden' ), $value );
		}

		$wlp     = new TInvWL_Product( array() );
		$product = $wlp->product_data( $data['product_id'], (integer) @$data['variation_id'] ); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
		if ( empty( $product ) ) {
			TInvWL_View::set_error( __( 'Product ID not found!', 'ti-woocommerce-wishlist-premium' ), 146 );
			TInvWL_View::set_redirect( $back_link );

			return TInvWL_View::render( 'index' );
		}

		global $wpdb;

		if ( ! tinv_get_option( 'general', 'multi' ) ) {
			$data['type'] = 'default';
		}
		if ( array_key_exists( 'user_id', $data ) ) {
			if ( ! empty( $data['user_id'] ) ) {
				$data['B`.`author'] = $data['user_id'];
			}
			unset( $data['user_id'] );
		}
		$data             = array_filter( $data );
		$data['external'] = false;
		$data['sql']      = 'SELECT `A`.*, `B`.`author` AS `user_id`,  GROUP_CONCAT(`A`.`wishlist_id`) AS `wishlist_id` FROM `{table}` AS `A` INNER JOIN `' . sprintf( '%s%s_%s', $wpdb->prefix, $this->_name, 'lists' ) . '` AS `B` ON `A`.`wishlist_id` = `B`.`ID` WHERE {where} GROUP BY `A`.`product_id`, `A`.`variation_id`, `B`.`author`;';
		$products         = $wlp->get( $data );

		if ( empty( $products ) ) {
			TInvWL_View::set_error( __( 'Product and Users for sending a promotion not found!', 'ti-woocommerce-wishlist-premium' ), 147 );
			TInvWL_View::set_redirect( $back_link );

			return TInvWL_View::render( 'index' );
		}

		$header         = '';
		$variation_data = $product->is_type( 'variation' ) ? wc_get_product_variation_attributes( $product->get_id() ) : array();
		if ( ! empty( $variation_data ) ) {
			$variation_data = implode( ', ', $variation_data );
			$header         = sprintf( __( 'Send Promotional Emails "%1$s (%2$s)"', 'ti-woocommerce-wishlist-premium' ), is_callable( array(
				$product,
				'get_name'
			) ) ? $product->get_name() : $product->get_title(), $variation_data );
		} else {
			$header = sprintf( __( 'Send Promotional Email "%s"', 'ti-woocommerce-wishlist-premium' ), is_callable( array(
				$product,
				'get_name'
			) ) ? $product->get_name() : $product->get_title() );
		}

		$data                 = array(
			'_header' => $header,
		);
		$form_fields          = array();
		$this->email_settings = TInvWL_Public_Email::instance();
		if ( array_key_exists( $this->_name . '_Public_Email_Promotional', (array) @$this->email_settings->parent_settings ) ) { // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
			$form_fields = $this->email_settings->parent_settings [ $this->_name . '_Public_Email_Promotional' ]->get_form_fields();
		}

		$email_type = tinv_get_option( 'promotional_email', 'email_type' );
		if ( empty( $email_type ) ) {
			$email_type = @$form_fields['email_type']['default']; // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
		}
		if ( empty( $email_type ) ) {
			$email_type = 'html';
		}
		$content       = array(
			(string) @$form_fields['content']['title'],
			// @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
			(string) @$form_fields['content']['description'],
			// @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
			tinv_get_option( 'promotional_email', 'content' ),
		);
		$content_plain = array(
			(string) @$form_fields['content_plain']['title'],
			// @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
			(string) @$form_fields['content_plain']['description'],
			// @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
			tinv_get_option( 'promotional_email', 'content_plain' ),
		);
		if ( empty( $content[2] ) ) {
			$content[2] = (string) @$form_fields['content']['default']; // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
		}
		if ( empty( $content_plain[2] ) ) {
			$content_plain[2] = (string) @$form_fields['content_plain']['default']; // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
		}

		$_coupons = get_posts( array( // @codingStandardsIgnoreLine WordPress.VIP.RestrictedFunctions.get_posts
			'post_type'      => 'shop_coupon',
			'post_status'    => 'publish',
			'posts_per_page' => - 1, // @codingStandardsIgnoreLine WordPress.VIP.PostsPerPage.posts_per_page
		) );
		$coupons  = array(
			'' => '',
		);
		foreach ( $_coupons as $_coupon ) {
			$coupons[ $_coupon->post_title ] = $_coupon->post_title;
		}
		unset( $_coupons );

		$sections = array(
			array(
				'id'         => 'content',
				'title'      => __( 'Content Promotional', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => false,
				'fields'     => array(
					array(
						'type'       => 'group',
						'id'         => 'content',
						'title'      => $content[0],
						'show_names' => true,
						'style'      => ( 'plain' === $email_type ? 'display:none;' : '' ),
					),
					array(
						'type'  => 'textarea',
						'name'  => 'content',
						'std'   => $content[2],
						'extra' => array( 'style' => 'height: 200px;' ),
					),
					array(
						'type' => 'html',
						'name' => 'content_label',
						'std'  => sprintf( '<p>%s</p><p>%s</p>', $content[1], TInvWL_Form::_button( 'content_button', __( 'View preview', 'ti-woocommerce-wishlist-premium' ), array(
							'class'     => $this->_name . '-preparepromo-preview button',
							'data-type' => 'content',
						) ) ),
					),
					array(
						'type'       => 'group',
						'id'         => 'content_plain',
						'title'      => $content_plain[0],
						'show_names' => true,
						'style'      => ( 'html' === $email_type ? 'display:none;' : '' ),
					),
					array(
						'type'  => 'textarea',
						'name'  => 'content_plain',
						'std'   => $content_plain[2],
						'extra' => array( 'style' => 'height: 200px;' ),
					),
					array(
						'type' => 'html',
						'name' => 'content_plain_label',
						'std'  => sprintf( '<p>%s</p><p>%s</p>', $content_plain[1], TInvWL_Form::_button( 'content_plain_button', __( 'View preview', 'ti-woocommerce-wishlist-premium' ), array(
							'class'     => $this->_name . '-preparepromo-preview button',
							'data-type' => 'content_plain',
						) ) ),
					),
				),
			),
			array(
				'id'         => 'coupon',
				'title'      => __( 'Coupon', 'ti-woocommerce-wishlist-premium' ),
				'show_names' => false,
				'style'      => ( 1 >= count( $coupons ) ? 'display:none;' : '' ),
				'fields'     => array(
					array(
						'type'       => 'group',
						'id'         => 'html',
						'title'      => __( 'Coupon', 'ti-woocommerce-wishlist-premium' ),
						'show_names' => true,
					),
					array(
						'type'    => 'select',
						'name'    => 'code',
						'std'     => '',
						'options' => $coupons,
					),
				),
			),
			array(
				'id'     => 'save_buttons',
				'class'  => 'only-button',
				'noform' => true,
				'fields' => array(
					array(
						'type'  => 'button_submit',
						'name'  => 'send_promo',
						'std'   => '<span><i class="ftinvwl ftinvwl-check"></i></span>' . __( 'Send Promotional Email', 'ti-woocommerce-wishlist-premium' ),
						'extra' => array( 'class' => 'tinvwl-btn split status-btn-ok' ),
					),
					array(
						'type' => 'html',
						'name' => 'sendpromo',
						'std'  => implode( '', $hiden_fields ),
					),
				),
			),
		);

		$view = new TInvWL_ViewSection( $this->_name, $this->_version );
		$view->load_data( $sections );
		$postdata = $view->post_form();
		if ( ! empty( $postdata ) && is_array( $postdata ) ) {

			if ( function_exists( 'WC' ) ) {
				WC()->mailer();
			}

			tinv_update_option( 'promotional_email_tmp', '', $postdata['content'] );
			add_filter( 'tinvwl_prepare_promotional_content', array( $this, 'product_promotional_content' ) );
			add_filter( 'tinvwl_prepare_promotional_content_plain', array(
				$this,
				'product_promotional_content_plain',
			) );

			foreach ( $products as $item ) {
				$user_id   = absint( $item['user_id'] );
				$wishlists = explode( ',', $item['wishlist_id'] );
				$coupon    = $postdata['coupon']['code'];
				if ( ! empty( $user_id ) ) {
					do_action( 'tinvwl_send_promotional', $product, $user_id, $wishlists, $coupon );
				}
			}
			TInvWL_View::set_redirect( $back_link );
		}
		$view->load_value( array(
			'content' => array_filter( (array) tinv_get_option( 'promotional_email_tmp', '' ) ),
		) );
		TInvWL_View::render( $view, $view->form_data( $data ) );
	}

	/**
	 * Prepare preview email
	 */
	function preview_email_presave() {
		$nonce = filter_input( INPUT_POST, '_tinvwl_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! wp_verify_nonce( $nonce, sprintf( '%s-%s', $this->_name, 'promotional' ) ) ) {
			wp_send_json( array() );
		}
		ob_start();
		$data             = array_filter( filter_input_array( INPUT_POST, array(
			'product_id'   => FILTER_VALIDATE_INT,
			'variation_id' => FILTER_VALIDATE_INT,
			'user_id'      => FILTER_VALIDATE_INT,
			'data_type'    => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'coupon-code'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		) ) );
		$data['_tinvwl_nonce'] = $nonce;
		$content          = filter_input_array( INPUT_POST, array(
			'content-content'       => FILTER_DEFAULT,
			'content-content_plain' => FILTER_DEFAULT,
		) );
		tinv_update_option( 'promotional_email_tmp', $data['data_type'], $content[ 'content-' . $data['data_type'] ] );
		switch ( $data['data_type'] ) {
			case 'content_plain':
				$data['data_type'] = 'plain';
				break;
			case 'content':
			default:
				$data['data_type'] = 'html';
		}
		ob_clean();
		wp_send_json( array(
			'url' => $this->admin_url( 'previewpromoemail', '', $data ),
		) );
	}

	/**
	 * Show preview email
	 */
	function preview_email() {
		$data = filter_input_array( INPUT_GET, array(
			'page'         => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'product_id'   => FILTER_VALIDATE_INT,
			'variation_id' => FILTER_VALIDATE_INT,
			'user_id'      => FILTER_VALIDATE_INT,
			'data_type'    => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'_tinvwl_nonce'     => FILTER_DEFAULT,
			'coupon-code'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		) );
		if ( ( isset( $data['page'] ) && $this->_name . '-previewpromoemail' !== $data['page'] ) || ! ( isset( $data['_tinvwl_nonce'] ) && wp_verify_nonce( $data['_tinvwl_nonce'], sprintf( '%s-%s', $this->_name, 'promotional' ) ) ) ) {
			return false;
		}
		tinv_update_option( 'promotional_email_tmp', 'email_type', $data['data_type'] );
		add_filter( 'tinvwl_prepare_promotional_email_type', array(
			$this,
			'product_promotional_email_type',
		) );
		$wlp     = new TInvWL_Product( array() );
		$product = $wlp->product_data( $data['product_id'], (integer) @$data['variation_id'] ); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
		if ( empty( $product ) ) {
			wp_die( esc_html__( 'Product ID not found!', 'ti-woocommerce-wishlist-premium' ) );
		}
		if ( function_exists( 'WC' ) ) {
			WC()->mailer();
		}
		$coupon = $data['coupon-code'];
		unset( $data['page'], $data['data_type'], $data['_tinvwl_nonce'], $data['coupon-code'] );

		global $wpdb;

		if ( ! tinv_get_option( 'general', 'multi' ) ) {
			$data['type'] = 'default';
		}
		if ( array_key_exists( 'user_id', $data ) ) {
			if ( ! empty( $data['user_id'] ) ) {
				$data['B`.`author'] = $data['user_id'];
			}
			unset( $data['user_id'] );
		}
		$data             = array_filter( $data );
		$data['external'] = false;
		$data['sql']      = 'SELECT `A`.*, `B`.`author` AS `user_id`,  GROUP_CONCAT(`A`.`wishlist_id`) AS `wishlist_id` FROM `{table}` AS `A` INNER JOIN `' . sprintf( '%s%s_%s', $wpdb->prefix, $this->_name, 'lists' ) . '` AS `B` ON `A`.`wishlist_id` = `B`.`ID` WHERE {where} GROUP BY `A`.`product_id`, `A`.`variation_id`, `B`.`author`;';
		$products         = $wlp->get( $data );

		if ( empty( $products ) ) {
			wp_die( esc_html__( 'Product and Users for sending a promotion not found!', 'ti-woocommerce-wishlist-premium' ) );
		}

		add_filter( 'tinvwl_prepare_promotional_content', array( $this, 'product_promotional_content' ) );
		add_filter( 'tinvwl_prepare_promotional_content_plain', array(
			$this,
			'product_promotional_content_plain',
		) );
		WC_Emails::instance();
		add_filter( 'woocommerce_mail_content', array( $this, 'preview_email_content' ) );
		$email_class = 'TInvWL_Public_Email_Promotional';
		if ( class_exists( $email_class ) ) {
			$email     = new $email_class( $this->_name, $this->_version );
			$templates = get_option( TINVWL_PREFIX . '-notification_template', array() );
			$template  = ( is_array( $templates ) && isset( $templates['TInvWL_Public_Email_Promotional'] ) ) ? $templates['TInvWL_Public_Email_Promotional'] : '';
			$email->set_template( $template );
			tinv_wishlist_template( str_replace( DIRECTORY_SEPARATOR . 'ti-', DIRECTORY_SEPARATOR . 'ti-preview', $email->template_html ) );
		}
		die();
	}

	/**
	 * Loading the temporary email type
	 *
	 * @param string $email_type Email type.
	 *
	 * @return string
	 */
	function product_promotional_email_type( $email_type ) {
		$new_email_type = tinv_get_option( 'promotional_email_tmp', 'email_type' );
		if ( ! empty( $new_email_type ) ) {
			return $new_email_type;
		}

		return $email_type;
	}

	/**
	 * Loading the temporary content
	 *
	 * @param string $content Content email.
	 *
	 * @return string
	 */
	function product_promotional_content( $content ) {
		$new_content  = tinv_get_option( 'promotional_email_tmp', 'content' );
		$search       = array(
			'#NSmain',
			'#NSbackground',
			'#NSbackgrcont',
			'#NStitle',
			'#NScontent',
			'{logo_text}',
			'{heading}',
			'{footer_text}',
			'{logo_image}',
			'{header_image}',
			'{header_image_pre}',
			'{header_image_post}',
		);
		$header_image = tinv_get_option( 'notifications_style', 'header_image' );
		$replace      = array(
			tinv_get_option( 'notifications_style', 'main' ),
			tinv_get_option( 'notifications_style', 'background' ),
			tinv_get_option( 'notifications_style', 'background_content' ),
			tinv_get_option( 'notifications_style', 'title' ),
			tinv_get_option( 'notifications_style', 'content' ),
			esc_html( tinv_get_option( 'notifications_style', 'logo_text' ) ),
			esc_html( tinv_get_option( 'promotional_email', 'heading' ) ),
			esc_html( tinv_get_option( 'notifications_style', 'footer_text' ) ),
			( ! tinv_get_option( 'notifications_style', 'current_logo' ) && tinv_get_option( 'notifications_style', 'logo' ) ) ? tinv_get_option( 'notifications_style', 'logo' ) : TINVWL_URL . 'assets/img/logo_heart.png',
			$header_image,
			empty( $header_image ) ? '<!--' : '',
			empty( $header_image ) ? '-->' : '',
		);

		$content = empty( $new_content ) ? $content : $new_content;
		$content = str_replace( $search, $replace, $content );

		return $content;
	}

	/**
	 * Loading the temporary content
	 *
	 * @param string $content Content email.
	 *
	 * @return string
	 */
	function product_promotional_content_plain( $content ) {
		$new_content = tinv_get_option( 'promotional_email_tmp', 'content_plain' );
		if ( ! empty( $new_content ) ) {
			return $new_content;
		}

		return $content;
	}

	/**
	 * Get email content for preview
	 *
	 * @param string $content Email content.
	 */
	function preview_email_content( $content ) {
		$data = filter_input_array( INPUT_GET, array(
			'data_type' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		) );
		if ( 'plain' === $data['data_type'] ) {
			echo '<pre>' . $content . '</pre>'; // WPCS: xss ok.
		} else {
			echo $content; // WPCS: xss ok.
		}
		die();
	}
}
