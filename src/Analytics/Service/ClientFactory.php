<?php

namespace Analytics\Service;

use Analytics\Service\Client\Google;
use Analytics\Service\Client\Interfaces\ClientOauthInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClientFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ClientOauthInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $settings \yimaSettings\Service\Settings */
        $settings = $serviceLocator->get('yimaSettings');
        $settings = $settings->get('analytics')
            ->getArrayCopy();

        $client = new Google($settings);
        $client->config()
            // Access Analytics Data as Readonly
            ->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));

        return $client;
    }
}
