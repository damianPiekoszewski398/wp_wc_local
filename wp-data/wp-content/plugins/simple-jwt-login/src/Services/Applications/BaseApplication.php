<?php

namespace SimpleJWTLogin\Services\Applications;

use SimpleJWTLogin\Modules\SimpleJWTLoginSettings;
use SimpleJWTLogin\Modules\WordPressDataInterface;

class BaseApplication
{
    /**
     * @var string
     */
    protected $requestMethod;

    /**
     * @var array
     */
    protected $request = [];
    /**
     * @var WordPressDataInterface
     */
    protected $wordPressData;

    /**
     * @var SimpleJWTLoginSettings
     */
    protected $settings;

    /**
     * @param array $request
     * @param string $requestMethod
     * @param SimpleJWTLoginSettings $settings
     * @param WordPressDataInterface $wordPressData
     */
    public function __construct(
        $request,
        $requestMethod,
        SimpleJWTLoginSettings $settings,
        WordPressDataInterface $wordPressData
    ) {
        $this->request = $request;
        $this->wordPressData = $wordPressData;
        $this->settings = $settings;
        $this->requestMethod = $requestMethod;
    }

    /**
     * @param string $email
     * @param string $provider
     * @param string $username
     * @param string $social_user_id
     * @param string $nice_name
     * @return \WP_User
     * @throws \Exception
     */
    protected function createUser($email, $provider = 'google', $username = '', $social_user_id = '')
    {
        $nice_username = '';

        if( ! empty( $username ) ) {
            $nice_username = $username;
        }

        if( $provider == 'facebook' ) {
            $username = $email;
        } else {
            $username = "user_" . $this->randomString(6) . "_goo";
        }

        $password = $this->wordPressData->generatePassword(
            $this->settings->getRegisterSettings()->getRandomPasswordLength()
        );
        $user = $this->wordPressData->createUser(
            $username,
            $email,
            $password,
            $this->settings->getRegisterSettings()->getNewUSerProfile(),
            [
            ]
        );

        $this->wordPressData->addUserMeta( $user->ID, 'is_company', -1 );
        $this->wordPressData->addUserMeta( $user->ID, 'provider', $provider );

        if( ! empty($nice_username) ) {
            $nice_username_exploded = explode(' ', $nice_username, 2);

            $this->wordPressData->addUserMeta( $user->ID, 'first_name', $nice_username_exploded[0] );
            if( count($nice_username_exploded) == 2  ) {
                $this->wordPressData->addUserMeta( $user->ID, 'last_name', $nice_username_exploded[1] );
            }
        }

        if( ! empty($social_user_id) ) {
            $this->wordPressData->addUserMeta( $user->ID, 'social_user_id', $social_user_id );
        }

        return $user;
    }

    /**
     * @param int $length
     * @return string
     */
    private function randomString($length = 8)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charLength = strlen($chars);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[rand(0, $charLength - 1)];
        }

        return $result;
    }

    public function getRedirectUrlByState(): string
    {
        $redirectUrlToShop = '';

        $redirectLegend = [
            'pl' => 'https://pl.globalshop.fwch.pl/',
            'uk' => 'https://uk.globalshop.fwch.pl/',
            'de' => 'https://de.globalshop.fwch.pl/',
            'it' => 'https://it.globalshop.fwch.pl/',
            'es' => 'https://es.globalshop.fwch.pl/',
            'fr' => 'https://fr.globalshop.fwch.pl/',
        ];

        if( isset( $this->request['state'] ) && !empty( $this->request['state'] ) ) {
            $states_request = explode( '&', $this->request['state'] );
            $states = [];

            foreach( $states_request as $state_request ) {
                $state_key_value = explode('=', $state_request);

                if( count( $state_key_value ) == 2 ) {
                    $states[ strtolower( trim( $state_key_value[0] ) ) ] = strtolower( trim( $state_key_value[1] ) );
                }
            }

            if( isset( $states['domain'] ) && isset( $redirectLegend[ $states['domain'] ] ) ) {
                $redirectUrlToShop = $redirectLegend[ $states['domain'] ];
            }
        }

        return $redirectUrlToShop;
    }
}
