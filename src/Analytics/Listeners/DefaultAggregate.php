<?php
namespace Analytics\Listeners;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class DefaultAggregate implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'onRenderAttachJScripts'), -1000);
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
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
}
