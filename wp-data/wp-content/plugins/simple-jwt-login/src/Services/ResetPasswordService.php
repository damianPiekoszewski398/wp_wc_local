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

class ResetPasswordService extends BaseService implements ServiceInterface
{
    public function makeAction()
    {
        if ($this->jwtSettings->getResetPasswordSettings()->isResetPasswordEnabled() === false) {
            throw  new Exception(
                __('Reset Password is not allowed.', 'simple-jwt-login'),
                ErrorCodes::ERR_RESET_PASSWORD_IS_NOT_ALLOWED
            );
        }

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
            case RouteService::METHOD_PUT:
                return $this->changeUserPassword();
            case RouteService::METHOD_POST:
                return $this->sendResetPassword();
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
    private function changeUserPassword()
    {
        $this->validateChangePassword();
        $newPassword = $this->wordPressData->sanitizeTextField($this->request['new_password']);
        $jwtAllowed = $this->jwtSettings->getResetPasswordSettings()->isJwtAllowed();
        if ($jwtAllowed === false && empty($this->request['code'])) {
            throw new Exception(
                __('Missing code parameter.', 'simple-jwt-login'),
                ErrorCodes::ERR_MISSING_CODE_FOR_CHANGE_PASSWORD
            );
        }

        $user = $this->getUser($jwtAllowed);
        $this->wordPressData->resetPassword($user, $newPassword);
        $response =  [
            'success' => true,
            'message' => __('User Password has been changed.', 'simple-jwt-login'),
        ];

        if ($this->jwtSettings->getHooksSettings()
                ->isHookEnable(SimpleJWTLoginHooks::HOOK_RESPONSE_CHANGE_USER_PASSWORD)
        ) {
            $response = $this->wordPressData
                ->triggerFilter(
                    SimpleJWTLoginHooks::HOOK_RESPONSE_CHANGE_USER_PASSWORD,
                    $response,
                    $user
                );
        }

        return $this->wordPressData->createResponse($response);
    }

    private function validateChangePassword()
    {
        if (empty($this->request['email'])) {
            throw new Exception(
                __('Missing email parameter.', 'simple-jwt-login'),
                ErrorCodes::ERR_MISSING_EMAIL_FOR_CHANGE_PASSWORD
            );
        }

        if (empty($this->request['new_password'])) {
            throw new Exception(
                __('Missing new_password parameter.', 'simple-jwt-login'),
                ErrorCodes::ERR_MISSING_NEW_PASSWORD_FOR_CHANGE_PASSWORD
            );
        }
    }

    /**
     * @return WP_REST_Response
     * @throws Exception
     */
    private function sendResetPassword()
    {
        $language         = isset($_GET['language'])?$_GET['language']:false;

        $this->validateSendResetPassword();
        $email = $this->wordPressData->sanitizeTextField($this->request['email']);

        $user = $this->wordPressData->getUserDetailsByEmail($email);
        if (empty($user)) {
            throw new Exception(
                __('Wrong user.', 'simple-jwt-login'),
                ErrorCodes::ERR_USER_NOT_FOUND_FOR_RESET_PASSWORD
            );
        }
        switch ($this->jwtSettings->getResetPasswordSettings()->getFlowType()) {
            case ResetPasswordSettings::FLOW_JUST_SAVE_IN_DB:
                $this->wordPressData->generateAndGetPasswordResetKey($user);
                $message = __('The Code has been saved into the database.', 'simple-jwt-login');
                break;
            case ResetPasswordSettings::FLOW_SEND_DEFAULT_WP_EMAIL:
                $this->wordPressData->sendDefaultWordPressResetPassword(
                    $this->wordPressData->getUserProperty($user, 'user_login')
                );
                $message = __('Reset password email has been sent.', 'simple-jwt-login');
                break;
            case ResetPasswordSettings::FLOW_SEND_CUSTOM_EMAIL:
                $code = $this->wordPressData->generateAndGetPasswordResetKey($user);
                $sendTo = $this->wordPressData->getUserProperty($user, 'user_email');

                if( $language ) {
                    switch( $language ) {
                        case 'pl':
                            $emailSubject = "Global Parts - Resetowanie hasła";
                            $emailTitle = "Resetowanie hasła";
                            $emailContent = "<p>Ktoś poprosił o zresetowanie hasła dla następującego konta: {{EMAIL}}<br/>
Jeżeli to był błąd, zignoruj tego e-maila i nic się nie stanie..</p>

<p>Twój kod resetowania hasła to: {{CODE}}</p>

<p>Ta prośba o zresetowanie hasła pochodzi z adresu IP: {{IP}}</p>";
                            break;
                        case 'de':
                            $emailSubject = "Global Parts - Passwort zurücksetzen";
                            $emailTitle = "Passwort zurücksetzen";
                            $emailContent = "<p>Jemand hat eine Passwortzurücksetzung für das folgende Konto angefordert: {{EMAIL}}<br/>
Wenn es ein Fehler war, ignorieren Sie diese E-Mail und es passiert nichts.</p>

<p>Ihr Code zum Zurücksetzen des Passworts lautet: {{CODE}}</p>

<p>Diese Anfrage zum Zurücksetzen des Passworts kommt von der IP-Adresse: {{IP}}</p>";
                            break;
                        case 'fr':
                            $emailSubject = "Global Parts - Réinitialisation du mot de passe";
                            $emailTitle = "Réinitialisation du mot de passe";
                            $emailContent = "<p>Quelqu'un a demandé une réinitialisation du mot de passe pour le compte suivant: {{EMAIL}}<br/>
S'il s'agissait d'une erreur, ignorez cet email et rien ne se passera.</p>

<p>Votre code de réinitialisation de mot de passe est:{{CODE}}</p>

<p>Cette demande de réinitialisation de mot de passe provient de l'adresse IP: {{IP}}</p>";
                            break;
                        case 'it':
                            $emailSubject = "Global Parts - Reimpostazione della password";
                            $emailTitle = "Reimpostazione della password";
                            $emailContent = "<p>Qualcuno ha richiesto la reimpostazione della password per il seguente account: {{EMAIL}}<br/>
Se si è trattato di un errore, ignora questa email e non accadrà nulla.</p>

<p>Il tuo codice di reimpostazione della password è: {{CODE}}</p>

<p>Questa richiesta di reimpostazione della password proviene dall'indirizzo IP: {{IP}}</p>";
                            break;
                        case 'es':
                            $emailSubject = "Global Parts - Restablecimiento de contraseña";
                            $emailTitle = "Restablecimiento de contraseña";
                            $emailContent = "<p>Alguien solicitó un restablecimiento de contraseña para la siguiente cuenta: {{EMAIL}}<br/>
Si fue un error, ignora este correo electrónico y no pasará nada.</p>

<p>Su código de restablecimiento de contraseña es: {{CODE}}</p>

<p>Esta solicitud de restablecimiento de contraseña proviene de la dirección IP: {{IP}}</p>";
                            break;
                        default:
                            $emailSubject = "Global Parts - Password reset";
                            $emailTitle = "Password reset";
                            $emailContent = "<p>Someone has requested a password reset for the following account: {{EMAIL}}<br/>
If this was a mistake, ignore this email and nothing will happen.</p>

<p>Your password reset code is: {{CODE}}</p>

<p>This password reset request originated from the IP address: {{IP}}</p>";
                    }

                    ob_start();
                    do_action( 'woocommerce_email_header', $emailTitle, $email );
                    print $emailContent;
                    do_action( 'woocommerce_email_footer', $email );
                    $emailBody = ob_get_contents();
                    ob_end_clean();

                    ob_start();
                    wc_get_template( 'emails/email-styles.php' );
                    $css = ob_get_contents();
                    ob_get_clean();

                    $css_inliner = CssInliner::fromHtml( $emailBody )->inlineCss( $css );

                    $dom_document = $css_inliner->getDomDocument();

                    HtmlPruner::fromDomDocument( $dom_document )->removeElementsWithDisplayNone();
                    $emailBody = CssToAttributeConverter::fromDomDocument( $dom_document )
                        ->convertCssToVisualAttributes()
                        ->render();
                } else {
                    $emailBody = $this->jwtSettings->getResetPasswordSettings()->getResetPasswordEmailBody();
                    $emailSubject = $this->jwtSettings->getResetPasswordSettings()->getResetPasswordEmailSubject();
                }

                if ($this->jwtSettings
                    ->getHooksSettings()
                    ->isHookEnable(SimpleJWTLoginHooks::RESET_PASSWORD_CUSTOM_EMAIL_TEMPLATE)
                ) {
                    $emailBody = $this->wordPressData->triggerFilter(
                        SimpleJWTLoginHooks::RESET_PASSWORD_CUSTOM_EMAIL_TEMPLATE,
                        $emailBody,
                        $this->request
                    );
                }
                $emailBody = $this->replaceVariablesInEmailBody(
                    $emailBody,
                    $user,
                    $code
                );
                $emailType = $this->jwtSettings->getResetPasswordSettings()->getResetPasswordEmailType();

                $sendAsHtml = $emailType === ResetPasswordSettings::EMAIL_TYPE_HTML;
                $this->wordPressData->sendEmail($sendTo, $emailSubject, $emailBody, $sendAsHtml);

                $message = __('Reset password email has been sent.', 'simple-jwt-login');
                break;
            default:
                throw new Exception(
                    __('Invalid flow type.', 'simple-jwt-login'),
                    ErrorCodes::ERR_RESET_PASSWORD_INVALID_FLOW
                );
        }

        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($this->jwtSettings->getHooksSettings()
            ->isHookEnable(SimpleJWTLoginHooks::HOOK_RESPONSE_SEND_RESET_PASSWORD)
        ) {
            $response = $this->wordPressData->triggerFilter(
                SimpleJWTLoginHooks::HOOK_RESPONSE_SEND_RESET_PASSWORD,
                $response,
                $user
            );
        }

        return $this->wordPressData->createResponse($response);
    }

    /**
     * @throws Exception
     */
    private function validateSendResetPassword()
    {
        if (empty($this->request['email'])) {
            throw new Exception(
                __('Missing email parameter.', 'simple-jwt-login'),
                ErrorCodes::ERR_MISSING_NEW_PASSWORD_FOR_RESET_PASSWORD
            );
        }
    }

    /**
     * @param string $emailBody
     * @param \WP_User $user
     * @param string $code
     *
     * @return mixed
     */
    private function replaceVariablesInEmailBody($emailBody, $user, $code)
    {
        $variables = array_keys($this->jwtSettings->getResetPasswordSettings()->getEmailContentVariables());
        foreach ($variables as $variableKey) {
            switch ($variableKey) {
                case "{{CODE}}":
                    $replace = $code;
                    break;
                case "{{NAME}}":
                    $replace = $this->wordPressData->getUserProperty($user, 'first_name')
                               . $this->wordPressData->getUserProperty($user, 'last_name');
                    break;
                case "{{EMAIL}}":
                    $replace = $this->wordPressData->getUserProperty($user, 'user_login');
                    break;
                case "{{NICKNAME}}":
                    $replace = $this->wordPressData->getUserProperty($user, 'nickname');
                    break;
                case "{{FIRST_NAME}}":
                    $replace = $this->wordPressData->getUserProperty($user, 'first_name');
                    break;
                case "{{LAST_NAME}}":
                    $replace = $this->wordPressData->getUserProperty($user, 'last_name');
                    break;
                case "{{SITE}}":
                    $replace = $this->wordPressData->getSiteUrl();
                    break;
                case "{{IP}}":
                    $replace = $this->serverHelper->getClientIP();
                    break;
                default:
                    $replace = $variableKey;
                    break;
            }

            if ($replace === null) {
                $replace = $variableKey;
            }

            $emailBody = str_replace($variableKey, $replace, $emailBody);
        }

        return $emailBody;
    }

    /**
     * @param bool $jwtAllowed
     * @return bool|\WP_User
     * @throws Exception
     */
    private function getUser($jwtAllowed)
    {
        if ($jwtAllowed && empty($this->request['code'])) {
            $this->jwt = $this->getJwtFromRequestHeaderOrCookie();
            if (empty($this->jwt)) {
                throw new Exception(
                    __('The `jwt` parameter is missing.', 'simple-jwt-login'),
                    ErrorCodes::ERR_MISSING_JWT_AUTH_VALIDATE
                );
            }
            $loginParameter = $this->validateJWTAndGetUserValueFromPayload(
                $this->jwtSettings->getLoginSettings()->getJwtLoginByParameter()
            );
            $user = $this->getUserDetails($loginParameter);
            if (empty($user)
                || $this->wordPressData->getUserProperty($user, 'user_email') !== $this->request['email']
            ) {
                throw new Exception(
                    __('This JWT can not change your password.', 'simple-jwt-login')
                );
            }

            $this->validateJwtRevoked(
                $this->wordPressData->getUserProperty($user, 'ID'),
                $this->jwt
            );

            return $user;
        }

        $code = $this->wordPressData->sanitizeTextField($this->request['code']);
        $user = $this->wordPressData->checkPasswordResetKeyByEmail(
            $code,
            $this->wordPressData->sanitizeTextField($this->request['email'])
        );
        if (empty($user)) {
            throw new Exception(
                __('Invalid code provided.', 'simple-jwt-login'),
                ErrorCodes::ERR_INVALID_RESET_PASSWORD_CODE
            );
        }

        return $user;
    }
}
