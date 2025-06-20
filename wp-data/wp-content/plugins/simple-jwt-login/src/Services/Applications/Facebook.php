<?php

namespace SimpleJWTLogin\Services\Applications;

use Exception;
use SimpleJWTLogin\ErrorCodes;
use SimpleJWTLogin\Helpers\Jwt\JwtKeyFactory;
use SimpleJWTLogin\Libraries\JWT\JWT;
use SimpleJWTLogin\Libraries\ServerCall;
use SimpleJWTLogin\Services\AuthenticateService;
use SimpleJWTLogin\Services\RouteService;

class Facebook extends BaseApplication implements ApplicationInterface
{
    const IIS = "accounts.facebook.com";
    const AUTH_URL = "https://www.facebook.com/v19.0/dialog/oauth";
//    const CHECK_TOKEN_URL = "https://oauth2.googleapis.com/tokeninfo?id_token=%s";
    const CHECK_TOKEN_URL = "https://graph.facebook.com/v19.0/oauth/access_token?%s";
    const ME_ACCESS_TOKEN_URL = "https://graph.facebook.com/me?fields=email,name,id&access_token=%s";

    public function validate()
    {
        if (!isset($this->request['code']) && !isset($this->request['id_token'])) {
            throw new Exception(
                __('The code or id_token parameter is missing from request.', 'simple-jwt-login'),
                ErrorCodes::ERR_MISSING_FACEBOOK_PARAM
            );
        }
    }

    /**
     * @SuppressWarnings(StaticAccess)
     * @param string $idToken
     * @return void
     * @throws Exception
     */
    public static function validateIdToken($idToken)
    {
        $statusCode = 400;
        $plainResult = '';
        ServerCall::get(
            sprintf(self::CHECK_TOKEN_URL, $idToken),
            [],
            $statusCode,
            $plainResult
        );
        if ($statusCode != 200) {
            throw new Exception(
                __("The provided id_token is invalid", 'simple-jwt-login'),
                ErrorCodes::ERR_GOOGLE_INVALID_ID_TOKEN
            );
        }
    }

    public function me($access_token)
    {

        $statusCode = 400;
        $plainResult = '';

//        print sprintf(self::ME_ACCESS_TOKEN_URL, $access_token);

        $jsonResult = ServerCall::get(
            sprintf(self::ME_ACCESS_TOKEN_URL, $access_token),
            [],
            $statusCode,
            $plainResult
        );

        if ($statusCode != 200) {
            throw new Exception(
                __("The provided access_token is invalid", 'simple-jwt-login'),
                ErrorCodes::ERR_FACEBOOK_INVALID_CODE
            );
        }

        return [
            'status_code' => $statusCode,
            'response' => $jsonResult,
        ];
    }

    /**
     * @SuppressWarnings(StaticAccess)
     * @throws \Exception
     */
    public function call()
    {
        switch (true) {
            case $this->requestMethod == ServerCall::REQUEST_METHOD_GET:
                // This will generate the oauth Link
                $this->handleOauth($this->request['code']);
                break;
            case !empty($this->request['code']):
                $result = $this->exchangeCode(
                    $this->request['code'],
                    $this->settings->getApplicationsSettings()->getFacebookExchangeCodeRedirectUri()
                );

                $responseStatusCode = $result['status_code'];
                $jsonResult = $result['response'];

                if ($responseStatusCode == 200) {
                    return [
                        'success' => true,
                        'data' => $jsonResult,
                    ];
                }
                throw new Exception(
                    __(
                        'The code you provided is invalid.' . $this->handleErrorMessage($jsonResult),
                        'simple-jwt-login'
                    ),
                    ErrorCodes::ERR_GOOGLE_INVALID_CODE
                );
            case !empty($this->request['id_token']):
                $access_token = $this->request['id_token'];

                $me = $this->me($access_token);
                $email = $me['response']['email'];

                if( empty( $email ) && ! empty( $me['response']['id'] ) ) {
                    $email = $me['response']['id'] . '@facebook.com';

                    $user = $this->wordPressData->getUserByUserLogin(
                        $this->wordPressData->sanitizeTextField($email)
                    );
                } else {
                    $user = $this->wordPressData->getUserDetailsByEmail(
                        $this->wordPressData->sanitizeTextField($email)
                    );
                }

                if (empty($user)) {
                    throw new Exception(
                        __('Wrong user credentials.', 'simple-jwt-login'),
                        ErrorCodes::ERR_GOOGLE_USER_NOT_FOUND
                    );
                }

                $payload = AuthenticateService::generatePayload(
                    [],
                    $this->wordPressData,
                    $this->settings,
                    $user
                );

                $response = [
                    'success' => true,
                    'data' => [
                        'jwt' => JWT::encode(
                            $payload,
                            JwtKeyFactory::getFactory($this->settings)->getPrivateKey(),
                            $this->settings->getGeneralSettings()->getJWTDecryptAlgorithm()
                        )
                    ]
                ];

                return $response;
        }

        return [];
    }

    /**
     * @SuppressWarnings(StaticAccess)
     * @param string $code
     * @param string $redirectUri
     * @return array
     */
    public function exchangeCode($code, $redirectUri)
    {
        $params = [
            'body' => [
                'client_id' => $this->settings->getApplicationsSettings()->getFacebookClientID(),
                'client_secret' => $this->settings->getApplicationsSettings()->getFacebookClientSecret(),
                'redirect_uri' => 'https://testshop.fwch.pl/?rest_route=%2Fsimple-jwt-login%2Fv1%2Foauth%2Ftoken&provider=facebook',
                'code' => $code
            ],
        ];

        $responseStatusCode = 500;
        $plainResult = null;
        $jsonResult = ServerCall::post(
            "https://graph.facebook.com/v19.0/oauth/access_token",
            $params,
            $responseStatusCode,
            $plainResult
        );

        return [
            'status_code' => $responseStatusCode,
            'response' => $jsonResult,
        ];
    }

