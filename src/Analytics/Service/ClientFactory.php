<?php

namespace Analytics\Service;

use Analytics\Service\Client\GoogleClient;
use Analytics\Service\Client\Interfaces\OauthInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClientFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OauthInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $settings \yimaSettings\Service\Settings */
        $settings = $serviceLocator->get('yimaSettings');
        $settings = $settings->get('analytics')->getArrayCopy();

        $client = new GoogleClient($settings);

        return $client;
    }
}
