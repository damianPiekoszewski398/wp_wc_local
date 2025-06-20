<?php

use SimpleJWTLogin\Modules\Settings\SettingsErrors;
use SimpleJWTLogin\Modules\SimpleJWTLoginSettings;
use SimpleJWTLogin\Services\Applications\Google;
use SimpleJWTLogin\Services\RouteService;

if (! defined('ABSPATH')) {
    /**
    @phpstan-ignore-next-line
     */
    exit;
}
// @Generic.Files.LineLength

/**
 * @var SettingsErrors $settingsErrors
 * @var SimpleJWTLoginSettings $jwtSettings
 * @var string $pluginDirUrl
 */
?>
<form method="GET" action="<?php echo 'https://www.facebook.com/v19.0/dialog/oauth';?>" class="simple-jwt-login-oauth-app facebook">
    <input type="hidden" name="client_id" value="<?php echo esc_attr($jwtSettings->getApplicationsSettings()->getFacebookClientID());?>" />
    <input type="hidden" name="response_type" value="code" />
    <input type="hidden" name="scope" value="email" />
    <input type="hidden" name="state" value="domain=pl&language=pl" />
<!--    <input type="hidden" name="redirect_uri" value="--><?php //echo $jwtSettings->generateExampleLink(RouteService::OAUTH_TOKEN, ['provider' => 'facebook']);?><!--" />-->
    <input type="hidden" name="redirect_uri" value="https://testshop.fwch.pl/?rest_route=/simple-jwt-login/v1/oauth/token&provider=facebook" />
<!--    <input type="hidden" name="state" value="rest_route=/simple-jwt-login/v1/oauth/token&provider=facebook"" />-->
    <button name="facebook-auth" class="simple-jwt-login-auth-btn">
        <img src="<?php echo $pluginDirUrl;?>/images/applications/facebook-64x64.png" alt="facebook logo"/>
        <span class="simple-jwt-login-auth-txt">
            <?php echo __('Continue with Facebook', 'simple-jwt-login');?>
        </span>
    </button>
</form>
<?php
