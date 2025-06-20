<?php
add_action( 'woocommerce_api_loaded', function(){
    include_once( 'bl-api-update-order.php' );
});

add_action(
    'rest_api_init',
    function () {
        register_rest_route(
            'bl/v1',
            '/update-order',
            array(
                'methods'             => 'GET',
                'callback'            => function ( $requested_data ) {
                    $requested_query_data = $requested_data->get_query_params();

                    $order_id = isset($requested_query_data['order_id'])?
                        intval($requested_query_data['order_id']):false;
                    $shipment_tracking_number = isset($requested_query_data['shipment_tracking_number'])?
                        sanitize_text_field($requested_query_data['shipment_tracking_number']):false;
                    $shipment_tracking_link = isset($requested_query_data['shipment_tracking_link'])?
                        sanitize_url($requested_query_data['shipment_tracking_link']):false;
                    $courier = isset($requested_query_data['courier'])?
                        sanitize_text_field($requested_query_data['courier']):false;
                    $invoice = isset($requested_query_data['invoice'])?
                        sanitize_url($requested_query_data['invoice']):false;
                    $receipt = isset($requested_query_data['receipt'])?
                        sanitize_url($requested_query_data['receipt']):false;
                    $email = isset($requested_query_data['email'])?
                        sanitize_email($requested_query_data['email']):false;

                    if (
                            !$order_id          ||  !$email         ||
                            empty($order_id)    ||  empty($email)
                    ) {
                        return new WP_Error( 'bad_request', 'No required parameters given.', array( 'status' => 500 ) );
                    }

                    $wc_order = wc_get_order( $order_id );

                    if (!$wc_order) {
                        return new WP_Error( 'bad_request', 'Order (' . $order_id . ') not exists!', array( 'status' => 500 ) );
                    }

                    $wc_order_email = $wc_order->get_billing_email();

                    if ($wc_order_email != $email) {
                        return new WP_Error( 'bad_request', 'E-mail address (' . $email . ') not match!', array( 'status' => 500 ) );
                    }

                    if ($shipment_tracking_number) {
                        update_post_meta($wc_order->get_id(), '_aftership_tracking_number', $shipment_tracking_number);
                        update_post_meta($wc_order->get_id(), '_tracking_number', $shipment_tracking_number);
                    }

                    if ($shipment_tracking_link && $shipment_tracking_number && $courier) {
                        $tracking_info = [
                            $shipment_tracking_number => [
                                'courierService' => $courier
                            ]
                        ];

                        $_wc_shipment_tracking_items = [
                            [
                                'tracking_number' => $shipment_tracking_number,
                                'tracking_provider' => $courier,
                                'custom_tracking_provider' => $courier,
                                'custom_tracking_link' => $shipment_tracking_link,
                                'tracking_id' => '',
                                'date_shipped' => time()
                            ]
                        ];

                        update_post_meta($wc_order->get_id(), 'tracking_info', $tracking_info);
                        update_post_meta($wc_order->get_id(), '_wc_shipment_tracking_items', $_wc_shipment_tracking_items);
                    }

                    if ($invoice || $receipt) {
                        $email_oc = new WC_Email_Customer_Completed_Order();

                        $bl_pdf_url = ($invoice?$invoice:$receipt);

                        add_filter( 'woocommerce_email_attachments', function( $attachments, $email_id, $order ) use ( $bl_pdf_url, $email ) {
                            $email = 'lukasz.sadowski26492@gmail.com';
                            $bl_pdf_url = 'https://orders-g.baselinker.com/12668147/8v0th18igk/receipt';

                            return bl_add_invoice_to_email( $attachments, $email_id, $order, $bl_pdf_url, $email );
                        }, 10, 3 );

                        $email_oc->trigger($order_id);
                    }

                    return array(
                        'data'    => array(
                            'status' => 200,
                        ),
                        'message' => __( 'Order details saved successfully.', 'globalparts' ),
                    );
                },
                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

function bl_add_invoice_to_email( $attachments, $email_id, $order, $bl_pdf_url, $email) {
    $data = array(
        'verification_data' => $email,
        'verify_access' => 'Pobierz',
    );
    $ch = curl_init();

    $referer = str_replace( ['receipt', 'invoice'], '', $bl_pdf_url );

    curl_setopt($ch, CURLOPT_URL, $bl_pdf_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, '');
    curl_setopt($ch, CURLOPT_COOKIEJAR, '');
    curl_setopt($ch,CURLOPT_HTTPHEADER, array(
        "Accept: */*",
        "Connection: keep-alive",
        "Content-Type: application/x-www-form-urlencoded",
        "Referer: $referer",
        "Origin: https://orders-g.baselinker.com"
    ));

    $response = curl_exec($ch);

    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);

    curl_close($ch);

    if ($body) {
        // Tworzymy ścieżkę do pliku PDF w folderze tymczasowym
        $tempFolder = sys_get_temp_dir();
        $filename = uniqid() . '.pdf';

        if (preg_match('/filename=([^\r\n;]+)/', $header, $filename_matches)) {
            $filename = preg_replace('/[^a-zA-Z0-9.-]/', '-', $filename_matches[1]);
        }

        $filePath = $tempFolder . DIRECTORY_SEPARATOR . $filename;

        // Zapisujemy odpowiedź (plik PDF) do pliku
        file_put_contents($filePath, $body);

        if ( file_exists( $filePath ) ) {
            $attachments[] = $filePath;
        }
    }

    return $attachments;
}
