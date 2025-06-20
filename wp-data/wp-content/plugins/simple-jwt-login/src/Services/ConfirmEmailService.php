<?php

namespace SimpleJWTLogin\Services;

use Exception;
use Pelago\Emogrifier\CssInliner;
use Pelago\Emogrifier\HtmlProcessor\CssToAttributeConverter;
use Pelago\Emogrifier\HtmlProcessor\HtmlPruner;
use SimpleJWTLogin\ErrorCodes;
use SimpleJWTLogin\Modules\Settings\ResetPasswordSettings;
use SimpleJWTLogin\Modules\SimpleJWTLoginHooks;
use WP_REST_Response;

class ConfirmEmailService extends BaseService implements ServiceInterface
{
    public function makeAction()
    {
        if ($this->jwtSettings->getResetPasswordSettings()->isAuthKeyRequired()
            && $this->validateAuthKey() === false
        ) {
            throw  new Exception(
                sprintf(
                    __('Invalid Auth Code ( %s ) provided.', 'simple-jwt-login'),
                    $this->jwtSettings->getAuthCodesSettings()->getAuthCodeKey()
                ),
                ErrorCodes::ERR_RESET_PASSWORD_INVALID_AUTH_KEY
            );
        }

        switch ($this->serverHelper->getRequestMethod()) {
            case RouteService::METHOD_GET:
                return $this->confirmEmailUser();
            default:
                throw new Exception(
                    __('Route called with invalid request method.', 'simple-jwt-login'),
                    ErrorCodes::ERR_ROUTE_CALLED_WITH_INVALID_METHOD
                );
        }
    }

    /**
     * @return WP_REST_Response
     * @throws Exception
     */
    private function confirmEmailUser()
    {
        $this->validateConfirmEmailUser();

        $market           = isset($_GET['market'])?$_GET['market']:false;
        $language           = isset($_GET['language'])?$_GET['language']:false;
        $login_url_result = '?confirm=error';

        $user = $this->getUser();
        if( $this->wordPressData->isInstanceOfuser( $user ) ) {
            update_user_meta( $user->ID, 'jwt_is_activated', '1' );
            $login_url_result = '?confirm=success';
        }

        switch( $market ) {
            case 'pl':
                $login_i18n_url = 'https://pl.globalshop.fwch.pl/zaloguj';
                break;
            case 'de':
                $login_i18n_url = 'https://de.globalshop.fwch.pl/login';
                break;
            case 'it':
                $login_i18n_url = 'https://it.globalshop.fwch.pl/accesso';
                break;
            case 'fr':
                $login_i18n_url = 'https://fr.globalshop.fwch.pl/connexion';
                break;
            case 'es':
                $login_i18n_url = 'https://es.globalshop.fwch.pl/inicio-de-sesion';
                break;
            case 'uk':
            default:
                $login_i18n_url = 'https://uk.globalshop.fwch.pl/login';
                break;
        }

        $this->wordPressData->redirect( $login_i18n_url . $login_url_result );

        $response =  [
            'success' => true,
            'message' => __('User\'s email address is confirmed.', 'simple-jwt-login'),
        ];

        return $this->wordPressData->createResponse($response);
    }

    private function validateConfirmEmailUser()
    {
        if (empty($this->request['email'])) {
            throw new Exception(
                __('Missing email parameter.', 'simple-jwt-login'),
                ErrorCodes::ERR_MISSING_EMAIL_FOR_CHANGE_PASSWORD
            );
        }

        if (empty($this->request['code'])) {
            throw new Exception(
                __('Missing new_password parameter.', 'simple-jwt-login'),
                ErrorCodes::ERR_MISSING_NEW_PASSWORD_FOR_CHANGE_PASSWORD
            );
        }
    }


    /**
     * @return bool|\WP_User
     * @throws Exception
     */
    private function getUser()
    {
        $code = $this->wordPressData->sanitizeTextField($this->request['code']);
        $email = $this->wordPressData->sanitizeTextField($this->request['email']);

        $user = get_user_by( 'email', $email );

        if ( ! $user ) {
            return false;
        }

        $expiration_duration = apply_filters( 'password_reset_expiration', DAY_IN_SECONDS );

        $pass_request_time = get_user_meta( $user->ID, 'jwt_activation_code_time', true );
        $pass_key = get_user_meta( $user->ID, 'jwt_activation_code', true );

        $expiration_time = $pass_request_time + $expiration_duration;

        if ( ! $pass_key ) {
            return false;
        }

        $code_is_correct = ( $pass_key === $code );

        if ( $code_is_correct && $expiration_time && time() < $expiration_time ) {
            return $user;
        } elseif ( $code_is_correct && $expiration_time ) {
            return false;
        }

        return false;
    }
}
