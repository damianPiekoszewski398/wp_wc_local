<?php

use SimpleJWTLogin\Modules\Settings\SettingsErrors;
use SimpleJWTLogin\Modules\SimpleJWTLoginSettings;
use SimpleJWTLogin\Services\RouteService;

if (!defined('ABSPATH')) {
    /**
     * @phpstan-ignore-next-line
     */
    exit;
}
// @Generic.Files.LineLength

/**
 * @var SettingsErrors $settingsErrors
 * @var SimpleJWTLoginSettings $jwtSettings
 */
?>
<div class="row">
    <div class="col-md-6">
        <h3 class="sub-section-title">
            Facebook <span class="beta">beta</span>
            <?php
            echo isset($errorCode)
            && (
                $settingsErrors->generateCode(
                    SettingsErrors::PREFIX_APPLICATIONS,
                    SettingsErrors::ERR_GOOGLE_CLIENT_ID_REQUIRED
                ) === $errorCode
                || $settingsErrors->generateCode(
                    SettingsErrors::PREFIX_APPLICATIONS,
                    SettingsErrors::ERR_GOOGLE_CLIENT_SECRET_REQUIRED
                ) === $errorCode
            )
                ? '<span class="simple-jwt-error">!</span>'
                : '';
            ?>
        </h3>
        <p class="text-muted">
            <?php
            echo __(
                'Integrate Facebook OAuth into your WordPress website.',
                'simple-jwt-login'
            );
            ?>
        </p>
    </div>
    <div class="col-md-6 text-right">
        <div class="facebook logo">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h3 class="sub-section-title">
            <?php echo __('Allow Facebook', 'simple-jwt-login'); ?>
        </h3>
        <div class="form-group app-settings">
            <input type="radio" id="social_facebook_enabled_no" name="facebook[enabled]" class="form-control"
                   value="0"
                <?php echo $jwtSettings->getApplicationsSettings()->isFacebookEnabled() === false ? 'checked' : ''; ?>
            />
            <label for="social_facebook_enabled_no">
                <?php echo __('No', 'simple-jwt-login'); ?>
            </label>

            <input type="radio" id="social_facebook_enabled_yes" name="facebook[enabled]" class="form-control"
                   value="1"
                <?php
                echo($jwtSettings->getApplicationsSettings()->isFacebookEnabled()
                    ? 'checked'
                    : ''
                );
                ?>
            />
            <label for="social_facebook_enabled_yes">
                <?php echo __('Yes', 'simple-jwt-login'); ?>
            </label>
        </div>
    </div>
</div>
<hr/>

<div class="row">
    <div class="col-md-12">
        <h3 class=section-title>Credentials</h3>
        <div class="form-group">
            <label for="facebook_client_id"><b>Client ID</b> <span class="required">*</span></label>
            <input
                    type="text"
                    name="facebook[client_id]"
                    id="facebook_client_id"
                    class="form-control"
                    value="<?php echo esc_attr($jwtSettings->getApplicationsSettings()->getFacebookClientID()); ?>"
                    placeholder="<?php echo esc_attr(__('Client ID', 'simple-jwt-login')); ?>"
            />

            <label for="facebook_client_secret"><b>Client Secret</b> <span class="required">*</span></label>
            <input
                    type="text"
                    class="form-control"
                    name="facebook[client_secret]"
                    id="facebook_client_secret"
                    value="<?php echo esc_attr($jwtSettings->getApplicationsSettings()->getFacebookClientSecret()); ?>"
                    placeholder="<?php echo esc_attr(__('Client Secret', 'simple-jwt-login')); ?>"
            />
        </div>
    </div>
</div>
<hr/>

