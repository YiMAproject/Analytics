<?php

namespace Analytics\Service;

use Analytics\Service\Client\GoogleDriver;
use Analytics\Service\Client\Interfaces\OauthClientInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClientFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OauthClientInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $settings \yimaSettings\Service\Settings */
        $settings = $serviceLocator->get('yimaSettings');
        $settings = $settings->get('analytics')
            ->getArrayCopy();

        $client = new GoogleDriver($settings);

        return $client;
    }
}
