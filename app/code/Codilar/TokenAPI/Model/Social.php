<?php
namespace Codilar\TokenAPI\Model;

use Hybrid_Auth;

class Social extends \Mageplaza\SocialLogin\Model\Social
{
    public function logout($apiName, $area = null)
    {
        $config = [
            'base_url' => $this->apiHelper->getBaseAuthUrl($area),
            'providers' => [
                $apiName => $this->getProviderData($apiName)
            ],
            'debug_mode' => false,
            'debug_file' => BP . '/var/log/social.log'
        ];

        $auth = new Hybrid_Auth($config);
        $auth->logoutAllProviders();
        return $auth;

    }
}
