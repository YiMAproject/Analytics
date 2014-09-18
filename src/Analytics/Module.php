<?php
namespace Analytics;

use Analytics\Service\Analytic\Interfaces\ListenerAnalyticInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\ModuleEvent;
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
     * @param \Zend\ModuleManager\ModuleManagerInterface $moduleModuleManager
     * @internal param \Zend\ModuleManager\ModuleManagerInterface $manager
     *
     * @return void
     */
    public function init(ModuleManagerInterface $moduleModuleManager)
    {
        $moduleModuleManager->loadModule('yimaSettings');

        // attach analytic listeners
        $events = $moduleModuleManager->getEventManager();
        $events->attach(
            ModuleEvent::EVENT_LOAD_MODULES_POST,
            array($this,'onLoadModulesPostAttachListeners'),
            -100000
        );
    }

    /**
     * Attach Analytic Listener if available
     *
     */
    public function onLoadModulesPostAttachListeners(ModuleEvent $e)
    {
        $sm = $e->getParam('ServiceManager');
        $analytic = $sm->get('Analytics.Service');

        if ($analytic instanceof ListenerAnalyticInterface) {
            /** @var $moduleManager \Zend\ModuleManager\ModuleManager */
            $moduleManager = $e->getTarget();
            /** @var $sharedEvents \Zend\EventManager\SharedEventManager */
            $sharedEvents = $moduleManager->getEventManager()
                ->getSharedManager();

            $sharedEvents->attachAggregate($analytic);
        }
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
               'Analytics.Client'  => 'Analytics\Service\ClientFactory',
               'Analytics.Service' => 'Analytics\Service\AnalyticServiceFactory',
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