    /**
     * @SuppressWarnings(StaticAccess)
     * Handle OAuth code and redirects to the correct page
     *
     * @param string $code
     */
    public function handleOauth($code)
    {
        try {
            $redirectUri = $this->settings->generateExampleLink(
                RouteService::OAUTH_TOKEN,
                ['provider' => 'facebook']
            );
            $result = $this->exchangeCode($code, str_replace("&amp;", "&", $redirectUri));

            $responseStatusCode = $result['status_code'];
            $jsonResult = $result['response'];

            if ($responseStatusCode !== 200) {
                $this->wordPressData->redirect($this->wordPressData->getLoginURL([
                    'error' => $this->handleErrorMessage($jsonResult)
                ]));

                return;
            }

            $redirectUrlToShop = $this->getRedirectUrlByState();

            $me = $this->me($jsonResult['access_token']);

//            $jwt = JWT::extractDataFromJwt($jsonResult['id_token']);
            $email = $me['response']['email'];
//            $login = $me['response']['id'];

            if( empty( $email ) && ! empty( $me['response']['id'] ) ) {
                $email = $me['response']['id'] . '@facebook.com';

                $user = $this->wordPressData->getUserByUserLogin($email);
            } else {
                $user = $this->wordPressData->getUserDetailsByEmail($email);
            }

            if ($user == null) {
                if ($this->settings->getApplicationsSettings()->isFacebookCreateUserIfNotExistsEnabled()) {
                    $user = $this->createUser($email, 'facebook', $me['response']['name'], $me['response']['id']);

                    $this->wordPressData->loginUser($user);

//                    $payload = AuthenticateService::generatePayload(
//                        [],
//                        $this->wordPressData,
//                        $this->settings,
//                        $user
//                    );
//                    $jwt_token = JWT::encode(
//                        $payload,
//                        JwtKeyFactory::getFactory($this->settings)->getPrivateKey(),
//                        $this->settings->getGeneralSettings()->getJWTDecryptAlgorithm()
//                    );
////                    $this->wordPressData->redirect( 'http://localhost:3000/api/auth?provider=facebook&id_token=' . $jsonResult['id_token'] );
//                    $this->wordPressData->redirect($this->wordPressData->getAdminUrl());

                    if( ! empty( $redirectUrlToShop ) ) {
                        $this->wordPressData->redirect( $redirectUrlToShop . '/api/auth?provider=facebook&id_token=' . $jsonResult['access_token'] );
                    } else {
                        if( $email == 'blazej.wodecki@boringowl.io' || $email == 'blazej@wodecki.dev' || $email == '6614310472003064@facebook.com' ) {
                            $this->wordPressData->redirect( 'http://localhost:3000/api/auth?provider=facebook&id_token=' . $jsonResult['access_token'] );
                        } else {
                            $this->wordPressData->redirect( 'https://global-parts-frontend.vercel.app/api/auth?provider=facebook&id_token=' . $jsonResult['access_token'] );
                        }
                    }

                    return;
                }

                $this->wordPressData->redirect($this->wordPressData->getLoginURL([]));

                return;
            }

            $this->wordPressData->loginUser($user);

//            $payload = AuthenticateService::generatePayload(
//                [],
//                $this->wordPressData,
//                $this->settings,
//                $user
//            );
//
//            $jwt_token = JWT::encode(
//                $payload,
//                JwtKeyFactory::getFactory($this->settings)->getPrivateKey(),
//                $this->settings->getGeneralSettings()->getJWTDecryptAlgorithm()
//            );

            if( ! empty( $redirectUrlToShop ) ) {
                $this->wordPressData->redirect($redirectUrlToShop . '/api/auth?provider=facebook&id_token=' . $jsonResult['access_token']);
            } else {
                if( $email == 'blazej.wodecki@boringowl.io' || $email == 'blazej@wodecki.dev' || $email == '6614310472003064@facebook.com' ) {
                    $this->wordPressData->redirect( 'http://localhost:3000/api/auth?provider=facebook&id_token=' . $jsonResult['access_token'] );
                } else {
                    $this->wordPressData->redirect( 'https://global-parts-frontend.vercel.app/api/auth?provider=facebook&id_token=' . $jsonResult['access_token'] );
                }
            }
//            $this->wordPressData->redirect($this->wordPressData->getAdminUrl());

            return;
        } catch (Exception $e) {
//            $this->wordPressData->redirect($this->wordPressData->getLoginURL(['error' => $e->getMessage()]));

            $this->wordPressData->redirect( 'http://localhost:3000/api/auth?error=' . $e->getMessage() );
        }
    }

    /**
     * @param string[] $jsonResult
     * @return string
     */
    private function handleErrorMessage($jsonResult)
    {
        $error = "";

        if (isset($jsonResult['error_description'])) {
            $error = ucfirst($jsonResult['error_description']) . ".";
        }
        if (isset($jsonResult['error'])) {
//            $error .= ($error === "" ? " " : "") . ucfirst($jsonResult['error']);
            $error .= ($error === "" ? " " : "") . json_encode($jsonResult['error']);
        }

        return $error;
    }
}
