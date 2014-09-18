<?php

namespace Analytics\Service;

use Analytics\Service\Client\Google;
use Analytics\Service\Client\Interfaces\ClientOauthInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AnalyticServiceFactory implements FactoryInterface
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
        /** @var $client \Analytics\Service\Client\Google */
        $client = $serviceLocator->get('Analytics.Client');

        $analytic = new GoogleAnalyticService();
        $analytic->setClient($client);

        return $analytic;
    }
}
