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

class resendConfirmEmailService extends BaseService implements ServiceInterface
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
                return $this->resendConfirmEmailUser();
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
    private function resendConfirmEmailUser()
    {
        $this->validateResendConfirmEmailUser();

        $market           = isset($_GET['market'])?$_GET['market']:false;
        $language           = isset($_GET['language'])?$_GET['language']:false;
        $login_url_result = '?resend=error';

        $result_status = false;
        $result_message = __('Error resending email.', 'simple-jwt-login');

        $user = $this->getUser();
        if( $user ) {
            $login_url_result = '?resend=success';

            $result_status = true;
            $result_message = __('Email resent successfully.', 'simple-jwt-login');

            $code = get_user_meta( $user->ID, 'jwt_activation_code', true );
            $this->sendConfirmEmail( $user, $code );
        }

//        switch( $market ) {
//            case 'pl':
//                $login_i18n_url = 'https://devshop.globalparts.com.pl/zaloguj';
//                break;
//            case 'de':
//                $login_i18n_url = 'https://devshop.globalparts-24.de/login';
//                break;
//            case 'it':
//                $login_i18n_url = 'https://devshop.globalparts-24.it/accesso';
//                break;
//            case 'fr':
//                $login_i18n_url = 'https://devshop.globalparts.fr/connexion';
//                break;
//            case 'es':
//                $login_i18n_url = 'https://devshop.globalparts.es/inicio-de-sesion';
//                break;
//            case 'uk':
//            default:
//                $login_i18n_url = 'https://devshop.globalparts.co.uk/login';
//                break;
//        }
//
//        $this->wordPressData->redirect( $login_i18n_url . $login_url_result );

        $response =  [
            'success' => $result_status,
            'message' => $result_message
        ];

        return $this->wordPressData->createResponse($response);
    }

    private function validateResendConfirmEmailUser()
    {
        if (empty($this->request['email'])) {
            throw new Exception(
                __('Missing email parameter.', 'simple-jwt-login'),
                ErrorCodes::ERR_MISSING_EMAIL_FOR_CHANGE_PASSWORD
            );
        }
    }


    /**
     * @return bool|\WP_User
     * @throws Exception
     */
    private function getUser()
    {
        $email = $this->wordPressData->sanitizeTextField($this->request['email']);

        $user = get_user_by( 'email', $email );

        if ( ! $user ) {
            return false;
        }

        $activated = get_user_meta( $user->ID, 'jwt_is_activated', true );

        // only for no activated account can resend mail

        if( $activated ) {
            return false;
        }

        return $user;
    }

    private function sendConfirmEmail( $user, $code )
    {
        $language         = isset($_GET['language'])?$_GET['language']:false;
        $market           = isset($_GET['market'])?$_GET['market']:false;

        if( ! $market ) { $market = 'uk'; }
        if( ! $language ) { $language = 'en'; }

        $email = $this->wordPressData->getUserProperty($user, 'user_email');

        $confirm_url = 'https://testshop.fwch.pl/?rest_route=/simple-jwt-login/v1/user/confirm&email=' . $email . '&code=' . $code . '&market=' . $market . '&language=' . $language;
        $confirm_url = str_replace('+','%2B', $confirm_url);

        switch( $language ) {
            case 'pl':
                $emailSubject = "Global Parts - Aktywacja konta";
                $emailTitle = "Aktywacja konta";
                $emailContent = "<p>Kliknij <a href=\"" . $confirm_url . "\">tutaj</a>, aby zweryfikować swój adres e-mail <strong>{{EMAIL}}</strong> w GlobalParts.</p>

<p>Jeśli masz problem, spróbuj skopiować poniższy link bezpośrednio do przeglądarki: " . $confirm_url . "</p>";
                break;
            case 'de':
                $emailSubject = "Global Parts - Bitte aktivieren Sie Ihr Konto";
                $emailTitle = "Bitte aktivieren Sie Ihr Konto";
                $emailContent = "<p>Klicken Sie <a href=\"" . $confirm_url . "\">hier</a>, um Ihre E-Mail-Adresse <strong>{{EMAIL}}</strong> bei GlobalParts zu bestätigen.</p>

<p>Wenn Sie ein Problem haben, kopieren Sie den folgenden Link direkt in Ihren Browser:: " . $confirm_url . "</p>";
                break;
            case 'fr':
                $emailSubject = "Global Parts - Veuillez activer votre compte";
                $emailTitle = "Veuillez activer votre compte";
                $emailContent = "<p>Cliquez <a href=\"" . $confirm_url . "\">ici</a> pour vérifier votre adresse e-mail <strong>{{EMAIL}}</strong> sur GlobalParts.</p>

<p>Si vous rencontrez un problème, essayez de copier le lien ci-dessous directement dans votre navigateur: " . $confirm_url . "</p>";
                break;
            case 'it':
                $emailSubject = "Global Parts - Per favore attiva il tuo account";
                $emailTitle = "Per favore attiva il tuo account";
                $emailContent = "<p>Clicca <a href=\"" . $confirm_url . "\">qui</a> per verificare il tuo indirizzo email <strong>{{EMAIL}}</strong> su GlobalParts.</p>

<p>Se hai un problema, prova a copiare il link sottostante direttamente nel tuo browser: " . $confirm_url . "</p>";
                break;
            case 'es':
                $emailSubject = "Global Parts - Por favor activa tu cuenta";
                $emailTitle = "Por favor activa tu cuenta";
                $emailContent = "<p>Haga clic <a href=\"" . $confirm_url . "\">aquí</a> para verificar su dirección de correo electrónico <strong>{{EMAIL}}</strong> en GlobalParts.</p>

<p>Si tiene algún problema, intente copiar el siguiente enlace directamente en su navegador: " . $confirm_url . "</p>";
                break;
            default:
                $emailSubject = "Global Parts - Please activate your account";
                $emailTitle = "Please activate your account";
                $emailContent = "<p>Click <a href=\"" . $confirm_url . "\">here</a> to verify your email <strong>{{EMAIL}}</strong> on GlobalParts.</p>

<p>If you have a problem, try copying the link below directly into your browser: " . $confirm_url . "</p> ";
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

        $emailBody = $this->replaceVariablesInEmailBody(
            $emailBody,
            $user,
            $code
        );

        $sendAsHtml = true;
        $this->wordPressData->sendEmail($email, $emailSubject, $emailBody, $sendAsHtml);
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
}
