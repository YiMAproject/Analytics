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
        /** @var $analytics \Analytics\Service\Analytic\GoogleAnalyticService */
        $analytics = $this->getServiceLocator()
            ->get('Analytics.Service');

        return array(
            'keywords' => $analytics->getTrafficKeywords()
        );
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

        return array(
            'has_authorized' => $client->getAuthToken(),
            'url_authorize'  => $client->getAuthUrl(),  // auth url if not authorized
            'url_revoke'     => $this->url()->fromRoute(
                    \yimaAdminor\Module::ADMIN_DEFAULT_ROUTE_NAME,
                    array('module' => 'Analytics', 'controller' => 'Index', 'action' => 'revokeAccess')
            ),
        );
    }

    /**
     * Revoke Access From Authorized User
     */
    public function revokeAccessAction()
    {
        /** @var $client \Analytics\Service\Client\Google */
        $client = $this->serviceLocator->get('Analytics.Client');

        if ($client->revokeAccess()) {
            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $request = $this->getRequest();
            if ($request->getServer()->offsetExists('HTTP_REFERER'))
                $referer = $request->getServer()->offsetGet('HTTP_REFERER');
            else
                $referer = $this->url()->fromRoute(
                    \yimaAdminor\Module::ADMIN_DEFAULT_ROUTE_NAME,
                    array('module' => 'Analytics', 'controller' => 'Index', 'action' => 'access')
                );

            $this->redirect()->toUrl($referer);

            return $this->getResponse();
        }

        // Access denied
        return $this->getResponse()
            ->setStatusCode(302);
    }
}