<div class="row">
    <div class="col-md-12">
        <h3 class="section-title">OAuth on Login/Register</h3>
        <p>
            <?php
            echo __(
                'This option will display the "Login with facebook" button on WordPress login.',
                'simple-jwt-login'
            );
            ?>
        </p>
        <div class="form-group">
            <input type="checkbox" name="facebook[enable_oauth]" id="facebook_enable_oauth"
                   value="1"
                <?php
                echo $jwtSettings->getApplicationsSettings()->isOauthEnabled()
                    ? 'checked="checked"'
                    : ""
                ?>
            />
            <label for="facebook_enable_oauth">
                <?php echo esc_html(__('Enable OAuth on WordPress login', 'simple-jwt-login')); ?>
            </label>
            <p>
                <?php
                echo __(
                    sprintf(
                        "In order for the OAuth flow to be successfull, please make sure you set the following Redirect URI in <a href='%s' target='_blank'>facebook console</a>:",
                        "https://console.cloud.facebook.com/"
                    ),
                    'simple-jwt-login'
                );
                ?>
            </p>
            <div class="generated-code">
                <span class="code">
                <?php
                $sampleUrlParams = [
                    'provider' => 'facebook',
                ];
                echo esc_html($jwtSettings->generateExampleLink(RouteService::OAUTH_TOKEN, $sampleUrlParams));
                ?>
            </span>
                <span class="copy-button">
                <button class="btn btn-secondary btn-xs">
                    <?php echo __('Copy', 'simple-jwt-login'); ?>
                </button>
            </span>
            </div>
        </div>
    </div>
</div>
<hr>


<div class="row">
    <div class="col-md-12">
        <h3 class="section-title">
            <?php echo esc_html(__('Exchange Facebook OAuth "code" with Facebook "id_token"', 'simple-jwt-login')); ?>
        </h3>
    </div>
    <div class="col-md-12">
        <p>
            <?php
            echo __(
                'This route allows you to exchange the code obtained in the Oauth flow, with a Facebook id_token.',
                'simple-jwt-login'
            );
            ?>
        </p>
        <p>
            <input type="checkbox" name="facebook[enable_exchange_code]" id="facebook_enable_exchange_code"
                   value="1"
                <?php
                echo $jwtSettings->getApplicationsSettings()->isFacebookExchangeCodeEnabled()
                    ? 'checked="checked"'
                    : ""
                ?>
            />
            <label for="facebook_enable_exchange_code">
                <?php echo __('Enable Exchange Facebook `code` with Facebook id_token', 'simple-jwt-login'); ?>
            </label>
        </p>
    </div>
    <div class="col-md-12">
        <p>
            <label for="facebook_redirect_uri_exchange_code">Redirect URI</label>
            <input
                    type="text"
                    id="facebook_redirect_uri_exchange_code"
                    class="form-control"
                    name="facebook[redirect_uri_exchange_code]"
                    value="<?php echo esc_attr($jwtSettings->getApplicationsSettings()->getFacebookExchangeCodeRedirectUri()); ?>"
                    placeholder="<?php echo __('Redirect URI', 'simple-jwt-login'); ?>"
            />
        </p>
    </div>
    <div class="col-md-12">
        <p class="text-muted">
            Parameters:<br/>
            <b>provider</b> -> <?php echo __('facebook', 'simple-jwt-login'); ?><br/>
            <b>code</b> -> <?php echo __('the code you received from OAuth flow', 'simple-jwt-login'); ?><br/>
        </p>
        <div class="generated-code">
            <span class="method">POST:</span>
            <span class="code">
                <?php
                $sampleUrlParams = [
                    'provider' => 'facebook',
                    'code' => __('your_code ', 'simple-jwt-login')
                ];

                echo esc_html($jwtSettings->generateExampleLink(RouteService::OAUTH_TOKEN, $sampleUrlParams));
                ?>
                </span>
            <span class="copy-button">
                    <button class="btn btn-secondary btn-xs">
                        <?php echo __('Copy', 'simple-jwt-login'); ?>
                    </button>
                </span>
        </div>
        <hr/>
    </div>
