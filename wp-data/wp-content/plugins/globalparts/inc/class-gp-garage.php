<?php

defined( 'ABSPATH' ) || exit;

class Gp_Garage {
    protected int $user_id = -1;

    protected $vehicles = [];
    protected string $default_vehicle_id = '';

    public function __construct( $user_id ) {
        $this->user_id = $user_id;

        $garage_vehicles = get_user_meta( $user_id, 'gp_garage_vehicles', true );
        if( !empty($garage_vehicles) ) {
            if( is_array($garage_vehicles) ) {
                $garage_vehicles = $garage_vehicles[0];
            }

            $this->vehicles = json_decode(
                $garage_vehicles,
                true
            );
        }

        $this->default_vehicle_id = get_user_meta( $user_id, 'gp_garage_default_vehicle', true );
    }

    public function index() {
        $vehicles = $this->vehicles;

        foreach( $vehicles as $vehicle_index => $vehicle ) {
            $vehicles[ $vehicle_index ]['make'] = $this->utf8_ansi( $vehicle['make'] );
            $vehicles[ $vehicle_index ]['model'] = $this->utf8_ansi( $vehicle['model'] );
        }

        return $vehicles;
    }

    protected function validateRequestedData( $requested_data ) {
//        $no_empty_fields = [ 'make', 'model', 'type', 'chassis', 'variant', 'year', 'engine' ];
        $no_empty_fields = [ 'make', 'model', 'model_type' ];
        foreach( $no_empty_fields as $field ) {
            if( !isset($requested_data[$field]) || empty($requested_data[$field]) ) {
                return false;
            }
        }

        return true;
    }

    protected function escapeRequestedData( $requested_data ) {
        $allowed_fields = [
            'id', 'vrn', 'vin', 'make', 'make_slug', 'model', 'model_slug', 'model_type', 'model_type_slug',
            'type', 'chassis', 'variant', 'year', 'engine'
        ];

        foreach( $requested_data as $text_field_id => $text_field ) {
            if( !in_array($text_field_id, $allowed_fields) ) {
                unset( $requested_data[$text_field_id] );
            }

            $requested_data[$text_field_id] = sanitize_text_field( $text_field );
        }

        return $requested_data;
    }

    protected function updateUserMeta() {
        update_user_meta( $this->user_id, 'gp_garage_default_vehicle', $this->default_vehicle_id);
        return update_user_meta( $this->user_id, 'gp_garage_vehicles', json_encode(array_values($this->vehicles)));
    }

    protected function getVehicle( $vehicle_id ) {
        foreach( $this->vehicles as $vehicle ) {
            if( $vehicle['id'] == $vehicle_id ) {
                $vehicle['make'] = $this->utf8_ansi( $vehicle['make'] );
                $vehicle['model'] = $this->utf8_ansi( $vehicle['model'] );

                return $vehicle;
            }
        }

        return false;
    }

    public function setDefaultVehicle( $vehicle_id ) {
        $vehicle = $this->getVehicle( $vehicle_id );

        if( $vehicle ) {
            $this->default_vehicle_id = $vehicle_id;
            $this->updateUserMeta();

            return $vehicle_id;
        } else {
            return new WP_Error( 'bad_requested_data', __( 'Vehicle no exists!', 'gp' ), array( 'status' => 400 ) );
        }
    }

    public function removeDefaultVehicle( $vehicle_id ) {
        $vehicle = $this->getVehicle( $vehicle_id );

        if( $vehicle ) {
            $this->default_vehicle_id = '';
            $this->updateUserMeta();

            return $vehicle_id;
        } else {
            return new WP_Error( 'bad_requested_data', __( 'Vehicle no exists!', 'gp' ), array( 'status' => 400 ) );
        }
    }

    public function getDefaultVehicle() {
        $vehicle = $this->getVehicle( $this->default_vehicle_id );

        if( $vehicle ) {
            return $vehicle;
        } else {
            return reset($this->vehicles);
        }
    }

    public function getDefaultVehicleId() {
        return $this->default_vehicle_id;
    }

    public function add( $requested_data ) {
        $requested_data = $this->escapeRequestedData( $requested_data );

        if( !$this->validateRequestedData( $requested_data ) ) {
            return new WP_Error( 'bad_requested_data', __( 'Invalid input.', 'gp' ), array( 'status' => 400 ) );
        }

        $vehicle = [
            'id' => uniqid(),
            'vrn' => $requested_data['vrn'],
            'vin' => $requested_data['vin'],
            'make' => $requested_data['make'],
            'make_slug' => $requested_data['make_slug'],
            'model' => $requested_data['model'],
            'model_slug' => $requested_data['model_slug'],
            'model_type' => $requested_data['model_type'],
            'model_type_slug' => $requested_data['model_type_slug'],
            'type' => $requested_data['type'],
            'chassis' => $requested_data['chassis'],
            'variant' => $requested_data['variant'],
            'year' => $requested_data['year'],
            'engine' => $requested_data['engine'],
        ];

        $this->vehicles[] = $vehicle;

        if( $this->updateUserMeta() ) {
            return $vehicle;
        } else {
            return false;
        }
    }

