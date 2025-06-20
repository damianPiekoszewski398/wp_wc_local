<?php
/*
Plugin Name: GlobalParts
Plugin URI: https://magazyn.globalparts.co.uk
description: Connect to Globalparts WMS & other additions
Version: 1.0
Author: Fabryka w chmurach
Author URI: https://fabrykawchmurach.pl
License: GPL2
*/

use Automattic\WooCommerce\Blocks\Payments\PaymentResult;
use Automattic\WooCommerce\Blocks\Payments\PaymentContext;

use SimpleJWTLogin\Libraries\JWT\JWT;
use SimpleJWTLogin\Helpers\Jwt\JwtKeyFactory;
use SimpleJWTLogin\Modules\SimpleJWTLoginSettings;
use SimpleJWTLogin\Modules\WordPressDataInterface;
use SimpleJWTLogin\Modules\WordPressData;

require_once __DIR__ . '/inc/class-gp-external-image.php';
require_once __DIR__ . '/inc/class-gp-register-form.php';
require_once __DIR__ . '/inc/class-gp-garage.php';

require_once __DIR__ . '/inc/tax_number.php';

require_once __DIR__ . '/bl-api-update-order.php';

Gp_External_Image::get_instance();
Gp_Register_Form::get_instance();

function woocommerce_product_custom_fields()
{
    $args = array(
        'id' => 'woocommerce_part_number',
        'label' => __('Part number', 'cwoa'),
    );
    woocommerce_wp_text_input($args);
    $args = array(
        'id' => 'woocommerce_reference_number',
        'label' => __('Reference number', 'cwoa'),
    );
    woocommerce_wp_text_input($args);
    $args = array(
        'id' => '_title_pl',
        'label' => __('Product title (PL)', 'cwoa'),
    );
    woocommerce_wp_text_input($args);
    $args = array(
        'id' => '_title_de',
        'label' => __('Product title (DE)', 'cwoa'),
    );
    woocommerce_wp_text_input($args);
}

add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');

function save_woocommerce_product_custom_fields($post_id)
{
    $product = wc_get_product($post_id);
    $custom_fields_woocommerce_part_number = isset($_POST['woocommerce_part_number']) ? $_POST['woocommerce_part_number'] : '';
    $custom_fields_woocommerce_reference_number = isset($_POST['woocommerce_reference_number']) ? $_POST['woocommerce_reference_number'] : '';
    $custom_fields_title_pl = isset($_POST['_title_pl']) ? $_POST['_title_pl'] : '';
    $custom_fields_title_de = isset($_POST['_title_de']) ? $_POST['_title_de'] : '';
    $product->update_meta_data('woocommerce_part_number', sanitize_text_field($custom_fields_woocommerce_part_number));
    $product->update_meta_data('woocommerce_reference_number', sanitize_text_field($custom_fields_woocommerce_reference_number));
    $product->update_meta_data('_title_pl', sanitize_text_field($custom_fields_title_pl));
    $product->update_meta_data('_title_de', sanitize_text_field($custom_fields_title_de));
    $product->save();
}
add_action('woocommerce_process_product_meta', 'save_woocommerce_product_custom_fields');

function woocommerce_custom_fields_display()
{
    global $post;
    $product = wc_get_product($post->ID);
    $custom_fields_woocommerce_part_number = $product->get_meta('woocommerce_part_number');
    if ($custom_fields_woocommerce_part_number) {
        printf(
            '<div class="wc-block-components-product-sku wc-block-grid__product-sku wp-block-woocommerce-product-sku product_meta">%s <strong>%s</strong></div>',
            'Part number:',
            esc_html($custom_fields_woocommerce_part_number)
        );
    }
    $custom_fields_woocommerce_reference_number = $product->get_meta('woocommerce_reference_number');
    if ($custom_fields_woocommerce_reference_number) {
        printf(
            '<div class="wc-block-components-product-sku wc-block-grid__product-sku wp-block-woocommerce-product-sku product_meta">%s <strong>%s</strong></div>',
            'Reference number:',
            esc_html($custom_fields_woocommerce_reference_number)
        );
    }
}

add_action('woocommerce_before_add_to_cart_button', 'woocommerce_custom_fields_display');