</div>
<hr/>

<div class="row">
    <h3 class="section-title">
        <?php echo esc_html(__('Exchange Facebook "id_token" with a WordPress "jwt"', 'simple-jwt-login')); ?>
    </h3>
    <br/>
    <p>
        <?php
        echo esc_html(
            __(
                'This route allows you to exchange the Facebook `id_token` with a Simple-JWT-Login JWT',
                'simple-jwt-login'
            )
        );
        ?>
    </p>
    <p>
        <input type="checkbox" name="facebook[enable_exchange_id_token]" id="facebook_enable_exchange_id_token"
               value="1"
            <?php
            echo $jwtSettings->getApplicationsSettings()->isFacebookExchangeIdTokenEnabled()
                ? esc_html('checked="checked"')
                : ""
            ?>
        />
        <label for="facebook_enable_exchange_id_token">
            <?php echo __('Enable Exchange Facebook id_token with a WordPress JWT', 'simple-jwt-login'); ?>
        </label>
    </p>
    <p class="text-muted">
        Parameters:<br/>
        <b>provider</b> -> <?php echo esc_html('facebook');?><br/>
        <b>id_token</b> -> <?php echo __('the `id_token` from your OAuth process', 'simple-jwt-login'); ?><br/>
    </p>
    <div class="generated-code">
        <span class="method">POST:</span>
        <span class="code">
                <?php
                $sampleUrlParams = [
                    'provider' => esc_html('facebook'),
                    'id_token' => __('facebook_id_token ', 'simple-jwt-login')
                ];

                echo esc_html($jwtSettings->generateExampleLink(RouteService::OAUTH_TOKEN, $sampleUrlParams));
                ?>
            </span>
        <span class="copy-button">
                <button class="btn btn-secondary btn-xs">
                    <?php echo __('Copy', 'simple-jwt-login'); ?>
                </button>
            </span>
    </div>
</div>

<hr/>
<div class="row">
    <div class="col-md-12">
        <h3 class="section-title">
            <?php echo __('Other options', 'simple-jwt-login'); ?>
        </h3>
    </div>
    <div class="col-md-12">
        <input type="checkbox" name="facebook[allow_on_all_endpoints]" id="facebook_all_endpoints"
               value="1"
            <?php
            echo $jwtSettings->getApplicationsSettings()->isFacebookJwtAllowedOnAllEndpoints()
                ? 'checked="checked"'
                : ""
            ?>
        />
        <label for="facebook_all_endpoints">
            <?php echo __('Allow usage of Facebook id_token on all endpoints', 'simple-jwt-login'); ?>
        </label><br/>
        <p class="text-muted">
            * <?php
            echo __(
                'This option will allow the usage of Facebook `id_token` on all endpoints.',
                'simple-jwt-login'
            );
            echo "&nbsp;";
            echo __(
                'The plugin will search for the user that has the email with the one specified in the JWT payload.',
                'simple-jwt-login'
            );
            echo '<br />';
            echo __(
                'In order for this option to work, you also need to enable the `All WordPress endpoints checks for JWT authentication` from General.',
                'simple-jwt-login'
            );
            ?>
        </p>
    </div>
    <div class="col-md-12">
        <input type="checkbox" name="facebook[create_user_if_not_exists]" id="facebook_create_user_if_not_exists"
               value="1"
            <?php
            echo $jwtSettings->getApplicationsSettings()->isFacebookCreateUserIfNotExistsEnabled()
                ? 'checked="checked"'
                : ""
            ?>
        />
        <label for="facebook_create_user_if_not_exists">
            <?php echo __('Create user if not exists', 'simple-jwt-login'); ?>
        </label><br/>
        <p class="text-muted">
            * <?php
            echo __(
                'This option will allow to create a new user if the email from JWT is not assigned to a WordPress user.',
                'simple-jwt-login'
            );
            ?>
        </p>
    </div>
</div>
