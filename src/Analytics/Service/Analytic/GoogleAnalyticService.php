<?php

namespace Analytics\Service\Analytic;

use Analytics\Service\Analytic\Interfaces\ListenerAnalyticInterface;
use Analytics\Service\Client\Interfaces\ClientInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class GoogleAnalyticService implements ListenerAnalyticInterface
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * Set Service Client Object
     *
     * @param ClientInterface $client
     *
     * @return $this
     */
    public function setClient(ClientInterface $client)
    {
        $engine = $client->getEngine();
        if (! $engine instanceof \Google_Client)
            throw new \Exception(
                sprintf(
                    'The Client Object Must Instanceof Google_Client. %s given.',
                    get_class($client)
                )
            );

        $this->client = $client;
    }

    /**
     * Get Client Object
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    // -----------------------------------------------------------------------------------------------------

    /**
     * Get Traffic Keywords
     *
     * @return array
     */
    public function getTrafficKeywords()
    {
        // TODO: Implement getTrafficKeywords() method.
    }

    // -----------------------------------------------------------------------------------------------------

    /**
     * Set Start Date
     *
     * @param \DateTime $dateTime DateTime Object
     *
     * @return $this
     */
    public function setFromDate(\DateTime $dateTime)
    {
        // TODO: Implement setFromDate() method.
    }

    /**
     * Get Start Date
     *
     * @return \DateTime
     */
    public function getFromDate()
    {
        // TODO: Implement getFromDate() method.
    }

    /**
     * Set Last Date
     *
     * @param \DateTime $dateTime DateTime Object
     *
     * @return $this
     */
    public function setTillDate(\DateTime $dateTime)
    {
        // TODO: Implement setTillDate() method.
    }

    /**
     * Get Last Date
     *
     * @return \DateTime
     */
    public function getTillDate()
    {
        // TODO: Implement getTillDate() method.
    }

    // -----------------------------------------------------------------------------------------------------

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the SharedEventManager
     * implementation will pass this to the aggregate.
     *
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $events->attach('Zend\Mvc\Application', MvcEvent::EVENT_RENDER, array($this, 'onRenderAttachJScripts'), -1000);
        $events->attach('Zend\Mvc\Application', MvcEvent::EVENT_RENDER_ERROR, array($this, 'onRenderAttachJScripts'), -1000);
    }

    /**
     * Render Event
     * - attach track scripts
     *
     * @param MvcEvent $e
     * @return bool
     */
    public function onRenderAttachJScripts(MvcEvent $e)
    {
        if ($e->getResult() instanceof Response
            || !$e->getViewModel() instanceof ViewModel
        )
            // Nothing to do
            return false;

        /** @var $matchedRoute \Zend\Mvc\Router\Http\RouteMatch */
        $matchedRoute = $e->getRouteMatch();
        if ($matchedRoute->getMatchedRouteName() == \yimaAdminor\Module::ADMIN_DEFAULT_ROUTE_NAME)
            // we don't want to track admin area page visits
            return false;

        $sm = $e->getApplication()
            ->getServiceManager();

        $viewRenderer = $sm->get('ViewRenderer');
        $viewRenderer->inlineScript()->appendScript("
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
              (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

              ga('create', '"./*$this->_user->getAnalyticsProfileId().*/"', '"./*$this->_user->getDomain().*/"');
              ga('send', 'pageview');
        ");

        return true;
    }

    /**
     * Detach all previously attached listeners
     *
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        // TODO: Implement detachShared() method.
    }
}
