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
     * @var \Google_Service_Analytics
     */
    protected $analytics;

    /**
     * @var Analytics Account Name
     */
    protected $analytics_account_name = 'ZUOO';

    /**
     * @var Analytics Account Profile Domain
     */
    protected $analytics_account_profile_domain;

    /**
     * @var Analytic Profile TrackID
     */
    protected $analytic_account_profile_trackid;

    /**
     * Set Service Client Object
     *
     * @param ClientInterface $client
     *
     * @throws \Exception
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

    /**
     * The client object is used to create an authorized analytics service object.
     *
     * @return Google_Service_Analytics
     * @throws \Exception
     */
    protected function getAnalyticsEngine()
    {
        if (!$this->getClient())
            throw new \Exception('No Analytics Client Provided Yet.');

        $analytics = new Google_Service_Analytics($this->getClient());

        return $analytics;
    }

    // -----------------------------------------------------------------------------------------------------

    /**
     * Get Traffic Keywords
     *
     * @return array
     */
    public function getTrafficKeywords()
    {
        $trafficKeywords = $this->getAnalyticsEngine()->data_ga->get(
            'ga:' . $this->getAnalyticsProfileId(),
            $this->getDateFrom(),
            $this->getDateTill(),
            'ga:sessions',
            array('dimensions' => 'ga:keyword', 'sort' => '-ga:sessions')
        )->getRows();

        $trafficKeywords = ($trafficKeywords) ? $trafficKeywords : array();

        return $trafficKeywords;
    }

    // -----------------------------------------------------------------------------------------------------

    /**
     * Set Start Date
     *
     * @param \DateTime $dateTime DateTime Object
     *
     * @return $this
     */
    public function setDateFrom(\DateTime $dateTime)
    {
        // TODO: Implement setFromDate() method.
    }

    /**
     * Get Start Date
     *
     * @return \DateTime
     */
    public function getDateFrom()
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
    public function setDateTill(\DateTime $dateTime)
    {
        // TODO: Implement setTillDate() method.
    }

    /**
     * Get Last Date
     *
     * @return \DateTime
     */
    public function getDateTill()
    {
        // TODO: Implement getTillDate() method.
    }

    // -----------------------------------------------------------------------------------------------------

    /**
     * Get Analytics Profile ID for Specific Domain
     * - search google analytic account for specific name
     * - search properties on account for specific domain
     * - get track id from that profile with domain
     *
     * @throws \Exception
     * @return string
     */
    protected function getAnalyticsProfileId()
    {
        if ($this->analytic_account_profile_trackid)
            return $this->analytic_account_profile_trackid;

        /**
         * From:
         * @see https://developers.google.com/analytics/devguides/reporting/core/v3/
         * Find:
         * "There are a couple of ways to find your view (profile) ID."
         */
        $analytics = $this->getAnalyticsEngine();

        $accounts = $analytics->management_accounts->listManagementAccounts();
        $accuredAccountId = false;
        if (count($accounts->getItems()) > 0) {
            $items = $accounts->getItems();

            /** @var $item Google_Service_Analytics_Account */
            foreach ($items as $item) {
                if ($item->getName() != $this->analytics_account_name)
                    continue;

                $accuredAccountId = $item->getId();
            }

            if (!$accuredAccountId)
                throw new \Exception(sprintf('Account %s not found in analytics.', $this->analytics_account_name));

            $webproperties = $analytics->management_webproperties
                ->listManagementWebproperties($accuredAccountId);

            $accWebpropertyId = false;
            if (count($webproperties->getItems()) > 0) {
                $items = $webproperties->getItems();

                /** @var $item Google_Service_Analytics_Webproperty */
                foreach($items as $item) {
                    $itemWebUrl = parse_url($item->getWebsiteUrl());
                    $itemWebUrl = $itemWebUrl['host'];
                    if ($itemWebUrl != $this->analytics_account_profile_domain)
                        continue;

                    $accWebpropertyId = $item->getId();
                }

                if (!$accWebpropertyId)
                    throw new \Exception(
                        sprintf(
                            'No Profile found on "%s" account with registered "%s" domain.',
                            $this->analytics_account_name,
                            $this->analytics_account_profile_domain
                        )
                    );

                return $accWebpropertyId;

            } else {
                throw new \Exception(
                    sprintf('No Profile found on "%s" Analytics Account.', $this->analytics_account_name)
                );
            }
        } else {
            throw new \Exception('Analytics Account Has No Account Defined.');
        }
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
        $profileID  = $this->getAnalyticsProfileId();
        $profDomain = $this->getAnalyticsProfileDomain();

        if (!$profileID || !$profDomain)
            return false;

        if ($e->getResult() instanceof Response
            || !$e->getViewModel() instanceof ViewModel
        )
            // Nothing to do
            return false;

        /** @var $matchedRoute \Zend\Mvc\Router\Http\RouteMatch */
        $matchedRoute = $e->getRouteMatch();
        if ($matchedRoute && $matchedRoute->getMatchedRouteName() == \yimaAdminor\Module::ADMIN_DEFAULT_ROUTE_NAME)
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

              ga('create', '".$profileID."', '".$profDomain."');
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