function my_custom_rest_cors() {
    remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
    add_filter( 'rest_pre_serve_request', function( $value ) {
        header( 'Access-Control-Allow-Origin: *' );
        header( 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD' );
        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Access-Control-Expose-Headers: Link', false );

        return $value;
    } );
}
add_action( 'rest_api_init', 'my_custom_rest_cors', 15 );

function translate_product_title( $product_id, $language = 'en', $default_title = '' ) {
    $allowed_languages = ['en', 'pl', 'de', 'it', 'es', 'fr'];

    if( in_array($language, $allowed_languages) ) {
        switch( $language ) {
            case 'en':
                $translated_title = get_the_title( $product_id );
                break;
            case 'pl':
            case 'de':
            case 'it':
            case 'es':
            case 'fr':
                $translated_title = get_post_meta( $product_id, '_title_' . $language, true );
                break;
            default:
                if( empty($default_title) ) {
                    $translated_title = get_the_title( $product_id );
                }
        }

        if( !empty($translated_title) ) {
            return $translated_title;
        }
    }

    return $default_title;
}

function extended_response_product_data($response, $product, $event, $request) {
    if( isset($product['product_id']) ) {
//        if( $request ) {
//            $requested_query_data = $request->get_query_params();
//        }
        $wc_product = wc_get_product( $product['product_id'] );
//
//        if( isset($requested_query_data['language']) ) {
//            $product_title = translate_product_title( $product['product_id'], $requested_query_data['language']);
//        } else {
            $product_title = $wc_product->get_title();
//        }

        $response['title'] = $product_title;
        $response['slug'] = $wc_product->get_slug();
        $response['stock'] = $wc_product->get_stock_quantity();
        $response['photo'] = get_post_meta( $product['product_id'], '_gp_product_img_url', true );
        $response['sku'] = $wc_product->get_sku();
        $response['category'] = $wc_product->get_category_ids();
        $response['price'] = $wc_product->get_price();

        $product_cats = wp_get_post_terms( $product['product_id'], 'product_cat' );
        foreach ($product_cats as $product_cat){
            $response['category_slug'] = $product_cat->slug;
        }
    }

    return $response;
}

function extended_response_validate_token( $response, $user ) {
    global $wpdb;

    $user_meta_fields = [
        'first_name', 'last_name', 'billing_company', 'billing_address_1',
        'billing_address_2', 'billing_building_number', 'billing_city', 'billing_country',
        'billing_email', 'billing_first_name', 'billing_last_name', 'billing_phone',
        'billing_phone_prefix', 'billing_postcode', 'billing_state', 'billing_tax_number', 'is_company', 'is_workshop',
        'shipping_company', 'shipping_address_1', 'shipping_address_2', 'shipping_building_number', 'shipping_city',
        'shipping_country', 'shipping_email', 'shipping_first_name', 'shipping_last_name',
        'shipping_phone', 'shipping_phone_prefix', 'shipping_postcode', 'shipping_state',
        'provider'
    ];

    foreach( $user_meta_fields as $user_meta_field ) {
        $response['data']['user'][ $user_meta_field ] = $user->get( $user_meta_field );
    }

//    $response['data']['user']['first_name'] = $user->get('first_name');
//    $response['data']['user']['last_name'] = $user->get('last_name');
//    $response['data']['user']['last_name'] = $user->get('last_name');
//
//    $response['data']['user']['billing_company'] = $user->get('billing_company');
//    $response['data']['user']['billing_address_1'] = $user->get('billing_address_1');
//    $response['data']['user']['billing_address_2'] = $user->get('billing_address_2');
//    $response['data']['user']['billing_city'] = $user->get('billing_city');
//    $response['data']['user']['billing_country'] = $user->get('billing_country');
//    $response['data']['user']['billing_email'] = $user->get('billing_email');
//    $response['data']['user']['billing_first_name'] = $user->get('billing_first_name');
//    $response['data']['user']['billing_last_name'] = $user->get('billing_last_name');
//    $response['data']['user']['billing_phone'] = $user->get('billing_phone');
//    $response['data']['user']['billing_postcode'] = $user->get('billing_postcode');
//    $response['data']['user']['billing_state'] = $user->get('billing_state');
//    $response['data']['user']['billing_tax_number'] = $user->get('billing_tax_number');
//    $response['data']['user']['is_company'] = $user->get('is_company');
//
//    $response['data']['user']['shipping_company'] = $user->get('billing_company');
//    $response['data']['user']['shipping_address_1'] = $user->get('billing_address_1');
//    $response['data']['user']['shipping_address_2'] = $user->get('billing_address_2');
//    $response['data']['user']['shipping_city'] = $user->get('billing_city');
//    $response['data']['user']['shipping_country'] = $user->get('billing_country');
//    $response['data']['user']['shipping_email'] = $user->get('billing_email');
//    $response['data']['user']['shipping_first_name'] = $user->get('billing_first_name');
//    $response['data']['user']['shipping_last_name'] = $user->get('billing_last_name');
//    $response['data']['user']['shipping_phone'] = $user->get('billing_phone');
//    $response['data']['user']['shipping_postcode'] = $user->get('billing_postcode');
//    $response['data']['user']['shipping_state'] = $user->get('billing_state');

    $wl       = new TInvWL_Wishlist();
    $wl->user = absint($user->get('ID'));

//    $get_share_code_from_wishlist_sql = "SELECT `share_key` FROM `wp_tinvwl_lists` WHERE `author` = " . $user->ID;
//    $stats_results = $wpdb->get_results( $get_share_code_from_wishlist_sql, ARRAY_A );
//    $response['data']['wishlist']['default'] = $stats_results;
//    $response['data']['wishlist']['user_id'] = $user_id;

    $response['data']['wishlist'] = $wl->get_by_user_default();
    if( empty($response['data']['wishlist']) ) {
        $wishlist = $wl->add( '', 'default', 'share', $wl->user );
        if ( is_array( $wishlist ) ) {
            if( array_key_exists( 'share_key', $wishlist ) ) {
                $wl->set_sharekey( $wishlist['share_key'] );
            }

            $response['data']['wishlist'] = [ $wishlist ];
        }
    }

    if( ! empty($response['data']['wishlist'][0]['ID']) ) {
        $wishlist_ID = absint($response['data']['wishlist'][0]['ID']);

        $get_products_from_wishlist_sql = "SELECT `ID`, `product_id` FROM `wp_tinvwl_items` WHERE `wishlist_id` = " . $wishlist_ID;
        $wishlist_products_results = $wpdb->get_results( $get_products_from_wishlist_sql, ARRAY_A );
        $response['data']['wishlist_products'] = $wishlist_products_results;

//        $wlp = new TInvWL_Product();
//
//        $args                = array();
//        $args['wishlist_id'] = $response['data']['wishlist'][0]['ID'];
//        $args['external']    = false;
//
//        $products = $wlp->get( $args );
//        $response['data']['wishlist_products'] = $products;
    }

//    $response['data']['wishlist_products'] = $wl->wishlist_get_products( ['share_key' => '2b3209'] );

    $garage = new Gp_Garage( $user->get('ID') );
    $response['data']['garage']['default_vehicle'] = $garage->getDefaultVehicleId();
    $response['data']['garage']['vehicles'] = $garage->index();

    return $response;
}

function extended_generate_payload( $payload, $user ) {
    $payload['is_workshop'] = $user->get('is_workshop');
    $payload['is_company'] = $user->get('is_company');

    return $payload;
}

add_filter( 'simple_jwt_login_response_validate_token', 'extended_response_validate_token', 10, 2);
add_filter( 'simple_jwt_login_generate_payload', 'extended_generate_payload', 10, 2);

add_filter( 'tinvwl_api_product_data_response', 'extended_response_product_data', 10, 4);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'wc/v3' );
        register_rest_route(
            $route_namespace,
            '/wishlist/remove_products',
            array(

                'methods'             => 'GET',

                'callback'            => function () {
                    global $wpdb;

                    $wishlist_products  = [];
                    $result             = true;

                    $wl                 = new TInvWL_Wishlist();
                    $wlp                = new TInvWL_Product();
                    $wl->user           = absint( get_current_user_id() );

                    $default_wishlist   = $wl->get_by_user_default();

                    if( ! empty($default_wishlist[0]['ID']) ) {
                        $wishlist_ID = absint($default_wishlist[0]['ID']);

                        $get_products_from_wishlist_sql = "SELECT `ID`, `product_id` FROM `wp_tinvwl_items` WHERE `wishlist_id` = " . $wishlist_ID;
                        $wishlist_products_results = $wpdb->get_results( $get_products_from_wishlist_sql, ARRAY_A );

                        $wishlist_products = $wishlist_products_results;

                        foreach( $wishlist_products as $wishlist_product ) {
                            $args       = array();
                            $args['ID'] = $wishlist_product['ID'];

                            if( $wlp->remove( $args ) == false ) {
                                $result = false;
                            }
                        }
                    }

                    return array(
                        'data'    => array(
                            'wishlist_products' => $wishlist_products,
                            'result' => $result,
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'gp/v1' );
        register_rest_route(
            $route_namespace,
            '/garage',
            array(

                'methods'             => 'GET',

                'callback'            => function () {

                    $garage = new Gp_Garage( get_current_user_id() );

                    return array(
                        'data'    => array(
                            'default_vehicle' => $garage->getDefaultVehicleId(),
                            'vehicles' => $garage->index(),
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'gp/v1' );
        register_rest_route(
            $route_namespace,
            '/garage/add',
            array(

                'methods'             => 'POST',

                'callback'            => function ( $requested_data ) {

                    $garage = new Gp_Garage( get_current_user_id() );

                    return array(
                        'data'    => array(
                            'vehicle' => $garage->add( $requested_data ),
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'gp/v1' );
        register_rest_route(
            $route_namespace,
            '/garage/get/(?P<id>[\w-]+)',
            array(

                'methods'             => 'GET',

                'callback'            => function ( $requested_data ) {

                    if ( empty( $requested_data['id'] ) || $requested_data['id'] === '' ) {
                        return new WP_Error( 'no_id', __( 'No id given.', 'bdvs-password-reset' ), array( 'status' => 400 ) );
                    }

                    $garage = new Gp_Garage( get_current_user_id() );

                    return array(
                        'data'    => array(
                            'id' => $requested_data['id'],
                            'vehicle' => $garage->get( $requested_data['id'] ),
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'gp/v1' );
        register_rest_route(
            $route_namespace,
            '/garage/update/(?P<id>[\w-]+)',
            array(

                'methods'             => 'POST',

                'callback'            => function ( $requested_data ) {

                    if ( empty( $requested_data['id'] ) || $requested_data['id'] === '' ) {
                        return new WP_Error( 'no_id', __( 'No id given.', 'bdvs-password-reset' ), array( 'status' => 400 ) );
                    }

                    $garage = new Gp_Garage( get_current_user_id() );

                    return array(
                        'data'    => array(
                            'id' => $requested_data['id'],
                            'vehicle' => $garage->update( $requested_data ),
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'gp/v1' );
        register_rest_route(
            $route_namespace,
            '/garage/delete/(?P<id>[\w-]+)',
            array(

                'methods'             => 'GET',

                'callback'            => function ( $requested_data ) {

                    if ( empty( $requested_data['id'] ) || $requested_data['id'] === '' ) {
                        return new WP_Error( 'no_id', __( 'No id given.', 'bdvs-password-reset' ), array( 'status' => 400 ) );
                    }

                    $garage = new Gp_Garage( get_current_user_id() );

                    return array(
                        'data'    => array(
                            'id' => $requested_data['id'],
                            'vehicle' => $garage->delete( $requested_data['id'] ),
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'gp/v1' );
        register_rest_route(
            $route_namespace,
            '/garage/deleteAll',
            array(

                'methods'             => 'GET',

                'callback'            => function ( $requested_data ) {

                    $garage = new Gp_Garage( get_current_user_id() );

                    return array(
                        'data'    => array(
                            'vehicle' => $garage->deleteAll(),
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'gp/v1' );
        register_rest_route(
            $route_namespace,
            '/garage/setDefaultVehicle/(?P<id>[\w-]+)',
            array(

                'methods'             => 'POST',

                'callback'            => function ( $requested_data ) {

                    if ( empty( $requested_data['id'] ) || $requested_data['id'] === '' ) {
                        return new WP_Error( 'no_id', __( 'No id given.', 'bdvs-password-reset' ), array( 'status' => 400 ) );
                    }

                    $garage = new Gp_Garage( get_current_user_id() );

                    return array(
                        'data'    => array(
                            'vehicle_id' => $requested_data['id'],
                            'vehicle' => $garage->setDefaultVehicle( $requested_data['id'] ),
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'gp/v1' );
        register_rest_route(
            $route_namespace,
            '/garage/removeDefaultVehicle/(?P<id>[\w-]+)',
            array(

                'methods'             => 'POST',

                'callback'            => function ( $requested_data ) {

                    if ( empty( $requested_data['id'] ) || $requested_data['id'] === '' ) {
                        return new WP_Error( 'no_id', __( 'No id given.', 'bdvs-password-reset' ), array( 'status' => 400 ) );
                    }

                    $garage = new Gp_Garage( get_current_user_id() );

                    return array(
                        'data'    => array(
                            'vehicle_id' => $requested_data['id'],
                            'vehicle' => $garage->removeDefaultVehicle( $requested_data['id'] ),
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'gp/v1' );
        register_rest_route(
            $route_namespace,
            '/garage/getDefaultVehicle',
            array(

                'methods'             => 'GET',

                'callback'            => function ( $requested_data ) {

                    $garage = new Gp_Garage( get_current_user_id() );

                    return array(
                        'data'    => array(
                            'vehicle' => $garage->getDefaultVehicle(),
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'gp/v1' );
        register_rest_route(
            $route_namespace,
            '/profile/update',
            array(

                'methods'             => 'POST',

                'callback'            => function ( $requested_data ) {

                    $current_user_id = get_current_user_id();
                    $allowed_fields = [
                        'first_name', 'last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 'billing_building_number', 'billing_city',
                        'billing_country', 'billing_email', 'billing_first_name', 'billing_last_name', 'billing_phone', 'billing_phone_prefix',
                        'billing_postcode', 'billing_state', 'billing_tax_number', 'is_company', 'is_workshop', 'shipping_company',
                        'shipping_address_1', 'shipping_address_2', 'shipping_building_number', 'shipping_city', 'shipping_country', 'shipping_email',
                        'shipping_first_name', 'shipping_last_name', 'shipping_phone', 'shipping_phone_prefix', 'shipping_postcode', 'shipping_state'
                    ];
                    $updated_fields = [];

                    if( $requested_data->is_json_content_type() ) {
                        $requested_data = $requested_data->get_json_params();
                    } else {
                        $requested_data = $requested_data->get_body_params();
                    }

                    foreach( $requested_data as $text_field_id => $text_field ) {
                        if( in_array($text_field_id, $allowed_fields) ) {
                            $sanitized_text_field = sanitize_text_field( $text_field );

                            if( update_user_meta( $current_user_id, $text_field_id, $sanitized_text_field) ) {
                                $updated_fields[ $text_field_id ] = $text_field;
                            }
                        }
                    }

                    if( isset($requested_data['user_email']) ) {
                        $requested_new_email = esc_attr($requested_data['user_email']);
                        $user = get_user_by( 'id', $current_user_id );

                        // check if user is really updating the value
                        if ($user->user_email != $requested_new_email) {
                            // check if email is free to use
                            if (email_exists( $requested_new_email )){
                                return new WP_Error( 'no_id', __( 'Email already exists!', 'global' ), array( 'status' => 400 ) );
                            } else {
                                $args = array(
                                    'ID'         => $current_user_id,
                                    'user_email' => $requested_new_email
                                );
                                wp_update_user( $args );
                            }
                        }
                    }

                    return array(
                        'data'    => array(
                            'updated_fields' => $updated_fields,
                            'requested_data' => $requested_data,
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'gp/v1' );
        register_rest_route(
            $route_namespace,
            '/profile/password',
            array(

                'methods'             => 'POST',

                'callback'            => function ( $requested_data ) {

                    $current_user_id = get_current_user_id();
                    if( $requested_data->is_json_content_type() ) {
                        $requested_data = $requested_data->get_json_params();
                    } else {
                        $requested_data = $requested_data->get_body_params();
                    }

                    if(
                        !isset( $requested_data['password'] ) ||
                        !isset( $requested_data['password2']) ||
                        empty(  $requested_data['password'] ) ||
                        empty(  $requested_data['password2'])
                    ) {
                        return new WP_Error( 'no_id', __( 'Password or repeat password not given!', 'global' ), array( 'status' => 400 ) );
                    }

                    if( $requested_data['password'] != $requested_data['password2'] ) {
                        return new WP_Error( 'no_id', __( 'Given passwords should be the same!', 'global' ), array( 'status' => 400 ) );
                    }

                    $password = esc_attr( $requested_data['password'] );
                    $result = wp_set_password( $password, $current_user_id );
//                    $result = update_user_meta( $current_user_id, 'password', $password );

                    return array(
                        'data'    => array(
                            'result' => $result,
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'gp/v1' );
        register_rest_route(
            $route_namespace,
            '/orders',
            array(

                'methods'             => 'GET',

                'callback'            => function ( $requested_data ) {

                    $requested_query_data = $requested_data->get_query_params();

                    $current_user_id = get_current_user_id();

                    $customer_orders = wc_get_orders( [
                        'type'        => 'shop_order',
                        'limit'       => - 1,
                        'customer_id' => $current_user_id,
                        'status'      => array_keys(wc_get_order_statuses()),
                    ] );

                    $orders = [];
                    foreach ($customer_orders as $customer_order) {
                        $orderq = wc_get_order($customer_order);
                        $items = [];

                        foreach ($orderq->get_items() as $item_id => $item) {
                            $product_name = $item['name'];

//                            if(
//                                isset($requested_data['language']) &&
//                                in_array($requested_data['language'], ['en', 'pl', 'de'])
//                            ) {
//                                $language = $requested_data['language'];
//                                $product_id = $item->get_product_id();
//
//                                switch( $language ) {
//                                    case 'en':
//                                        $translated_title = get_the_title( $product_id );
//                                        break;
//                                    case 'pl':
//                                    case 'de':
//                                        $translated_title = get_post_meta( $product_id, '_title_' . $language, true );
//                                        break;
//                                    default:
//                                        $translated_title = '';
//                                }
//
//                                if( !empty($translated_title) ) {
//                                    $product_name = $translated_title;
//                                }
//                            }

                            if( isset($requested_query_data['language']) ) {
                                $product_name = translate_product_title( $item->get_product_id(), $requested_query_data['language'], $item['name']);
                            }

                            // Get the item quantity
                            $item_quantity = $orderq->get_item_meta($item_id, '_qty', true);

                            // Get the item line total
                            $item_total = $orderq->get_item_meta($item_id, '_line_total', true);

                            $items[] = [
                                'name' => $product_name,
                                'quantity' => $item_quantity,
                                'total' => $item_total
                            ];
                        }

                        $currency_code = $orderq->get_currency();

                        $orders[] = [
                            "ID" => $orderq->get_id(),
                            "total" => $orderq->get_total(),
                            "status" => $orderq->get_status(),
                            "currency" => $currency_code,
                            "currency_symbol" => get_woocommerce_currency_symbol( $currency_code ),
                            'items' => $items,
                            "date" => $orderq->get_date_created()->date_i18n('d.m.Y'),
                        ];
                    }

                    return array(
                        'data'    => array(
//                            'requested_data' => $requested_data,
                            'orders' => $orders,
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

add_action(
    'rest_api_init',
    function () {
        $route_namespace = apply_filters( 'gp_route_namespace', 'gp/v1' );
        register_rest_route(
            $route_namespace,
            '/order/(?P<id>[\d]+)',
            array(

                'methods'             => 'GET',

                'callback'            => function ( $requested_data ) {
                    $requested_query_data = $requested_data->get_query_params();

                    $order_id = absint( $requested_data['id'] );
                    $current_user_id = get_current_user_id();

                    $customer_users = get_post_meta( $order_id, '_customer_user', false );
                    $has_access = false;

                    foreach( $customer_users as $customer_user_id ) {
                        if( $customer_user_id == $current_user_id ) {
                            $has_access = true;
                        }
                    }

                    if( $current_user_id == 0 ) {
                        $has_access = false;
                    }

                    if( ! $has_access ) {
                        return array(
                            'data'    => array(
                                'requested_data' => $requested_data,
                                'order' => null,
                                'status' => 403,
                            )
                        );
                    }

                    $wc_order = wc_get_order( $order_id );
                    $items = [];

                    $order_data = $wc_order->get_data();
                    foreach ($wc_order->get_items() as $item_id => $item) {
                        $wc_product = $item->get_product();
                        $product_name = $item['name'];

                        if( isset($requested_data['language']) ) {
                            $product_name = translate_product_title( $item->get_product_id(), $requested_query_data['language'], $item['name']);
                        }

                        // Get the item quantity
                        $item_quantity = $wc_order->get_item_meta($item_id, '_qty', true);

                        // Get the item line total
                        $item_total = $wc_order->get_item_meta($item_id, '_line_total', true);

                        $item_product_slug = $wc_product->get_slug();
                        $item_product_photo = get_post_meta( $item->get_product_id(), '_gp_product_img_url', true );
                        $item_product_sku = $wc_product->get_sku();
                        $item_product_category = $wc_product->get_category_ids();
                        $item_product_category_slug = '';

                        $product_cats = wp_get_post_terms( $item->get_product_id(), 'product_cat' );
                        foreach ($product_cats as $product_cat){
                            $item_product_category_slug = $product_cat->slug;
                        }

                        $items[] = [
                            'name' => $product_name,
                            'quantity' => $item_quantity,
                            'total' => $item_total,
                            'slug' => $item_product_slug,
                            'photo' => $item_product_photo,
                            'sku' => $item_product_sku,
                            'category_slug' => $item_product_category_slug,
                        ];
                    }

                    $order = [
                        'id' => $order_data['id'],
                        'status' => $order_data['status'],
                        'currency' => $order_data['currency'],
                        'currency_symbol' => get_woocommerce_currency_symbol( $order_data['currency'] ),
                        'payment_method' => $order_data['payment_method'],
                        'payment_method_title' => $order_data['payment_method_title'],
                        'date_created' => $order_data['date_created']->date('Y-m-d H:i:s'),
                        'date_modified' => $order_data['date_modified']->date('Y-m-d H:i:s'),
                        'date_paid' => !empty($order_data['date_paid'])?$order_data['date_paid']->date('Y-m-d H:i:s'):false,
                        'discount_total' => $order_data['discount_total'],
                        'order_discount_tax' => $order_data['discount_tax'],
                        'order_shipping_total' => $order_data['shipping_total'],
                        'order_shipping_tax' => $order_data['shipping_tax'],
                        'order_total' => $order_data['total'],
                        'order_total_tax' => $order_data['total_tax'],
                        'order_customer_id' => $order_data['customer_id'],

                        'order_billing_first_name' => $order_data['billing']['first_name'],
                        'order_billing_last_name' => $order_data['billing']['last_name'],
                        'order_billing_company' => $order_data['billing']['company'],
                        'order_billing_address_1' => $order_data['billing']['address_1'],
                        'order_billing_address_2' => $order_data['billing']['address_2'],
                        'order_billing_building_number' => ($order_data['billing']['building_number']??''),
                        'order_billing_city' => $order_data['billing']['city'],
                        'order_billing_state' => $order_data['billing']['state'],
                        'order_billing_postcode' => $order_data['billing']['postcode'],
                        'order_billing_country' => $order_data['billing']['country'],
                        'order_billing_email' => $order_data['billing']['email'],
                        'order_billing_phone' => $order_data['billing']['phone'],

                        'order_shipping_first_name' => $order_data['shipping']['first_name'],
                        'order_shipping_last_name' => $order_data['shipping']['last_name'],
                        'order_shipping_phone' => $order_data['shipping']['phone'],
                        'order_shipping_company' => $order_data['shipping']['company'],
                        'order_shipping_address_1' => $order_data['shipping']['address_1'],
                        'order_shipping_address_2' => $order_data['shipping']['address_2'],
                        'order_shipping_building_number' => ($order_data['shipping']['building_number']??''),
                        'order_shipping_city' => $order_data['shipping']['city'],
                        'order_shipping_state' => $order_data['shipping']['state'],
                        'order_shipping_postcode' => $order_data['shipping']['postcode'],
                        'order_shipping_country' => $order_data['shipping']['country'],
                        'items' => $items,

                        'parcel_locker' => [
                            'parcelLockerId' => $wc_order->get_meta( '_parcel_locker_id' ),
                            'parcelLockerName' => $wc_order->get_meta( '_parcel_locker_name' ),
                        ],

                        'shipment_tracking' => $wc_order->get_meta('_wc_shipment_tracking_items')
//                        'order_data' => $order_data
                    ];

                    return array(
                        'data'    => array(
                            'requested_data' => $requested_data,
                            'order' => $order,
                            'status' => 200,
                        )
                    );
                },

                'permission_callback' => function () {
                    return true;
                },

            )
        );
    }
);

function gp_query_vars( $qvars ) {
    $qvars[] = 'language';
    $qvars[] = 'market';
    return $qvars;
}
add_filter( 'query_vars', 'gp_query_vars' );

add_filter( 'woocommerce_order_get_items', function( $items, $order ) {
//    $language = get_query_var( 'language', '@' );
    $language         = isset($_GET['language'])?$_GET['language']:false;
//    $order_currency   = $order->get_meta('_order_currency', true );
//    $wmc_order_info   = $order->get_meta('wmc_order_info', true );

//    $return_items = array();
//
//    foreach ( $items as $item_id => $item ) {
////        if ( $item && is_a( $item, 'WC_Order_Item_Product' ) ) {
////            $item = clone $item;
////            $item->set_subtotal( wmc_revert_price( $item->get_subtotal(), $order_currency ) );
////            $item->set_total( wmc_revert_price( $item->get_total(), $order_currency ) );
////        }
//
//        $product_name = $item['name'];
//
//        if( $language ) {
//            $product_name = translate_product_title( $item['product_id'], $language, $item['name']);
//        }
//
//        $item['name'] = $product_name;
//
//        $return_items[ $item_id ] = $item;
//    }

    foreach( $items as $item_id => $item ) {
        $product_name = $item['name'];

        if( $language ) {
            $product_name = translate_product_title( $item['product_id'], $language, $item['name']);
        }

        $items[$item_id]['name'] = $product_name;
    }

    return $items;
//    return $return_items;

}, 10, 2 );

add_filter( 'woocommerce_product_title', 'wp_kama_woocommerce_product_title_filter', 10, 2 );
function wp_kama_woocommerce_product_title_filter( $parent_data_title, $that ){
    $language = isset($_GET['language'])?$_GET['language']:false;

    if ( $language ){
        return translate_product_title( $that->get_id(), $language, $parent_data_title);
    }

    return $parent_data_title;
}

//add_filter( 'woocommerce_get_cart_contents', 'gp_woo_get_cart_contents' );
//function gp_woo_get_cart_contents( $cart_items ) {
////    $language         = $_GET['language'];
//
//    if ( ! empty( $cart_items ) ) {
//
//        foreach ( $cart_items as $item ) {
////            if( isset($item['data']) ) {
//            if ( ! empty( $item[ 'data' ] ) ) {
////                $item['data']['name'] = '++++';
//            }
////                $product_name = $item['data']->get_title();
////                $product_name = $item['name'];
//
////                if( $language ) {
////                    $product_name = translate_product_title( $item['product_id'], $language, $item['name']);
////                }
//
////                $cart_items[$item_key]['data']->set_title($product_name . '$');
////            }
////            if ( ! empty( $item[ $this->data_key ] ) ) {
////
////                $data         = $item[ $this->data_key ];
////                $apartment_id = ! empty( $data['apartment_id'] ) ? $data['apartment_id'] : 0;
////
////                if ( ! empty( $item[ $this->price_key ] ) ) {
////                    $price = $item[ $this->price_key ];
////                } else {
////
////                    $price = get_post_meta( $apartment_id, '_apartment_price', true );
////                    $price = floatval( $price );
////                    $diff  = $data['check_out_date'] - $data['check_in_date'];
////                    $diff  = ceil( $diff / DAY_IN_SECONDS );
////
////                    if ( ! Plugin::instance()->engine_plugin->is_per_nights_booking() ) {
////                        $diff++;
////                    }
////
////                    $advanced_price_rates = new Advanced_Price_Rates( $apartment_id );
////                    $rates                = $advanced_price_rates->get_rates();
////
////                    if ( ! empty( $rates ) ) {
////                        foreach ( $rates as $rate ) {
////
////                            $duration = absint( $rate['duration'] );
////
////                            if ( $diff >= $duration ) {
////                                $price = floatval( $rate['value'] );
////                            }
////
////                        }
////                    }
////
////                    $price = $price * $diff;
////
////                }
////
////                if ( $price ) {
////                    $item['data']->set_price( floatval( $price ) );
////                }
////
////                $this->price_adjusted = true;
////
////            }
//        }
//    }
//
//    return $cart_items;
//}

//add_filter( 'woocommerce_cart_loaded_from_session', 'gp_woo_cart_loaded_from_session' );
//function gp_woo_cart_loaded_from_session( $cart_items ) {
//    $language         = $_GET['language'];
//
//    if ( ! empty( $cart_items ) ) {
//
//        foreach ( $cart_items as $item_key => $item ) {
//            $product_name = $item['name'];
//
//            if( $language ) {
//                $product_name = translate_product_title( $item['product_id'], $language, $item['name']);
//            }
//
//            $items[$item_key]['name'] = $product_name . '^';
//        }
//    }
//
//    return $cart_items;
//}
// TEMPLATE
add_filter( 'woocommerce_cart_item_name', 'gp_woo_cart_item_name_test', 8, 3 );
function gp_woo_cart_item_name_test( $product_name, $cart_item, $cart_item_key ){
    $language = $_GET['language'];

    if ( isset( $cart_item['product_id'] ) && $language ){
        $product_name = translate_product_title( $cart_item['product_id'], $language, $product_name);
    }

    return $product_name;
}

///**
// * Compatibility with WooCommerce added to cart message
// *
// * Makes sure title of product is translated.
// *
// * The title of product is added through sprintf %s of a Gettext.
// *
// */
//add_filter( 'the_title', 'trp_woo_translate_product_title_added_to_cart_2', 10, 2 );
//function trp_woo_translate_product_title_added_to_cart_2( ...$args ){
//    // fix themes that don't implement the_title filter correctly. Works on PHP 5.6 >.
//    // Implemented this because users we getting this error frequently.
//    if( isset($args[0])){
//        $title = $args[0];
//    } else {
//        $title = '';
//    }
//
//
//    if( class_exists( 'WooCommerce' ) ){
//        if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
//            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);//set a limit if it is supported to improve performance
//        }
//        else{
//            $callstack_functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
//        }
//
//        $list_of_functions = apply_filters( 'trp_woo_translate_title_before_translate_page', array( 'wc_add_to_cart_message' ) );
//        if( !empty( $callstack_functions ) ) {
//            foreach ( $callstack_functions as $callstack_function ) {
////                if ( in_array( $callstack_function['function'], $list_of_functions ) ) {
////                    $trp = TRP_Translate_Press::get_trp_instance();
////                    $translation_render = $trp->get_component( 'translation_render' );
////                    $title = $translation_render->translate_page($title);
//                    $title = $title . '+++';
////                    break;
////                }
//            }
//        }
//    }
//    return $title;
//}

//add_filter( 'woocommerce_cart_item_name', 'trp_woo_cart_item_name_test', 8, 3 );
//function trp_woo_cart_item_name_test( $product_name, $cart_item, $cart_item_key ){
////    if ( isset( $cart_item['product_id'] ) ){
////        $title = get_the_title( $cart_item['product_id'] );
////        if ( !empty( $title )){
////            if ( strpos( $product_name, '</a>' ) ) {
////                preg_match_all('~<a(.*?)href="([^"]+)"(.*?)>~', $product_name, $matches);
////                $product_name = sprintf( '<a href="%s">%s</a>', esc_url( $matches[2][0] ), $title );
////            }
////        }
////    }
//    return $product_name . '@@';
//}

//remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
//function woocommerce_template_single_title_custom()
//{
//    $additional_text = ' More Info ';
//    the_title( '<h3 class="product_title entry-title">'.$additional_text,' </h3>' );
//}
//add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title_custom', 5);

//$post_types = array_merge(
//    ["comment", "category"],
//    get_post_types()
//);
//
//foreach ( $post_types as $post_type ) {
//    add_filter( 'rest_prepare_'. $post_type, 'gp_handle_rest_api_translations' );
//}
//
///**
// * Function that translates the content excerpt and post title in the REST API
// * @param $response
// * @return mixed
// */
//function gp_handle_rest_api_translations($response){
//    if ( isset( $response->data ) ) {
//        if ( isset( $response->data['name'] ) ){
//            $response->data['name'] = 'NAME';
//        }
//        if ( isset( $response->data['title'] ) && isset( $response->data['title']['rendered'] ) ) {
//            $response->data['title']['rendered'] = 'TitleRendered';
//        }
//        if ( isset( $response->data['title'] ) ) {
//            $response->data['title'] = 'TitleRendered';
//        }
//    }
//    return $response;
//}

/****
 * add_filter( 'the_title', 'change_product_title', 10, 2 );
function change_product_title( $post_title, $post_id ) {
if( get_post_type($post_id) === 'product' ) {
$product = wc_get_product($post_id);

if( ( is_a($product, 'WC_Product') && $product->get_sale_price() > 0 )
|| has_term( 'Hard Goods', 'product_tag', $post_id ) ) {
$post_title .= ' NET';
}
}
return $post_title;
}

add_filter( 'woocommerce_product_variation_get_name', 'change_product_name', 10, 2 );
add_filter( 'woocommerce_product_get_name', 'change_product_name', 10, 2 );
function change_product_name( $name, $product ) {
if ( $product->get_sale_price() > 0 || has_term( 'Hard Goods', 'product_tag', $product->get_id() ) ) {
$name .= ' NET';
}
return $name;
}
 *
 */

function gp_multi_currency_response( $value, \WC_Order $order ) {
    $wmc_order_info  = $order->get_meta( 'wmc_order_info', true );
    $order_currency_code = $order->get_currency();

    if( isset( $wmc_order_info[$order_currency_code] ) ) {
        $position = $wmc_order_info[$order_currency_code]['pos'];
        $symbol   = html_entity_decode( get_woocommerce_currency_symbol( $order_currency_code ) );
        $currency_minor_unit = $wmc_order_info[$order_currency_code]['decimals'];
        $currency_decimal_separator = $wmc_order_info[$order_currency_code]['decimal_sep'];
        $currency_thousand_separator = $wmc_order_info[$order_currency_code]['thousand_sep'];
        $prefix   = '';
        $suffix   = '';
    } else {
        $position = get_option( 'woocommerce_currency_pos' );
        $symbol   = html_entity_decode( get_woocommerce_currency_symbol() );
        $order_currency_code = get_woocommerce_currency();
        $currency_minor_unit = wc_get_price_decimals();
        $currency_decimal_separator = wc_get_price_decimal_separator();
        $currency_thousand_separator = wc_get_price_thousand_separator();
        $prefix   = '';
        $suffix   = '';
    }

    switch ( $position ) {
        case 'left_space':
            $prefix = $symbol . ' ';
            break;
        case 'left':
            $prefix = $symbol;
            break;
        case 'right_space':
            $suffix = ' ' . $symbol;
            break;
        case 'right':
            $suffix = $symbol;
            break;
    }

    return array_merge(
        (array) $value,
        [
            'currency_code'               => $order_currency_code,
            'currency_symbol'             => $symbol,
            'currency_minor_unit'         => $currency_minor_unit,
            'currency_decimal_separator'  => $currency_decimal_separator,
            'currency_thousand_separator' => $currency_thousand_separator,
            'currency_prefix'             => $prefix,
            'currency_suffix'             => $suffix,
        ]
    );
}

add_filter('woocommerce_product_is_in_stock', 'custom_is_in_stock', 10, 2);
function custom_is_in_stock( $value, $product )
{
    return ($product->get_stock_quantity() > 0);
}

add_filter( 'woocommerce_product_get_stock_quantity' ,'custom_get_stock_quantity', 10, 2 );
function custom_get_stock_quantity( $value, $product )
{
    $product_id     = $product->get_id();
    $market         = isset($_GET['market'])?$_GET['market']:false;

    if( isset( $_COOKIE['market'] ) && $market === false )
    {
        $market     = $_COOKIE['market'];
    }

    if( $market && $product_id )
    {
        if( $market == 'uk' )
        {
            $stock_market   = '_stock_uk';
        }
        else if ( $market == 'de' )
        {
            $stock_market   = '_stock_de';
        }
        else if ( $market == 'it' )
        {
            $stock_market   = '_stock_it';
        }
        else if ( $market == 'es' )
        {
            $stock_market   = '_stock_es';
        }
        else if ( $market == 'fr' )
        {
            $stock_market   = '_stock_fr';
        }
        else if ( $market == 'pl' )
        {
            $stock_market   = '_stock_pl';
        }
        else
        {
            $stock_market   = '_stock_uk';
        }

        return intval($product->get_meta( $stock_market, true ));
    }

    return intval($product->get_meta( '_stock_uk', true ));
//    return $value;
}

add_filter( 'woocommerce_product_get_shipping_class_id' ,'custom_get_shipping_class_id', 10, 2 );
function custom_get_shipping_class_id( $value, $product )
{
    $product_id     = $product->get_id();
    $market         = isset($_GET['market'])?$_GET['market']:false;

    if( isset( $_COOKIE['market'] ) && $market === false )
    {
        $market     = $_COOKIE['market'];
    }

    if( $market && $product_id )
    {
        $shipping_class_for_market = false;

        if( $market == 'uk' )
        {
            return $value;
        }
        else if ( $market == 'de' )
        {
            $shipping_class_for_market = '_shipping_class_de';
        }
        else if ( $market == 'it' )
        {
            $shipping_class_for_market = '_shipping_class_it';
        }
        else if ( $market == 'es' )
        {
            $shipping_class_for_market = '_shipping_class_es';
        }
        else if ( $market == 'fr' )
        {
            $shipping_class_for_market = '_shipping_class_fr';
        }
        else if ( $market == 'pl' )
        {
            $shipping_class_for_market = '_shipping_class_pl';
        }

        if( $shipping_class_for_market ) {
//            return $value;
            return $product->get_meta( $shipping_class_for_market, true );
        }

        return $value;
    }

    return $value;
}

add_filter('woocommerce_api_order_response', 'intercept_api_response', 1, 4);
/**
 * Here, intercept api's response to include the url of payment
 **/
function intercept_api_response($order_data, $order)
{
    $order_data['payment_info'] = $order->payment_info;

    return $order_data;
}

add_filter('woocommerce_api_create_order', 'intercept_on_api_create_order', 10, 3);


function intercept_on_api_create_order($id, $data, $api)
{
    if( $data['payment_details']['method_id'] == 'bacs' ) {
        update_post_meta($id, '_payment_info', 'mBank');
    }

//    if( $data['payment_details']['method_id'] == 'przelewy24' ) {
//        $przelewy24 = new WC_Gateway_Przelewy24();
//        $order = new WC_Order( $id );
//
//        $config_accessor = $przelewy24->get_settings_from_internal_formatted();
////        $config_accessor = $this->get_settings_from_internal_formatted( 'PLN' );
////        $hasher = new P24_Hasher( $config_accessor );
//
////        $order_id = $hasher->return_if_valid( $postData['order_id'], $postData['p24_hashed_order_id'] );
//        $p24_session_id = $session_id = addslashes( $id . '_' . uniqid( md5( wp_rand() ), true ) );
//        $p24_method = 0;
//        $p24_regulation_accept = true;
//
//        $data_to_register = $przelewy24->generator->generate_payload_for_rest( $order, $p24_session_id, $p24_method, $p24_regulation_accept );
//        $P24C = new Przelewy24Class($config_accessor);
//
//        $api_rest         = new P24_Rest_Transaction( $config_accessor );
//        $token            = $api_rest->register_raw_token( $data_to_register );
//        if ( isset( $token ) ) {
//            update_post_meta($id, '_payment_url', $P24C->trnRequest($token, false));
//        } else {
//            update_post_meta($id, '_payment_url', $data_to_register['urlReturn']);
//        }
//    }
//    if (in_array($data['payment_details']['method_id'], ['pagseguro', 'bacs'])) {
//        $order = wc_get_order($id);
//        $order->calculate_totals();
//
//        if ($data['payment_details']['method_id'] == 'paypal') {
//            $paypal = new WC_Gateway_Paypal();
//            $payment = $paypal->process_payment($id);
//        }
//        update_post_meta($id, '_payment_url', $payment['redirect']);
//    }
    return $data;
}

add_action( 'woocommerce_store_api_checkout_update_order_from_request', 'gp_checkout_update_order_meta_from_request', 8, 2 );

function gp_checkout_update_order_meta_from_request(\WC_Order $order, \WP_REST_Request $request) {
    $body = $request->get_body();
    $body_decode_json = json_decode( $body, true );

    $companyTaxNumber = sanitize_text_field( $body_decode_json['billing_address']['companyTaxNumber']  ?? '' );

    $order->add_meta_data( '_billing_nip', $companyTaxNumber, true );
}

add_action( 'woocommerce_store_api_checkout_update_order_meta', 'gp_checkout_update_order_meta', 8, 1 );

function gp_checkout_update_order_meta( \WC_Order $order ) {
    $language         = isset($_GET['language'])?$_GET['language']:false;
    $market           = isset($_GET['market'])?$_GET['market']:false;
    $jwt              = isset($_GET['jwt'])?$_GET['jwt']:false;

    if( ! $jwt && isset($_GET['JWT']) ) {
        $jwt          = $_GET['JWT'];
    }

    if( ! $market && isset( $_COOKIE['market'] ) ) {
        $market = $_COOKIE['market'];
    }

    $order->add_meta_data( '_language', $language );
    $order->add_meta_data( '_market', $market );

    if( $jwt ) {
        $jwtSettings = new SimpleJWTLoginSettings(new WordPressData());
        JWT::$leeway = 60;
        $decoded = (array)JWT::decode(
            $jwt,
            JwtKeyFactory::getFactory($jwtSettings)->getPublicKey(),
            [$jwtSettings->getGeneralSettings()->getJWTDecryptAlgorithm()]
        );

        $order->add_meta_data( '_jwt', json_encode( $decoded ) );

        if( isset( $decoded['id'] ) && ! empty( $decoded['id'] ) ) {
            $order->add_meta_data( '_customer_user', $decoded['id'] );
        }
    }
}

add_action( 'woocommerce_rest_checkout_process_payment_with_context', 'add_payment_request_order_meta', 8, 2 );

function add_payment_request_order_meta( PaymentContext $context, PaymentResult &$result ) {
    if( isset($context->payment_data['parcellockerid']) ) {
        $parcel_locker_id = $context->payment_data['parcellockerid'];
        $order_id = $context->order->get_id();

        update_post_meta($order_id, '_parcel_locker_id', $parcel_locker_id);

        // BASELINKER !!!
        update_post_meta($order_id, 'delivery_point_id', $parcel_locker_id);
        update_post_meta($order_id, '_inpost_id', $parcel_locker_id);
    }

    if( isset($context->payment_data['parcellockername']) ) {
        update_post_meta($context->order->get_id(), '_parcel_locker_name', $context->payment_data['parcellockername']);
    }

    if ( 'bacs' === $context->payment_method ) {
        $order = $context->order;

        $bacs = new WC_Gateway_BACS();
        $bacs_payment_details = [];

        if( ! empty( $bacs->account_details ) )
        {
            $bacs_accounts = $bacs->account_details;

            // Get the order country and country $locale.
            $country = $order->get_billing_country();
            $locale  = $bacs->get_country_locale();

            $market = $order->get_meta('_market');

            // Get sortcode label in the $locale array and use appropriate one.
            $sortcode = isset( $locale[ $country ]['sortcode']['label'] ) ? $locale[ $country ]['sortcode']['label'] : __( 'Sort code', 'woocommerce' );

            $bank_accounts = [
                'pl' => [
                    'bank_name' => 'ING',
                    'account_number' => '85105010701000009081159437',
                    'sort_code' => '10501070',
                    'iban' => 'PL85105010701000009081159437',
                    'bic' => 'INGBPLPW'
                ],
                'eur' => [
                    'bank_name' => 'Alior Bank',
                    'account_number' => '25249010570000990243257916',
                    'sort_code' => '2490105700',
                    'iban' => 'PL25249010570000990243257916',
                    'bic' => 'ALBPPLPW'
                ],
                'uk' => [
                    'bank_name' => 'Bank Of Scotland',
                    'account_number' => '14103160',
                    'sort_code' => '80-22-60',
                    'iban' => 'GB07BOFS80226014103160',
                    'bic' => 'BOFSGBS1SDP'
                ],
            ];

            switch( $market ) {
                case 'pl':
                    $bank_account_details = $bank_accounts['pl'];
                    break;
                case 'uk':
                    $bank_account_details = $bank_accounts['uk'];
                    break;
                default:
                    $bank_account_details = $bank_accounts['eur'];
            }

            $bacs_payment_details = [
                'bank_name'      => [
                    'label' => __( 'Bank', 'woocommerce' ),
                    'value' => $bank_account_details['bank_name'],
                ],
                'account_number' => [
                    'label' => __( 'Account number', 'woocommerce' ),
                    'value' => $bank_account_details['account_number'],
                ],
                'sort_code'      => [
                    'label' => $sortcode,
                    'value' => $bank_account_details['sort_code'],
                ],
                'iban'           => [
                    'label' => __( 'IBAN', 'woocommerce' ),
                    'value' => $bank_account_details['iban'],
                ],
                'bic'            => [
                    'label' => __( 'BIC', 'woocommerce' ),
                    'value' => $bank_account_details['bic'],
                ]
            ];

//            foreach ( $bacs_accounts as $bacs_account ) {
//                $bacs_account = (object) $bacs_account;
//
////                if( str_contains( $bacs_account->iban, $country ) )
////                {
//                    $bacs_payment_details = [
//                        'bank_name'      => [
//                            'label' => __( 'Bank', 'woocommerce' ),
//                            'value' => $bacs_account->bank_name,
//                        ],
//                        'account_number' => [
//                            'label' => __( 'Account number', 'woocommerce' ),
//                            'value' => $bacs_account->account_number,
//                        ],
//                        'sort_code'      => [
//                            'label' => $sortcode,
//                            'value' => $bacs_account->sort_code,
//                        ],
//                        'iban'           => [
//                            'label' => __( 'IBAN', 'woocommerce' ),
//                            'value' => $bacs_account->iban,
//                        ],
//                        'bic'            => [
//                            'label' => __( 'BIC', 'woocommerce' ),
//                            'value' => $bacs_account->bic,
//                        ]
//                    ];
////                }
//
//                $bacs_account_number = $bacs_account->account_number;
//            }
        }

        update_post_meta($order->get_id(), '_payment_details_bacs', $bacs_payment_details);
    }

    if( 'przelewy24' === $context->payment_method  ) {
        $order = $context->order;

//        if( isset($context->payment_data['methodrefid']) ) {
//
//        } else {
            $przelewy24 = new WC_Gateway_Przelewy24();
            $przelewy24generator = new Przelewy24Generator( $przelewy24 );

            $config_accessor = $przelewy24->get_settings_from_internal_formatted( $order->get_currency() );

            $p24_session_id = $session_id = addslashes( $order->get_id() . '_' . uniqid( md5( wp_rand() ), true ) );

            if( isset($context->payment_data['methodrefid']) ) {
                $p24_method = 229;
            } else {
                if( isset( $context->payment_data['p24method'] ) ) {
                    $p24_method = $context->payment_data['p24method'];
                } else {
                    $p24_method = 0;
                }
            }

            $p24_regulation_accept = true;

            $data_to_register = $przelewy24generator->generate_payload_for_rest( $order, $p24_session_id, $p24_method, $p24_regulation_accept );

            if( isset($context->payment_data['methodrefid']) ) {
                $data_to_register['methodRefId'] = (string)$context->payment_data['methodrefid'];
//                $date_to_register['method'] = 229;
                update_post_meta($order->get_id(), '_method_ref_id', (string)$context->payment_data['methodrefid']);
            }

            $P24C = new Przelewy24Class($config_accessor);

            $api_rest         = new P24_Rest_Transaction( $config_accessor );
            $token            = $api_rest->register_raw_token( $data_to_register );
            if ( isset( $token ) ) {
                $redirect_url = $P24C->trnRequest($token, false);
            } else {
                $redirect_url = $data_to_register['urlReturn'];
            }

            update_post_meta($order->get_id(), '_payment_url', $redirect_url);
            update_post_meta($order->get_id(), '_p24_order_session_id', $p24_session_id);

            $gateway_result = [
                'redirect_p24_url' => $redirect_url
            ];

            if( isset( $token ) ) {
                $gateway_result[ 'token' ] = $token;
            }

            $result->set_redirect_url( $redirect_url );
            $result->set_payment_details( array_merge( $result->payment_details, $gateway_result ) );

            WC()->cart->empty_cart();
//        }
    }

//    if( 'ppcp' === $context->payment_method ) {
//        $order = $context->order;
//
//        $ppcp_order = ppcp_create_paypal_order_for_wc_order( $order );
//
//        print_r( $ppcp_order );
//        exit();
//    }
}

//add_filter( 'woocommerce_payment_successful_result', 'modify_successful_payment_result', 99999, 2 );
//
//function modify_successful_payment_result( $result, $order_id ) {
//    return [
//        'result'   => $result,
//        'redirect' => 'fabrykawchmurach.home.pl',
//    ];
//}

//add_filter('wp_mail_smtp_options_get_group', 'customize_smtp_options_get_group', 10, 2);
//function customize_smtp_options_get_group( $options, $group   ){
//    error_log( 'GRUPA: ' . $group );
//    error_log( 'OPCJE: ' . print_r( $options, true ) );
//
//    return $options;
//}

add_filter( 'wp_mail_from', 'customize_mail_from', 9999, 1 );
function customize_mail_from( $from_email ){
    $market           = isset($_GET['market'])?$_GET['market']:false;

    if( ! $market && isset( $_COOKIE['market'] ) ) {
        $market = $_COOKIE['market'];
    }

    if( $market ) {
//        error_log( 'STARY ADRES E-MAIL (' . $from_email . ')' );
//        error_log( 'NOWY ADRES E-MAIL ' . 'no-replay@' . getDomainByMarket( $market ) );

        return 'no-replay@' . getDomainByMarket( $market );
    }

//    error_log( 'NIE MA MARKETU!' );

    return 'no-replay@globalparts.co.uk';
}

add_filter('wp_mail_smtp_options_get', 'customize_smtp_options_get', 9999, 3);
function customize_smtp_options_get( $value, $group, $key ){
    $market           = isset($_GET['market'])?$_GET['market']:false;

    if( ! $market && isset( $_COOKIE['market'] ) ) {
        $market = $_COOKIE['market'];
    }

    if(
        ($group == 'smtp' && $key == 'user') ||
        ($group == 'mail' && $key == 'from_mail') ||
        ($group == 'mail' && $key == 'from_email')
    ) {

//        error_log( 'STARY ADRES E-MAIL SMTP (' . $key . '): ' . $value );
//        error_log( 'NOWY ADRES E-MAIL SMTP (' . $key . '):' . 'no-replay@' . getDomainByMarket( $market ) );

        if( $market ) {
            return 'no-replay@' . getDomainByMarket( $market );
        } else {
//            error_log( 'MARKET NOT SET??' );
        }
    }

//    error_log( $value . ' ' . $group . ' ' . $key );

    return $value;
}

//add_filter('woocommerce_available_payment_gateways', 'filter_payment_gateways_based_on_shipping', 10, 1);
//function filter_payment_gateways_based_on_shipping($available_gateways) {
//    // Check if specific shipping method is selected
//    if (is_checkout()) {
//        $chosen_shipping_method = WC()->session->get('chosen_shipping_methods')[0];
//
//        // List of shipping methods for which you want to disable payment methods
//        $shipping_methods_to_check = array('flexible_shipping_single:4'); // Adjust with your shipping method IDs, you can get these in many way, easiest, inspect the checkbox on payment page
//
//        // List of payment methods to be disabled
//        $payment_methods_to_disable = array('cod'); // Adjust with your payment method IDs
//
//        if (in_array($chosen_shipping_method, $shipping_methods_to_check)) {
//            foreach ($payment_methods_to_disable as $method_id) {
//                if (isset($available_gateways[$method_id])) {
//                    unset($available_gateways[$method_id]);
//                }
//            }
//        }
//
//        // List of shipping methods for which you want to disable payment methods
//        $shipping_methods_to_check = array('flexible_shipping_single:13'); // Adjust with your shipping method IDs, you can get these in many way, easiest, inspect the checkbox on payment page
//
//        // List of payment methods to be disabled
//        $payment_methods_to_disable = array('monri', 'bacs'); // Adjust with your payment method IDs
//
//        if (in_array($chosen_shipping_method, $shipping_methods_to_check)) {
//            foreach ($payment_methods_to_disable as $method_id) {
//                if (isset($available_gateways[$method_id])) {
//                    unset($available_gateways[$method_id]);
//                }
//            }
//        }
//    }
//    return $available_gateways;
//}

function parseQueryStringToGetShopDomain() {
    $query_array = [];
    if( isset($_SERVER['QUERY_STRING']) ) {
        parse_str($_SERVER['QUERY_STRING'], $query_array);

        if (
            count($query_array) > 0 &&
            isset($query_array['market']) &&
            isset($query_array['language'])
        ) {
            switch ($query_array['market']) {
                case 'pl':
                    return 'globalparts.com.pl';
                case 'de':
                    return 'globalparts-24.de';
                case 'it':
                    return 'globalparts-24.it';
                case 'fr':
                    return 'globalparts.fr';
                case 'es':
                    return 'globalparts.es';
                case 'uk':
                default:
                    return 'globalparts.co.uk';
            }
        }
    }

    return false;
}

function getDomainByMarket( $market ) {
    switch ( $market ) {
        case 'pl':
            return 'globalparts.com.pl';
        case 'de':
            return 'globalparts-24.de';
        case 'it':
            return 'globalparts-24.it';
        case 'fr':
            return 'globalparts.fr';
        case 'es':
            return 'globalparts.es';
        case 'uk':
        default:
            return 'globalparts.co.uk';
    }
}

add_filter('woocommerce_get_return_url', 'customize_get_return_url', 10, 2);
add_filter( 'woocommerce_get_checkout_order_received_url', 'customize_get_return_url', 10, 2 );

//     <domena>/{en/pl/de}/{order/zamowienie/bestellung}?email={billing_email}&order_id={order_id}&order_key={order_key}
function customize_get_return_url( $return_url, $order ){
//    $host = 'https://global-parts-frontend.vercel.app/';

    if ( $order ) {
        $market = $order->get_meta('_market');
        $language = $order->get_meta('_language');

        if( ! $market ) { $market = 'uk'; }
        if( ! $language ) { $language = 'en'; }

        switch( $market ) {
            case 'pl':
                $order_i18n_url = 'https://pl.globalshop.fwch.pl//api/order';
                break;
            case 'de':
                $order_i18n_url = 'https://de.globalshop.fwch.pl//api/order';
                break;
            case 'it':
                $order_i18n_url = 'https://it.globalshop.fwch.pl//api/order';
                break;
            case 'fr':
                $order_i18n_url = 'https://fr.globalshop.fwch.pl//api/order';
                break;
            case 'es':
                $order_i18n_url = 'https://es.globalshop.fwch.pl//api/order';
                break;
            case 'uk':
            default:
                $order_i18n_url = 'https://uk.globalshop.fwch.pl//api/order';
                break;
        }

//        $url_return = $language . '/' . $order_i18n_url;
        $url_return = $order_i18n_url;

//        return $host . $url_return . '?email=' . $order->get_billing_email() . '&order_id=' . $order->get_ID() . '&order_key=' . $order->get_order_key();
        return $url_return . '?email=' . $order->get_billing_email() . '&order_id=' . $order->get_ID() . '&order_key=' . $order->get_order_key();
    }

    return $return_url;
//    return 'http://fabrykawchmurach.pl';
}

add_filter(
    'allowed_redirect_hosts',
    function( $allowed_hosts ) : array {
        $allowed_hosts[] = 'es.globalshop.fwch.pl';
        $allowed_hosts[] = 'pl.globalshop.fwch.pl';
        $allowed_hosts[] = 'uk.globalshop.fwch.pl';
        $allowed_hosts[] = 'fr.globalshop.fwch.pl';
        $allowed_hosts[] = 'it.globalshop.fwch.pl';
        return (array) $allowed_hosts;
    }
);

//add_filter('ppcp_create_order_request_body_data', static function (array $data): array {
//    $data['application_context']['brand_name'] = 'New Brand Name';
//
//    error_log( print_r( $data, true ) );
//
//    return $data;
//});

add_filter( 'woocommerce_get_checkout_url', 'custom_checkout_url', 30 );
function custom_checkout_url( $checkout_url ) {
    $query_array = [];
    if( isset($_SERVER['QUERY_STRING']) ) {
        parse_str($_SERVER['QUERY_STRING'], $query_array);

        if (
            count($query_array) > 0 &&
            isset($query_array['market']) &&
            isset($query_array['language'])
        ) {
            switch ($query_array['market']) {
                case 'pl':
                    $checkout_i18n_url = 'https://pl.globalshop.fwch.pl/';
                    break;
                case 'de':
                    $checkout_i18n_url = 'https://de.globalshop.fwch.pl/';
                    break;
                case 'it':
                    $checkout_i18n_url = 'https://it.globalshop.fwch.pl/';
                    break;
                case 'fr':
                    $checkout_i18n_url = 'https://fr.globalshop.fwch.pl/';
                    break;
                case 'es':
                    $checkout_i18n_url = 'https://es.globalshop.fwch.pl/';
                    break;
                case 'uk':
                default:
                    $checkout_i18n_url = 'https://uk.globalshop.fwch.pl/';
                    break;
            }

            return $checkout_i18n_url . 'checkout';
        }
    }

    return $checkout_url;
}

//add_filter('determine_locale', 'gp_determine_locale', 10, 1);
//function gp_determine_locale( $determined_locale ) {
//    $language         = isset($_GET['language'])?$_GET['language']:false;
//
//    switch( $language )
//    {
//        case 'pl':
//            return 'pl_PL';
//        case 'de':
//            return 'de_DE';
//        case 'it':
//            return 'it_IT';
//        case 'es':
//            return 'es_ES';
//        case 'fr':
//            return 'fr_FR';
//        case 'en':
//            return 'en_US';
//        default:
//            return $determined_locale;
//    }
//}

//function uniquePrefix_force_english_only_admin( $locale ) {
//    $locale = 'pl_PL';
//
//    return $locale;
//}
//
//add_filter( 'locale', 'uniquePrefix_force_english_only_admin', 1, 1 );

add_action(
    'rest_api_init',
    function () {
        global $locale;

        $language         = isset($_GET['language'])?$_GET['language']:false;

//        if( $language ) {
//
//        }

        $get_locale       = 'pl_PL';

        if( $language ) {
            switch ($language) {
                case 'pl':
                    $get_locale = 'pl_PL';
                    break;
                case 'de':
                    $get_locale = 'de_DE';
                    break;
                case 'it':
                    $get_locale = 'it_IT';
                    break;
                case 'es':
                    $get_locale = 'es_ES';
                    break;
                case 'fr':
                    $get_locale = 'fr_FR';
                    break;
                case 'en':
                    $get_locale = 'en_US';
                    break;
            }
        }

//            load_plugin_textdomain( 'woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
            $locale = $get_locale;
            setlocale( LC_ALL, $get_locale );

            $result = switch_to_locale( $get_locale );
//            setlocale(LC_ALL, $get_locale);
//            define('WPLANG', $get_locale);

//            global $wp_locale_switcher;
//            if ( ! $wp_locale_switcher ) {
//                error_log( 'No locale switcher!' );
//            } else {
//                error_log( ':Locale switcher!' );
//                $wp_locale_switcher->switch_to_locale( $get_locale );
//            }

//            error_log( 'REST_API_INIT ' . $language . ' ' . $get_locale . ' !' . get_locale() .' ' . ($result?'TRUE':'FALSE') );
//            error_log( 'REST_API_INIT ' . $get_locale  );
//        }
    }
);

/*
Remove recalculation of shipping cost when Add-to-Cart. This prevents slow add-to-cart.
*/
//function filter_need_shipping ($val) {
//    $prevent_after_add = WC()->cart->prevent_recalc_on_add_to_cart;
//    return $val && !$prevent_after_add;
//}
//add_filter( 'woocommerce_cart_needs_shipping', 'filter_need_shipping' );
//
//function mark_cart_not_to_recalc ($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
//    WC()->cart->prevent_recalc_on_add_to_cart = true;
//}
//add_action('woocommerce_add_to_cart', 'mark_cart_not_to_recalc', 10, 6);

add_filter('woocommerce_package_rates', 'change_shipping_method_name_based_on_shipping_class', 50, 2);
function change_shipping_method_name_based_on_shipping_class($rates, $package){
    $language         = isset($_GET['language'])?$_GET['language']:false;
    $cop_rates        = [ 482, 483, 491, 843, 846, 848, 867, 1053, 1054 ];
    $parcel_rates     = [ 1049 ];

    $cart_value = floatval( WC()->cart->subtotal );

    if(
        count($rates) == 0 &&
        isset($package['contents']) &&
        count($package['contents']) > 0
    ) {
        $cart_contents = [];
        $today = new DateTime();

        foreach( $package['contents'] as $package_content ) {
            $cart_contents[] = $package_content['product_id'];
        }

        $log_line = '[' . $today->format('Y-md H:i:s') . '] ' . $package['destination']['country'] . ': ' . implode( ',', $cart_contents ) . PHP_EOL;
        file_put_contents('woocommerce_package_rates.log', $log_line , FILE_APPEND | LOCK_EX);
    }

    foreach ( $rates as $rate_key => $rate ) {
        if(
            $cart_value < 90 && $rate->instance_id == 1058
        ) {
            unset( $rates[$rate_key] );
            continue;
        }

        if (
            'flat_rate' === $rate->method_id &&
            translateShippingMethodName( $rate->instance_id, $language )
        ) {
            $rates[$rate_key]->label = translateShippingMethodName( $rate->instance_id, $language );
        }

        if( in_array( $rate->instance_id, $cop_rates ) ) {
            $rates[$rate_key]->add_meta_data( 'rate_type', 'cop' );
        } else if( in_array( $rate->instance_id, $parcel_rates ) ) {
            $rates[$rate_key]->add_meta_data( 'rate_type', 'parcel' );
        } else {
            $rates[$rate_key]->add_meta_data( 'rate_type', 'courier' );
        }

        $company_delivers = [ 'inpost', 'royal mail', 'dhl', 'pocztex', 'dpd', 'suus', 'taat' ];
        $rate_company = 'other';

        foreach( $company_delivers as $company_deliver ) {
            if(str_contains(mb_strtolower($rates[$rate_key]->label), $company_deliver)) {
                $rate_company = $company_deliver;
            }
        }

        $rates[$rate_key]->add_meta_data( 'rate_company', $rate_company );
    }
    return $rates;
}

function translateShippingMethodName( $instance_id, $language ) {
    $translations = [
        12 => [ 'en' => 'Domestic TAAT', 'pl' => 'Krajowy TAAT' ],
        14 => [ 'en' => 'Other Courier', 'pl' => 'Inny kurier' ],
        481 => [ 'en' => 'Express Delivery (Other 24 Hour Courier)', 'pl' => 'Dostawa ekspresowa (inny kurier 24-godzinny)' ],
        482 => [ 'en' => 'Local pickup', 'pl' => 'Odbiór lokalny' ],
        483 => [ 'en' => 'Collection in Person', 'pl' => 'Odbiór osobisty' ],
        484 => [ 'en' => 'Standard Delivery (Royal Mail 24)', 'pl' => 'Przesyłka standardowa (Royal Mail 24)' ],
        485 => [ 'de' => 'Standardversand (Sonder-/Speditionsversand)', 'en' => 'Standard shipping (special/forwarding shipping)' ],
        486 => [ 'de' => 'Standardversand (Lieferung bis Bordsteinkante)', 'en' => 'Standard shipping (delivery to curbside)' ],
        487 => [ 'fr' => 'Livraison à domicile', 'en' => 'Home delivery' ],
        488 => [ 'fr' => 'Autre mode d\'envoi de courrier', 'en' => 'Other method of sending mail' ],
        489 => [ 'de' => 'Standardversand (Sonstige)', 'en' => 'Standard Shipping (Other)' ],
        490 => [ 'fr' => 'Écopli', 'en' => 'Écopli' ],
        491 => [ 'en' => 'Local pickup', 'pl' => 'Odbiór lokalny' ],
        492 => [ 'it' => 'Posta standard', 'en' => 'Ordinary package' ],
        493 => [ 'it' => 'Consegna a domicilio', 'en' => 'Home delivery' ],
        494 => [ 'es' => 'Otros (ver descripcion)', 'en' => 'Others (see description)' ],
        495 => [ 'es' => 'Entrega a domicilio', 'en' => 'Home delivery' ],
        496 => [ 'en' => 'GLS Standard' ],
        497 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        498 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        499 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        500 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        501 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        502 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        503 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        504 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        505 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        506 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        507 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        508 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        509 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        510 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        511 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        512 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        513 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        514 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        515 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        516 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        517 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        518 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        519 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        520 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        521 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        522 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        523 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        524 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        525 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        526 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        527 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        528 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        529 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        530 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        531 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        532 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        533 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        534 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        535 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        536 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        537 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        538 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        539 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        540 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        541 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        542 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        543 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        544 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        545 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        546 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        547 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        548 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        550 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        551 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        552 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        553 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        554 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        555 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        556 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        557 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        558 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        559 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        560 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        561 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        562 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        563 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        564 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        565 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        566 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        567 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        568 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        569 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        570 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        571 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        572 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        573 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        712 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        713 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        714 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        715 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        716 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        717 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        718 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        719 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        720 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        721 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        722 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        723 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        724 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        725 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        726 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        727 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        728 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        729 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        730 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        731 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        732 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        733 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        734 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        735 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        736 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        737 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        738 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        739 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        740 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        741 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        742 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        743 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        744 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        745 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        746 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        747 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        748 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        749 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        750 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        751 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        752 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        753 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        754 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        755 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        756 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        757 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        758 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        759 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        760 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        761 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        762 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        763 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        764 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        765 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        766 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        767 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        768 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        769 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        770 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        771 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        772 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        773 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        774 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        775 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        776 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        777 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        778 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        779 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        780 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        781 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        782 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        783 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        784 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        785 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        786 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        787 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        788 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        789 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        790 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        791 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        792 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        793 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        794 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        795 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        796 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        797 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        798 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        799 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        800 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        801 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        802 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        803 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        804 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        805 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        806 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        807 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        808 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        809 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        810 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        811 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        812 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        813 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        814 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        815 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        816 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        817 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        818 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        819 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        820 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        821 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        822 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        823 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        824 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        825 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        826 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        827 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        828 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        829 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        830 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        831 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        832 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        833 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        834 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        835 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        836 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        837 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        838 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        839 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        840 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        841 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        842 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        843 => [ 'en' => 'Collect in person', 'pl' => 'Odbiór osobisty' ],
        844 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        845 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        846 => [ 'en' => 'Collect in person', 'pl' => 'Odbiór osobisty' ],
        847 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        848 => [ 'en' => 'Collect in person', 'pl' => 'Odbiór osobisty' ],
        849 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        850 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        851 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        852 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        853 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        854 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        855 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        856 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        857 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        858 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        859 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        860 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        861 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        862 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        863 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        864 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        865 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        866 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        867 => [ 'en' => 'Collect in person', 'pl' => 'Odbiór osobisty' ],
        868 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        869 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        870 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        871 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        872 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        873 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        874 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        875 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        876 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        877 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        878 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        879 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        880 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        881 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        882 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        883 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        884 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        885 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        886 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        887 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        888 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        889 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        890 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        891 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        892 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        893 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        894 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        895 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        896 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        897 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        898 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        899 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        900 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        901 => [ 'en' => 'Other courier or delivery service', 'pl' => 'Inna usługa kurierska lub kurierska' ],
        902 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        903 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        904 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        905 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        906 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        907 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        908 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        909 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        910 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        911 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        913 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        914 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        915 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        916 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        917 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        918 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        919 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        920 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        921 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        922 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        923 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        924 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        925 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        926 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        927 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        928 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        929 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        930 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        931 => [ 'de' => 'Standardversand (Standard International)', 'en' => 'Standard Shipping (Standard International)' ],
        932 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        933 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        934 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        935 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        936 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        937 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        938 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        939 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        940 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        942 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        943 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        944 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        945 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        946 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        947 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        948 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        949 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        950 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        951 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        952 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        953 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        954 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        955 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        956 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        957 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        958 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        959 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        960 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        961 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        962 => [ 'fr' => 'La Poste - Colissimo International', 'en' => 'La Poste - International Shipping' ],
        963 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        964 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        965 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        966 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        967 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        968 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        969 => [ 'en' => 'Standard Delivery (Standard Int\'l Postage)', 'pl' => 'Przesyłka standardowa (standardowa przesyłka międzynarodowa)' ],
        970 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        971 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        972 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        973 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        974 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        975 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        976 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        977 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        979 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        980 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        982 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        983 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        984 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        985 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        986 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        987 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        988 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        989 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        991 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        992 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        993 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        994 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        995 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        996 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        997 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        998 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        999 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1000 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1001 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1002 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1003 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1004 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1005 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1006 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1007 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1008 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1009 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1010 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1011 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1012 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1013 => [ 'it' => 'Spedizione internazionale', 'en' => 'International shipping' ],
        1014 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1015 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1016 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1017 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1018 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1019 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1020 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1021 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1022 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1023 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1024 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1025 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1026 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1027 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1028 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1029 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1030 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1031 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1032 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1033 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1034 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1035 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1036 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1037 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1038 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1039 => [ 'es' => 'Envío internacional estándar', 'en' => 'Standard International Shipping' ],
        1040 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        1041 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        1042 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        1043 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        1044 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        1045 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        1046 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        1047 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        1048 => [ 'it' => 'Spedizione internazionale standard a prezzo fisso', 'en' => 'International shipping standard fixed price' ],
        1049 => [ 'pl' => 'Paczkomaty InPost', 'en' => 'InPost parcel lockers' ],
        1050 => [ 'pl' => 'Kurier DHL', 'en' => 'DHL courier' ],
        1051 => [ 'pl' => 'Kurier Pocztex', 'en' => 'Pocztex courier' ],
        1052 => [ 'pl' => 'Kurier DPD',  'en' => 'DPD courier' ],
        1053 => [ 'pl' => 'Kurier DPD pobranie',  'en' => 'DPD courier cash on delivery' ],
        1054 => [ 'pl' => 'Odbiór osobisty w punkcie sprzedawcy pobranie',  'en' => 'Personal pickup at the seller\'s point of delivery' ],
        1055 => [ 'pl' => 'SUUS Logistics', 'en' => 'SUUS Logistics' ],
        1056 => [ 'pl' => 'DHL Palety', 'en' => 'DHL Pallets' ],
    ];

//    error_log( $instance_id . ' ' . $language . $translations[$instance_id][$language] );

    return $translations[$instance_id][$language]??false;
}

// Woocommerce Shipment Mails
//add_action( 'woocommerce_order_status_processing_to_cancelled_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_on-hold_to_cancelled_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_completed_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_pending_to_on-hold_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_failed_to_on-hold_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_cancelled_to_on-hold_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_cancelled_to_processing_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_failed_to_processing_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_on-hold_to_processing_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_pending_to_processing_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_fully_refunded_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_partially_refunded_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_pending_to_failed_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_on-hold_to_failed_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_pending_to_completed_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_failed_to_completed_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//add_action( 'woocommerce_order_status_cancelled_to_completed_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
//function dartrax_prepare_locale_for_Mail_with_order_id( $order_id ) {
//    // Get language code that may be stored in post meta data
//    $language = get_post_meta($order_id, '_language', true);
//    if( $language == '' ) return '';
//
//    global $locale;
//
//    $language = 'pl_PL';
//    $locale = $language;
//    setlocale( LC_ALL, $language );
//    switch_to_locale( $language );
////    switch_text_domains( $language );
//    WC()->load_plugin_textdomain();
//}

add_filter( 'plugin_locale', 'global_get_locale', 99999, 2);
add_filter( 'plugin_locale', 'global_change_locale', 99999 );

function global_get_locale( $locale, $domain ) {
    $language = isset($_GET['language'])?$_GET['language']:false;
    if(
        isset( $_COOKIE['language'] ) &&
        $language === false
    )
    {
        $language = $_COOKIE['language'];
    }

    $get_locale = 'en_US';

    if( $language ) {
        switch ($language) {
            case 'pl':
                $get_locale = 'pl_PL';
                break;
            case 'de':
                $get_locale = 'de_DE';
                break;
            case 'it':
                $get_locale = 'it_IT';
                break;
            case 'es':
                $get_locale = 'es_ES';
                break;
            case 'fr':
                $get_locale = 'fr_FR';
                break;
            case 'en':
                $get_locale = 'en_US';
                break;
        }
    } else {
        $market = isset($_GET['market'])?$_GET['market']:false;
        if(
            isset( $_COOKIE['market'] ) &&
            $market === false
        )
        {
            $market = $_COOKIE['market'];
        }

        if( $market ) {
            switch ($market) {
                case 'pl':
                    $get_locale = 'pl_PL';
                    break;
                case 'de':
                    $get_locale = 'de_DE';
                    break;
                case 'it':
                    $get_locale = 'it_IT';
                    break;
                case 'es':
                    $get_locale = 'es_ES';
                    break;
                case 'fr':
                    $get_locale = 'fr_FR';
                    break;
                case 'uk':
                    $get_locale = 'en_US';
                    break;
            }
        }
    }

    return $get_locale;
}

function global_change_locale( $locale ) {
    $language = isset($_GET['language'])?$_GET['language']:false;
    if( isset( $_COOKIE['language'] ) && $language === false )
    {
        $language = $_COOKIE['language'];
    }

    $get_locale       = 'en_US';

    if( $language ) {
        switch ($language) {
            case 'pl':
                $get_locale = 'pl_PL';
                break;
            case 'de':
                $get_locale = 'de_DE';
                break;
            case 'it':
                $get_locale = 'it_IT';
                break;
            case 'es':
                $get_locale = 'es_ES';
                break;
            case 'fr':
                $get_locale = 'fr_FR';
                break;
            case 'en':
                $get_locale = 'en_US';
                break;
        }
    } else {
        $market = isset($_GET['market'])?$_GET['market']:false;
        if( isset( $_COOKIE['market'] ) && $market === false )
        {
            $market = $_COOKIE['market'];
        }

        if( $market ) {
            switch ($market) {
                case 'pl':
                    $get_locale = 'pl_PL';
                    break;
                case 'de':
                    $get_locale = 'de_DE';
                    break;
                case 'it':
                    $get_locale = 'it_IT';
                    break;
                case 'es':
                    $get_locale = 'es_ES';
                    break;
                case 'fr':
                    $get_locale = 'fr_FR';
                    break;
                case 'uk':
                    $get_locale = 'en_US';
                    break;
            }
        }
    }

    return $get_locale;
}

function global_get_market() {
    $market = isset($_GET['market'])?$_GET['market']:false;

    if( isset( $_COOKIE['market'] ) && $market === false )
    {
        $market = $_COOKIE['market'];
    }

    if( $market === false ) {
        $market = 'uk';
    }

    return $market;
}

//function global_get_locale( $locale, $domain ) {
//    global $locale;
//
//    $language         = isset($_GET['language'])?$_GET['language']:false;
//    $get_locale       = 'pl_PL';
//
//    if( $language ) {
//        switch ($language) {
//            case 'pl':
//                $get_locale = 'pl_PL';
//                break;
//            case 'de':
//                $get_locale = 'de_DE';
//                break;
//            case 'it':
//                $get_locale = 'it_IT';
//                break;
//            case 'es':
//                $get_locale = 'es_ES';
//                break;
//            case 'fr':
//                $get_locale = 'fr_FR';
//                break;
//            case 'en':
//                $get_locale = 'en_US';
//                break;
//        }
//    }
//
////    error_log( 'GLOBAL GET LOCALE: ' . $get_locale );
//
//    $locale = $get_locale;
//
//    return $get_locale;
//}
//
//function global_change_locale( $locale ) {
//    $language         = isset($_GET['language'])?$_GET['language']:false;
//    $get_locale       = 'pl_PL';
//
//    if( $language ) {
//        switch ($language) {
//            case 'pl':
//                $get_locale = 'pl_PL';
//                break;
//            case 'de':
//                $get_locale = 'de_DE';
//                break;
//            case 'it':
//                $get_locale = 'it_IT';
//                break;
//            case 'es':
//                $get_locale = 'es_ES';
//                break;
//            case 'fr':
//                $get_locale = 'fr_FR';
//                break;
//            case 'en':
//                $get_locale = 'en_US';
//                break;
//        }
//    }
//
////    error_log( 'GLOBAL GET LOCALE2: ' . $get_locale );
//
//    return $get_locale;
//}

//function switch_text_domains( $language ) {
//    global $locale, $woocommerce;
//
//    unload_textdomain( 'default' );
//    unload_textdomain( 'woocommerce' );
//
//    $locale	= $language;
//
//    load_default_textdomain( $language );
//    $woocommerce->load_plugin_textdomain();
//}

function baselinker_limit_market_by_user($args, $request)
{
    $user_id = get_current_user_id();

    if( $user_id == 2 ) {
        $args['meta_key'] = '_market';
        $args['meta_value'] = 'pl';
        $args['compare'] = 'LIKE';
    } else {
        $args['meta_key'] = '_market';
        $args['meta_value'] = 'uk';
        $args['compare'] = 'LIKE';
    }

    return $args;
}

add_filter('woocommerce_rest_shop_order_object_query', 'baselinker_limit_market_by_user', 10, 2);

add_filter('woocommerce_coupon_is_valid', 'woocommerce_coupon_is_valid_for_market', 10, 3);
function woocommerce_coupon_is_valid_for_market( $valid, $coupon, $discount ) {
    $market = global_get_market();

    $coupon_code = strtolower( $coupon->get_code() );
    $coupon_id = $coupon->get_id();

    if( $coupon_code == 'rabat5' && $market != 'pl' ) {
        return false;
    }

    if( $coupon_code == 'gp5' && $market != 'uk' ) {
        return false;
    }

    /**
     * GL-149 Coupon wyłącznie dla marketów - które go obsługują
     */
    $allowed_markets = get_post_meta( $coupon_id, 'markets', true );
    if ( !empty( $allowed_markets ) ) {
        $markets_array = array_map( 'trim', explode( ',', $allowed_markets ) );
        if ( ! in_array( $market, $markets_array ) ) {
            return false;
        }
    }

    return $valid;
}

add_filter('cwgsubscribe_raw_subject', 'translate_cwgsubscribe_raw_subject', 10, 2);
function translate_cwgsubscribe_raw_subject( $raw_subject, $subscriber_id ) {
    $language = get_post_meta( $subscriber_id, 'cwginstock_language', true );
    if( $language ) {
        switch( $language ) {
            case 'pl':
                $raw_subject = 'Powiadomimy Cię o dostępności {product_name}';
                break;
            case 'de':
                $raw_subject = 'Sie haben {product_name}abonniert';
                break;
        }
    }

    return $raw_subject;
}

add_filter('cwgsubscribe_raw_message', 'translate_cwgsubscribe_raw_message', 10, 2);
function translate_cwgsubscribe_raw_message( $raw_message, $subscriber_id ) {
    $language = get_post_meta( $subscriber_id, 'cwginstock_language', true );
    if( $language ) {
        switch( $language ) {
            case 'pl':
                $raw_message = 'Witaj {subscriber_name}, <br/><br/>'
                    . 'powiadomimy Cię o dostępności produktu <span style="color:#0c2b6b;font-weight:900">{product_name}</span>. Wyślemy Ci wiadomość e-mail, gdy produkt będzie ponownie dostępny.<br/><br/><br/>';
                break;
            case 'de':
                $raw_message = 'Sehr geehrter {subscriber_name}, <br/><br/>'
                    . 'vielen Dank für Ihr Abonnement von {product_name}. Wir senden Ihnen eine E-Mail, sobald das Produkt wieder auf Lager ist.';
                break;
        }
    }

    return $raw_message;
}

add_filter('cwginstock_raw_subject', 'translate_cwginstock_raw_subject', 10, 2);
function translate_cwginstock_raw_subject( $raw_subject, $subscriber_id ) {
    $language = get_post_meta( $subscriber_id, 'cwginstock_language', true );
    if( $language ) {
        switch( $language ) {
            case 'pl':
                $raw_subject = 'Produkt {product_name} jest ponownie dostępny';
                break;
            case 'de':
                $raw_subject = 'Produkt {product_name} ist wieder auf Lager';
                break;
        }
    }

    return $raw_subject;
}

add_filter('cwginstock_raw_message', 'translate_cwginstock_raw_message', 10, 2);
function translate_cwginstock_raw_message( $raw_message, $subscriber_id ) {
    $language = get_post_meta( $subscriber_id, 'cwginstock_language', true );
    if( $language ) {
        switch( $language ) {
            case 'pl':
                $raw_message = 'Witaj {subscriber_name}, <br/>'
					. "Dziękujemy za cierpliwość i wreszcie koniec czekania! <br/> Twój subskrybowany produkt {product_name} jest już z powrotem w magazynie! Mamy tylko ograniczoną ilość towaru, a ten e-mail nie jest gwarancją, że go otrzymasz, więc pospiesz się, aby zostać jednym ze szczęśliwych klientów, którym się to uda <br/> Dodaj ten produkt {product_name} bezpośrednio do koszyka <a href='{cart_link}'>{cart_link</a>";
                break;
            case 'de':
                $raw_message = 'Sehr geehrter {subscriber_name}, <br/>'
                    . "Vielen Dank für Ihre Geduld, das Warten hat endlich ein Ende! <br/> Ihr abonniertes Produkt {product_name} ist jetzt wieder auf Lager! Wir haben nur eine begrenzte Menge auf Lager und diese E-Mail ist keine Garantie dafür, dass Sie eines bekommen, also beeilen Sie sich, um einer der glücklichen Käufer zu sein, die es bekommen. <br/> Fügen Sie dieses Produkt {product_name} direkt zu Ihrem Warenkorb hinzu <a href='{cart_link}'>{cart_link}</a>";
                break;
        }
    }

    return $raw_message;
}

//add_filter( 'woocommerce_package_rates' , 'businessbloomer_sort_shipping_methods', 9999, 2 );
//function businessbloomer_sort_shipping_methods( $rates, $package ) {
//
//    if ( ! is_array( $rates ) ) return $rates;
//
//    uasort( $rates, function ( $a, $b ) {
//        if ( $a == $b ) return 0;
//        return ( $a->cost < $b->cost ) ? -1 : 1;
//    } );
//
//    return $rates;
//
//    // NOTE: BEFORE TESTING EMPTY YOUR CART
//
//}

add_filter( 'woocommerce_package_rates' , 'sort_shipping_method_by_cost_zero_empty_cost_last', 10, 2 );
function sort_shipping_method_by_cost_zero_empty_cost_last( $rates, $package ) {
    if ( empty( $rates ) || ! is_array( $rates ) ) return $rates;

    // Sort shipping methods based on cost
    uasort( $rates, function ( $a, $b ) {
        if ( $a == $b ) return 0;
        return ( $a->cost < $b->cost ) ? -1 : 1;
    } );

    $free = $zero = []; // Initializing

//    error_log( 'WYSYLKI!!!' );
//    error_log( print_r( $rates, true ) );

    // Loop through shipping rates
    foreach ( $rates as $rate_key => $rate ) {
        // For "free shipping" methods
        if (
//            'free_shipping' === $rate->method_id ||
            'flat_rate:482' === $rate->id ||
            'flat_rate:483' === $rate->id
        ) {
            // set them on a separated array
            $free[$rate_key] = $rate;

            // Remove "Free shipping" method from $rates array
            unset($rates[$rate_key]);
        }
        // For other shipping rates with zero cost
//        elseif ( $rate->cost == 0 ) {
//            // set them on a separated array
//            $zero[$rate_key] = $rate;
//
//            // Remove the current method from $rates array
//            unset($rates[$rate_key]);
//        }
    }

    // Merge zero cost and "free shipping" methods at the end if they exist
//    return ! empty( $free ) || ! empty( $zero ) ? array_merge($rates, $zero, $free) : $rates;
    return (! empty( $free ) ? array_merge($rates, $free) : $rates);
//    return $rates;
}

add_filter( 'woocommerce_validate_postcode' , 'disable_woocommerce_validate_postcode', 99, 3 );

function disable_woocommerce_validate_postcode( $valid, $postcode, $country ) {
    return true;
}

global $GP_LANGUAGE;
$GP_LANGUAGE = 'en_US';

// Woocommerce Shipment Mails
add_action( 'woocommerce_order_status_processing_to_cancelled_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_on-hold_to_cancelled_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_completed_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_pending_to_on-hold_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_failed_to_on-hold_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_cancelled_to_on-hold_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_cancelled_to_processing_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_failed_to_processing_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_on-hold_to_processing_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_pending_to_processing_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_fully_refunded_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_partially_refunded_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_pending_to_failed_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_on-hold_to_failed_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_pending_to_completed_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_failed_to_completed_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
add_action( 'woocommerce_order_status_cancelled_to_completed_notification','dartrax_prepare_locale_for_Mail_with_order_id', 5, 1 );
function dartrax_prepare_locale_for_Mail_with_order_id( $order_id ) {
    $language = get_post_meta($order_id, '_language', true);

    $today = new DateTime();
    $log_line = '[' . $today->format('Y-md H:i:s') . '] ORDER_STATUS: ' . $language . PHP_EOL;
    file_put_contents('woocommerce_package_rates.log', $log_line , FILE_APPEND | LOCK_EX);

    switch( $language ) {
        case 'pl':
            $lang_code = 'pl_PL';
            break;
        case 'de':
            $lang_code = 'de_DE';
            break;
        case 'it':
            $lang_code = 'it_IT';
            break;
        case 'es':
            $lang_code = 'es_ES';
            break;
        case 'fr':
            $lang_code = 'fr_FR';
            break;
        case 'en':
        default:
            $lang_code = 'en_US';
            break;
    }

    switch_text_domains( $lang_code );

    global $GP_LANGUAGE;
    $GP_LANGUAGE = $lang_code;
}

// Woocommerce Mails when resend by Admin
add_action( 'woocommerce_before_resend_order_emails','dartrax_prepare_locale_for_resend_Mails', 5, 2 );
function dartrax_prepare_locale_for_resend_Mails( $order, $mail_type ) {
    if( $mail_type == 'customer_invoice' )
        dartrax_prepare_locale_for_Mail_with_order_id( $order->get_id() );
}

// Woocommerce Note to customer Mail
add_action( 'woocommerce_new_customer_note_notification','dartrax_prepare_locale_for_note_Mails', 5, 1 );
function dartrax_prepare_locale_for_note_Mails( $note_and_order_id ) {
    dartrax_prepare_locale_for_Mail_with_order_id( $note_and_order_id['order_id'] );
}

// Override Locale when WooCommerce sends an eMail
add_filter( 'woocommerce_email_setup_locale', function() {
    // Override translatepress 'locale'-function because that does not work in Admin interface
    add_filter('locale','dartrax_force_trp_locale', 99999 + 1);
    // Switch text domains to load the correct .po/.mo-file based translations
    global $GP_LANGUAGE;

    $today = new DateTime();
    $log_line = '[' . $today->format('Y-md H:i:s') . '] Email Setup Locale: ' . $GP_LANGUAGE . PHP_EOL;
    file_put_contents('woocommerce_package_rates.log', $log_line , FILE_APPEND | LOCK_EX);

    switch_text_domains( $GP_LANGUAGE );
    return false;
} );
add_filter( 'woocommerce_email_restore_locale', function() {
    // Undo overriding of translatepress' 'locale'-function
    remove_filter('locale','dartrax_force_trp_locale', 99999 + 1);
    return true;
} );

// Override translatepress 'locale'-function because that does not deliver $TRP_LANGUAGE in Admin interface
function dartrax_force_trp_locale($locale) {
    global $GP_LANGUAGE;
    return $GP_LANGUAGE;
}

// Override 'plugin_locale'-function so Woocommerce won't use the admin profile language
function dartrax_force_woo_locale($locale, $plugin) {
    global $GP_LANGUAGE;
    return $plugin == 'woocommerce' ? $GP_LANGUAGE : $locale;
}

// Switch to another text domain. Inspired by https://gist.github.com/Jon007/5b90e78289899bc28e9c39c12ef56e60
function switch_text_domains( $language ) {
    $today = new DateTime();

    if ( class_exists( 'WooCommerce' ) ) {
        global $locale, $woocommerce;
        // unload plugin's textdomains
        unload_textdomain( 'default' );
        unload_textdomain( 'woocommerce' );
        unload_textdomain( 'przelewy24' );
        // set locale to order locale
        $locale	= apply_filters( 'locale', $language );
        // Woocommerce uses the admin profile language instead of the side language. Override with the desired language
        add_filter('plugin_locale', 'dartrax_force_trp_locale', 10, 2);
        // (re-)load plugin's textdomain with order locale
        load_default_textdomain( $language );
        $woocommerce->load_plugin_textdomain();
        $wp_locale = new \WP_Locale();
        // Clean up
        remove_filter('plugin_locale', 'dartrax_force_woo_locale', 10);
    }
}

add_filter( 'gettext', 'bbloomer_translate_woocommerce_strings_emails', 999 );

function bbloomer_translate_woocommerce_strings_emails( $translated ) {
    // Get strings and translate them into empty strings
    $translated = str_ireplace( 'Thanks for shopping with us.', '', $translated );
    $translated = str_ireplace( 'We hope to see you again soon.', '', $translated );
    $translated = str_ireplace( 'We look forward to fulfilling your order soon.', '', $translated );
    $translated = str_ireplace( 'Congratulations on the sale.', '', $translated );
    return $translated;
}

//remove sitename from email subject
add_filter('wp_mail', 'email_subject_remove_sitename');
function email_subject_remove_sitename($email) {
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    $email['subject'] = str_replace("[".$blogname."] - ", "", $email['subject']);
    $email['subject'] = str_replace("[".$blogname."]:", "", $email['subject']);
    $email['subject'] = str_replace("[".$blogname."]", "", $email['subject']);
    return $email;
}
