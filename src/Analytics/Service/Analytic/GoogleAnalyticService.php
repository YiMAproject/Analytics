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
     * @var GoogleAnalyticConfig
     */
    protected $config;


    protected $internal_property_view_id;

    /**
     * @var \DateTime
     */
    protected $date_from;

    /**
     * @var \DateTime
     */
    protected $date_till;

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
     * Get Config
     *
     * @return GoogleAnalyticConfig
     */
    public function config()
    {
        if (!$this->config)
            $this->config = new GoogleAnalyticConfig();

        return $this->config;
    }

    /**
     * The client object is used to create an authorized analytics service object.
     *
     * @return \Google_Service_Analytics
     * @throws \Exception
     */
    protected function getAnalyticsEngine()
    {
        if (!$this->getClient())
            throw new \Exception('No Analytics Client Provided Yet.');

        if (!$this->getClient()->isAuthorized())
            throw new \Exception('Analytics Client Not Authorized.');

        $googleClient = $this->getClient()
            ->getEngine();

        $analytics = new \Google_Service_Analytics($googleClient);

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
            $this->getDateFrom()->format('Y-m-d'),
            $this->getDateTill()->format('Y-m-d'),
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
        $this->date_from = $dateTime;

        return $this;
    }

    /**
     * Get Start Date
     *
     * @return \DateTime
     */
    public function getDateFrom()
    {
        if (!$this->date_from) {
            // show from 12 month past
            $date = new \DateTime();
            $date->modify('-12 month');

            $this->date_from = $date;
        }

        return $this->date_from;
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
        $this->date_till = $dateTime;

        return $this;
    }

    /**
     * Get Last Date
     *
     * @return \DateTime
     */
    public function getDateTill()
    {
        if (!$this->date_till)
            $this->date_till = new \DateTime();

        return $this->date_till;
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
    protected function getAnalyticsPropertyId()
    {
        if ($this->config()->getPropertyId())
            return $this->config()->getPropertyId();
        elseif ($this->config()->getPropertyName() === null)
            throw new \Exception('Once "Property ID" or "Property Name" Needed as Config.');

        /**
         * From:
         * @see https://developers.google.com/analytics/devguides/reporting/core/v3/
         * Find:
         * "There are a couple of ways to find your view (profile) ID."
         */
        $property = $this->getAnalyticsWebProperty(array('name' => $this->config()->getPropertyName()));
        if ($property)
            $this->config()->setPropertyId($property['id']);

        return $this->config()->getPropertyId();
    }

    /**
     * Get Analytic Profile(View) ID from current Property
     *
     * @return string
     */
    protected function getAnalyticsProfileId()
    {
        if ($this->internal_property_view_id)
            return $this->internal_property_view_id;

        //                                         --- return first accrued profile with this common key ----
        $profile = $this->getAnalyticsWebProfile(array('webPropertyId' => $this->getAnalyticsPropertyId()));
        if ($profile)
            $this->internal_property_view_id = $profile['id'];

        return $this->internal_property_view_id;
    }

    /**
     * Get AnalyticAccountID
     *
     * @throws \Exception
     * @return false|string
     */
    protected function getAnalyticsAccountID()
    {
        if ($this->config()->getAccountId())
            return $this->config()->getAccountId();
        elseif ($this->config()->getAccountName() === null)
            throw new \Exception('Once "Account ID" or "Account Name" Needed as Config.');


        $analytics = $this->getAnalyticsEngine();

        $accounts = $analytics->management_accounts->listManagementAccounts();
        $accruedAccountId = false;
        if (count($accounts->getItems()) > 0) {
            $items = $accounts->getItems();

            /** @var $item \Google_Service_Analytics_Account */
            foreach ($items as $item) {
                //                      -- Current Account ----------
                if ($item->getName() != $this->config()->getAccountName())
                    continue;

                $accruedAccountId = $item->getId();
            }
        } else {
            throw new \Exception('Analytics Account Has No Account Defined.');
        }

        $this->config()->setAccountId($accruedAccountId);

        return $this->config()->getAccountId();
    }

    /**
     * Search On Current Account For A Matched Property with props
     * and return first accrued match
     *
     * @param array $props Match Against Property Item
     *
     * @throws \Exception
     * @return array
     */
    protected function getAnalyticsWebProperty(array $props)
    {
        $accruedAccountId = $this->getAnalyticsAccountID();
        if (!$accruedAccountId)
            throw new \Exception(sprintf('Account %s not found in analytics.', $this->config()->getAccountName()));

        $analytics = $this->getAnalyticsEngine();
        $webproperties = $analytics->management_webproperties
            ->listManagementWebproperties($accruedAccountId);

        if (count($webproperties->getItems()) > 0) {
            $items = $webproperties->getItems();

            /** @var $item \Google_Service_Analytics_Webproperty */
            foreach($items as $item) {
                $itemArray = [
                    'id'                    => $item->id,
                    'accountId'             => $item->accountId,
                    'industryVertical'      => $item->industryVertical,
                    'internalWebPropertyId' => $item->internalWebPropertyId,
                    'level'                 => $item->level,
                    'name'                  => $item->name,
                    'websiteUrl'            => $item->websiteUrl,
                ];

                if (count(array_intersect($itemArray, $props)) == count($props))
                    return $itemArray;
            }

        } else {
            throw new \Exception(
                sprintf('No Property found on "%s" Analytics Account.', $this->config()->getAccountName())
            );
        }

        return array();
    }

    /**
     * Search On Current Account For A Matched Profile(View) with props
     * and return first accrued match
     * ! use current account id
     * ! use current property id
     *
     * @param array $props Match Against Property Item
     *
     * @throws \Exception
     * @return array
     */
    protected function getAnalyticsWebProfile(array $props)
    {
        $accruedAccountId = $this->getAnalyticsAccountID();
        if (!$accruedAccountId)
            throw new \Exception(sprintf('Account %s not found in analytics.', $this->config()->getAccountName()));
        $webPropertyId    = $this->getAnalyticsPropertyId();
        if (!$accruedAccountId)
            throw new \Exception(
                sprintf('No Property "%s" found on "%s" Analytics Account.',
                    $this->config()->getPropertyName(),
                    $this->config()->getAccountName()
                )
            );

        $analytics = $this->getAnalyticsEngine();
        $profiles = $analytics->management_profiles
            ->listManagementProfiles($accruedAccountId, $webPropertyId);

        if (count($profiles->getItems()) > 0) {
            $items = $profiles->getItems();

            /** @var $item \Google_Service_Analytics_Profile */
            foreach($items as $item) {
                $itemArray = [
                    'id'                    => $item->id,
                    'name'                  => $item->name,
                    'webPropertyId'         => $item->webPropertyId,
                    'currency'              => $item->currency,
                    'eCommerceTracking'     => $item->eCommerceTracking,
                    'internalWebPropertyId' => $item->internalWebPropertyId,
                    'timezone'              => $item->timezone,
                    'type'                  => $item->type,
                    'websiteUrl'            => $item->websiteUrl,
                ];

                if (count(array_intersect($itemArray, $props)) == count($props))
                    return $itemArray;
            }

        } else {
            throw new \Exception(
                sprintf('No Profile found on "%s" Analytics Account.', $this->config()->getAccountName())
            );
        }

        return array();
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
        try {
            $profileID  = $this->getAnalyticsPropertyId();
        } catch (\Exception $e) {
            $profileID  = false;
        }

        if (!$profileID)
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

              ga('create', '".$profileID."', 'auto');
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
