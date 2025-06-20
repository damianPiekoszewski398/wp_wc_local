<?php

namespace SimpleJWTLogin\Services;

use Exception;
use SimpleJWTLogin\ErrorCodes;
use SimpleJWTLogin\Helpers\Jwt\JwtKeyFactory;
use SimpleJWTLogin\Libraries\JWT\JWT;
use SimpleJWTLogin\Modules\AuthCodeBuilder;
use SimpleJWTLogin\Modules\Settings\AuthenticationSettings;
use SimpleJWTLogin\Modules\SimpleJWTLoginHooks;
use SimpleJWTLogin\Modules\UserProperties;
use Pelago\Emogrifier\CssInliner;
use Pelago\Emogrifier\HtmlProcessor\CssToAttributeConverter;
use Pelago\Emogrifier\HtmlProcessor\HtmlPruner;
use WP_REST_Response;

class RegisterUserService extends BaseService implements ServiceInterface
{
    const ACTION_NAME_CREATE_USER = 1;

    /**
     * @return WP_REST_Response|null
     * @throws Exception
     */
    public function makeAction()
    {
        $this->validateRegisterUser();

        return $this->createUser();
    }

    /**
     * @SuppressWarnings(StaticAccess)
     * @return WP_REST_Response|null
     * @throws Exception
     */
    public function createUser()
    {
        $email = $this->wordPressData->sanitizeTextField($this->request['email']);
        $extraParameters = UserProperties::getExtraParametersFromRequest($this->request);
        $username = !empty($extraParameters['user_login'])
            ? $this->wordPressData->sanitizeTextField($extraParameters['user_login'])
            : $email;
        $user = false;

        if ($this->wordPressData->checkUserExistsByUsernameAndEmail($username, $email) == true) {
            $user = $this->wordPressData->getUserDetailsByEmail( $email );
            $activated = get_user_meta( $user->ID, 'jwt_is_activated', true );

            if( $activated ) {
                throw new Exception(
                    __('User already exists.', 'simple-jwt-login'),
                    ErrorCodes::ERR_REGISTER_USER_ALREADY_EXISTS
                );
            }
        }

        $password = $this->jwtSettings->getRegisterSettings()->isRandomPasswordForCreateUserEnabled()
            ? $this->wordPressData->generatePassword(
                $this->jwtSettings->getRegisterSettings()->getRandomPasswordLength()
            )
            : $this->wordPressData->sanitizeTextField($this->request['password']);

        $newUserRole = $this->jwtSettings->getRegisterSettings()->getNewUSerProfile();
        if (isset($this->request[$this->jwtSettings->getAuthCodesSettings()->getAuthCodeKey()])) {
            $authCodes = $this->jwtSettings->getAuthCodesSettings()->getAuthCodes();
            foreach ($authCodes as $code) {
                $authCodeBuilder = new AuthCodeBuilder($code);
                $authCodeKey = $this->jwtSettings->getAuthCodesSettings()->getAuthCodeKey();
                if ($authCodeBuilder->getCode() === $this->request[$authCodeKey]
                    && !empty($authCodeBuilder->getRole())
                ) {
                    $newUserRole = $authCodeBuilder->getRole();
                }
            }
        }

        if( $user ) {
            $user->user_login = $username;
            $user->user_pass = $password;
            $user->user_email = $email;

            wp_update_user( $user );
        } else {
            $user = $this->wordPressData->createUser(
                $username,
                $email,
                $password,
                $newUserRole,
                $extraParameters
            );
        }

        $userId = $this->wordPressData->getUserIdFromUser($user);

        if (!empty($this->request['user_meta'])) {
            $userMeta = $this->wordPressData->sanitizeTextField($this->request['user_meta']);
            if (is_array($this->request['user_meta'])) {
                $userMeta = $this->wordPressData->sanitizeArray($this->request['user_meta']);
            } elseif (is_string($this->request['user_meta'])) {
                $userMeta = json_decode($userMeta, true);
                if ($userMeta === null
                    && strpos($this->request['user_meta'], '\\"') !== false
                ) {
                    $userMeta = json_decode(
                        stripslashes(
                            $this->wordPressData->sanitizeTextField(
                                $this->request['user_meta']
                            )
                        ),
                        true
                    );
                }
            }

            $allowedUserMetaKeys = array_map(function ($value) {
                return trim($value);
            }, explode(',', $this->jwtSettings->getRegisterSettings()->getAllowedUserMeta()));

            if (is_array($userMeta) && !empty($userMeta)) {
                foreach ($userMeta as $metaKey => $metaValue) {
                    if (!in_array($metaKey, $allowedUserMetaKeys)) {
                        continue;
                    }
                    $this->wordPressData->updateUserMeta(
                        $userId,
                        $this->wordPressData->sanitizeTextField($metaKey),
                        $this->wordPressData->sanitizeTextField($metaValue)
                    );
                }
            }
        }

        if ($this->jwtSettings->getHooksSettings()->isHookEnable(SimpleJWTLoginHooks::REGISTER_ACTION_NAME)) {
            $this->wordPressData->triggerAction(SimpleJWTLoginHooks::REGISTER_ACTION_NAME, $user, $password);
        }

        $activation_code = md5( time() );

        update_user_meta( $userId, 'jwt_is_activated',         '0' );
        update_user_meta( $userId, 'jwt_activation_code',      $activation_code );
        update_user_meta( $userId, 'jwt_activation_code_time', time() );

        $this->sendConfirmEmail( $user, $activation_code );

        if ($this->jwtSettings->getLoginSettings()->isAutologinEnabled()
            && $this->jwtSettings->getRegisterSettings()->isForceLoginAfterCreateUserEnabled()
        ) {
            $this->wordPressData->loginUser($user);
            if ($this->jwtSettings->getHooksSettings()->isHookEnable(SimpleJWTLoginHooks::LOGIN_ACTION_NAME)) {
                $this->wordPressData->triggerAction(SimpleJWTLoginHooks::LOGIN_ACTION_NAME, $user);
            }

            return (new RedirectService())
                ->withRequest($this->request)
                ->withCookies($this->cookie)
                ->withSession($this->session)
                ->withSettings($this->jwtSettings)
                ->withUser($user)
                ->makeAction();
        }

        $userArray = $this->wordPressData->wordpressUserToArray($user);
        if (isset($userArray['user_pass'])) {
            unset($userArray['user_pass']);
        }

        $response = [
            'success' => true,
            'id' => $userId,
            'message' => __('User was successfully created.', 'simple-jwt-login'),
            'user' => $userArray,
            'roles' => $this->wordPressData->getUserRoles($user),
        ];

        if ($this->jwtSettings->getRegisterSettings()->isJwtEnabled()) {
            $payload = $this->initPayload($user);

            $response['jwt'] = JWT::encode(
                $payload,
                JwtKeyFactory::getFactory($this->jwtSettings)->getPrivateKey(),
                $this->jwtSettings->getGeneralSettings()->getJWTDecryptAlgorithm()
            );
        }

        if ($this->jwtSettings->getHooksSettings()
            ->isHookEnable(SimpleJWTLoginHooks::HOOK_RESPONSE_REGISTER_USER)
        ) {
            $response = $this->wordPressData
                ->triggerFilter(
                    SimpleJWTLoginHooks::HOOK_RESPONSE_REGISTER_USER,
                    $response,
                    $user
                );
        }

        return $this->wordPressData->createResponse($response);
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

    /**
     * @throws Exception
     */
    private function validateRegisterUser()
    {
        if ($this->jwtSettings->getRegisterSettings()->isRegisterAllowed() === false) {
            throw  new Exception(
                __('Register is not allowed.', 'simple-jwt-login'),
                ErrorCodes::ERR_REGISTER_IS_NOT_ALLOWED
            );
        }

        if ((
            $this->jwtSettings->getRegisterSettings()->isAuthKeyRequiredOnRegister()
                || isset($this->request[$this->jwtSettings->getAuthCodesSettings()->getAuthCodeKey()])
        ) && $this->validateAuthKey() === false
        ) {
            throw  new Exception(
                sprintf(
                    __('Invalid Auth Code ( %s ) provided.', 'simple-jwt-login'),
                    $this->jwtSettings->getAuthCodesSettings()->getAuthCodeKey()
                ),
                ErrorCodes::ERR_REGISTER_INVALID_AUTH_KEY
            );
        }

        $allowedIPs = $this->jwtSettings->getRegisterSettings()->getAllowedRegisterIps();
        if (!empty($allowedIPs) && !$this->serverHelper->isClientIpInList($allowedIPs)) {
            throw new Exception(
                sprintf(
                    __('This IP[%s] is not allowed to register users.', 'simple-jwt-login'),
                    $this->serverHelper->getClientIP()
                ),
                ErrorCodes::ERR_REGISTER_IP_IS_NOT_ALLOWED
            );
        }


        if (!isset($this->request['email'])
            || (
                !isset($this->request['password'])
                && $this->jwtSettings->getRegisterSettings()->isRandomPasswordForCreateUserEnabled() === false
            )
        ) {
            throw new Exception(
                __('Missing email or password.', 'simple-jwt-login'),
                ErrorCodes::ERR_REGISTER_MISSING_EMAIL_OR_PASSWORD
            );
        }

        if ($this->wordPressData->isEmail($this->request['email']) === false) {
            throw  new Exception(
                __('Invalid email address.', 'simple-jwt-login'),
                ErrorCodes::ERR_REGISTER_INVALID_EMAIL_ADDRESS
            );
        }

        if (!empty($this->jwtSettings->getRegisterSettings()->getAllowedRegisterDomain())) {
            $parts = explode(
                '@',
                $this->wordPressData->sanitizeTextField($this->request['email'])
            );
            if (!isset($parts[1])
                || !in_array(
                    $parts[1],
                    array_map(
                        'trim',
                        explode(',', $this->jwtSettings->getRegisterSettings()->getAllowedRegisterDomain())
                    )
                )
            ) {
                throw new Exception(
                    __('This website does not allows users from this domain.', 'simple-jwt-login'),
                    ErrorCodes::ERR_REGISTER_DOMAIN_FOR_USER
                );
            }
        }
    }

    /**
     * @SuppressWarnings(StaticAccess)
     * @param \WP_User $user
     *
     * @return array
     */
    private function initPayload($user)
    {
        if ($this->jwtSettings->getAuthenticationSettings()->isAuthenticationEnabled()) {
            return AuthenticateService::generatePayload(
                [],
                $this->wordPressData,
                $this->jwtSettings,
                $user
            );
        }

        $userEmail = $this->wordPressData
            ->getUserProperty($user, 'user_email');
        $userId = $this->wordPressData
            ->getUserProperty($user, 'ID');
        $username = $this->wordPressData
            ->getUserProperty($user, 'user_login');
        $iss = $this->jwtSettings
            ->getAuthenticationSettings()->getAuthIss();

        return [
            AuthenticationSettings::JWT_PAYLOAD_PARAM_EMAIL    => $userEmail,
            AuthenticationSettings::JWT_PAYLOAD_PARAM_ID       => $userId,
            AuthenticationSettings::JWT_PAYLOAD_PARAM_USERNAME => $username,
            AuthenticationSettings::JWT_PAYLOAD_PARAM_IAT      => time(),
            AuthenticationSettings::JWT_PAYLOAD_PARAM_ISS                    => $iss,
        ];
    }
}
