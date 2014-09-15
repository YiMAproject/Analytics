<?php
namespace Analytics\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class IndexController
 *
 * @package Analytics\Controller\Admin
 */
class IndexController extends AbstractActionController
{
    /**
     * Display Analytics Data
     */
    public function dashboardAction()
    {

    }

    /**
     * Grant Access To Google Analytics Data
     * - get access token by user approval to oauth
     * - get refresh token and store it
     *   also can set handy from settings
     * - tell user about access status
     */
    public function accessAction()
    {
        /** @var $client \Analytics\Service\Client\Google */
        $client = $this->serviceLocator->get('Analytics.Client');

        /*if (!$client->isAuthorized())
            $this->redirect()->toUrl($client->getAuthUrl());*/

    }
}