    public function update( $requested_data ) {
        if( $requested_data->is_json_content_type() ) {
            $requested_body_data = $requested_data->get_json_params();
        } else {
            $requested_body_data = $requested_data->get_body_params();
        }
        $requested_params = $requested_data->get_url_params();
        $requested_body_data = $this->escapeRequestedData( $requested_body_data );

        if( !isset($requested_params['id']) ) {
            return new WP_Error( 'bad_requested_data', __( 'No id given.', 'gp' ), array( 'status' => 400 ) );
        }

        if( !$this->validateRequestedData( $requested_body_data ) ) {
            return new WP_Error( 'bad_requested_data', __( 'Invalid input.', 'gp' ), array( 'status' => 400 ) );
        }

        $current_vehicle = $this->get( $requested_params['id'] );

        if( !$current_vehicle ) {
            return new WP_Error( 'bad_requested_data', __( 'Vehicle no exists!', 'gp' ), array( 'status' => 400 ) );
        }

        $current_vehicle = array_replace( $current_vehicle, $requested_body_data );

        foreach( $this->vehicles as $vehicle_key => $vehicle ) {
            if( $vehicle['id'] == $requested_params['id'] ) {
                $this->vehicles[$vehicle_key] = $current_vehicle;
            }
        }

        return $this->updateUserMeta();
    }

    public function get( $vehicle_id ) {
        $vehicle = $this->getVehicle( $vehicle_id );

        if( $vehicle ) {
            return $vehicle;
        } else {
            return new WP_Error( 'bad_requested_data', __( 'Vehicle no exists!', 'gp' ), array( 'status' => 400 ) );
        }

    }

    public function delete( $vehicle_id ) {
        foreach( $this->vehicles as $vehicle_key => $vehicle ) {
            if( $vehicle['id'] == $vehicle_id ) {
                unset( $this->vehicles[ $vehicle_key ] );
                return $this->updateUserMeta();
            }
        }

        return new WP_Error( 'bad_requested_data', __( 'Vehicle no exists!', 'gp' ), array( 'status' => 400 ) );
    }

    public function deleteAll() {
        $this->vehicles = [];
        return $this->updateUserMeta();
    }

    private function utf8_ansi( $string = '' ) {

        $utf8_ansi2 = array(
            "u00c0" =>"À",
            "u00c1" =>"Á",
            "u00c2" =>"Â",
            "u00c3" =>"Ã",
            "u00c4" =>"Ä",
            "u00c5" =>"Å",
            "u00c6" =>"Æ",
            "u00c7" =>"Ç",
            "u00c8" =>"È",
            "u00c9" =>"É",
            "u00ca" =>"Ê",
            "u00cb" =>"Ë",
            "u00cc" =>"Ì",
            "u00cd" =>"Í",
            "u00ce" =>"Î",
            "u00cf" =>"Ï",
            "u00d1" =>"Ñ",
            "u00d2" =>"Ò",
            "u00d3" =>"Ó",
            "u00d4" =>"Ô",
            "u00d5" =>"Õ",
            "u00d6" =>"Ö",
            "u00d8" =>"Ø",
            "u00d9" =>"Ù",
            "u00da" =>"Ú",
            "u00db" =>"Û",
            "u00dc" =>"Ü",
            "u00dd" =>"Ý",
            "u00df" =>"ß",
            "u00e0" =>"à",
            "u00e1" =>"á",
            "u00e2" =>"â",
            "u00e3" =>"ã",
            "u00e4" =>"ä",
            "u00e5" =>"å",
            "u00e6" =>"æ",
            "u00e7" =>"ç",
            "u00e8" =>"è",
            "u00e9" =>"é",
            "u00ea" =>"ê",
            "u00eb" =>"ë",
            "u00ec" =>"ì",
            "u00ed" =>"í",
            "u00ee" =>"î",
            "u00ef" =>"ï",
            "u00f0" =>"ð",
            "u00f1" =>"ñ",
            "u00f2" =>"ò",
            "u00f3" =>"ó",
            "u00f4" =>"ô",
            "u00f5" =>"õ",
            "u00f6" =>"ö",
            "u00f8" =>"ø",
            "u00f9" =>"ù",
            "u00fa" =>"ú",
            "u00fb" =>"û",
            "u00fc" =>"ü",
            "u00fd" =>"ý",
            "u00ff" =>"ÿ");

        return strtr($string, $utf8_ansi2);
    }

}
