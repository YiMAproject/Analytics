<?php
namespace Analytics;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;

class Module implements
    InitProviderInterface,
    ServiceProviderInterface,
    ConfigProviderInterface,
    AutoloaderProviderInterface
{
    /**
     * Initialize workflow
     *
     * @param ModuleManagerInterface $manager
     *
     * @return void
     */
    public function init(ModuleManagerInterface $moduleModuleManager)
    {
        /** @var $moduleModuleManager \Zend\ModuleManager\ModuleManager */

        $moduleModuleManager->loadModule('yimaSettings');
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
               'Analytics.Client' => 'Analytics\Service\ClientFactory'
            ),
        );
    }

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }
}
