<?php

namespace Analytics\Service;

use Analytics\Service\Client\Google;
use Analytics\Service\Client\Interfaces\ClientOauthInterface;
use yimaSettings\DataStore\Entity\Converter\ArrayConverter;
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
        $settings = $serviceLocator->get('yimaSettings');
        $settings = $settings->using('analytics')
            ->fetch();

        $client = new Google($settings->getAs(new ArrayConverter()));
        $client->config()
            // Access Analytics Data as Readonly
            ->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'))
            ->setApplicationName('YiMa Analytics')
        ;

        $client->setServiceManager($serviceLocator);

        return $client;
    }
}
