<?php

defined( 'ABSPATH' ) || exit;

class Gp_Register_Form {

    protected static $instance;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function __construct() {
        add_action( 'woocommerce_register_form_start', array( $this, 'woo_extra_register_fields' ) );
        add_action( 'woocommerce_created_customer', array( $this, 'woo_save_extra_register_fields' ) );
    }

    public function woo_save_extra_register_fields( $customer_id ) {
        if ( isset( $_POST['billing_first_name'] ) ) {
            update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
            update_user_meta( $customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']) );
        }
        if ( isset( $_POST['billing_last_name'] ) ) {
            update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
            update_user_meta( $customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']) );
        }
        if ( isset( $_POST['billing_company'] ) ) {
            update_user_meta( $customer_id, 'billing_company', sanitize_text_field( $_POST['billing_company'] ) );
        }
        if ( isset( $_POST['billing_tax_number'] ) ) {
            update_user_meta( $customer_id, 'billing_tax_number', sanitize_text_field( $_POST['billing_tax_number'] ) );
        }
        if ( isset( $_POST['billing_phone'] ) ) {
            update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
        }

        $is_company = isset( $_POST['is_company'] ) ? '1' : '0';
        $is_workshop = isset( $_POST['is_workshop'] ) ? '1' : '0';
        update_user_meta( $customer_id, 'is_company', $is_company );
        update_user_meta( $customer_id, 'is_workshop', $is_workshop );
    }

    public function woo_extra_register_fields() {
?>
        <p class="form-row form-row-wide">
            <label for="reg_is_company"><?php _e( 'Is company?', 'woocommerce' ); ?></label>
            <input type="checkbox" class="input-text" name="is_company" id="reg_is_company" value="1" />
        </p>
        <p class="form-row form-row-wide">
            <label for="reg_billing_company"><?php _e( 'Company name', 'woocommerce' ); ?></label>
            <input type="text" class="input-text" name="billing_company" id="reg_billing_company" value="<?php esc_attr_e( $_POST['billing_company'] ); ?>" />
        </p>
        <p class="form-row form-row-wide">
            <label for="reg_billing_company"><?php _e( 'Tax number', 'woocommerce' ); ?></label>
            <input type="text" class="input-text" name="billing_tax_number" id="reg_billing_tax_number" value="<?php esc_attr_e( $_POST['billing_tax_number'] ); ?>" />
        </p>
        <p class="form-row form-row-first">
            <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?></label>
            <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
        </p>
        <p class="form-row form-row-last">
            <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?></label>
            <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
        </p>
        <p class="form-row form-row-wide">
            <label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?></label>
            <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php esc_attr_e( $_POST['billing_phone'] ); ?>" />
        </p>
        <div class="clear"></div>
<?php
    }
}