<?php

add_action( 'woocommerce_checkout_update_order_meta', 'wpdesk_checkout_vat_number_update_order_meta' );
/**
 * Save VAT Number in the order meta
 */
function wpdesk_checkout_vat_number_update_order_meta( $order_id ) {
    error_log( 'UPDATE ORDER: ' . $order_id );
    error_log( print_r( $_POST, true ) );
    
    if ( ! empty( $_POST['billingAddress']['companyTaxNumber'] ) ) {
        update_post_meta( $order_id, '_billing_nip', sanitize_text_field( $_POST['billingAddress']['companyTaxNumber'] ) );
    }
}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'wpdesk_vat_number_display_admin_order_meta', 10, 1 );
/**
 * Display VAT Number in order edit screen
 */
function wpdesk_vat_number_display_admin_order_meta( $order ) {
    echo '<p><strong>' . __( 'VAT Number', 'woocommerce' ) . ':</strong> ' . get_post_meta( $order->id, '_billing_nip', true ) . '</p>';
}

add_filter( 'woocommerce_email_order_meta_keys', 'wpdesk_vat_number_display_email' );
/**
 * VAT Number in emails
 */
function wpdesk_vat_number_display_email( $keys ) {
    $keys['VAT Number'] = '_billing_nip';
    return $keys;
}