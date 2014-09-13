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
    public function dashboardAction()
    {
        /** @var $client \Analytics\Service\Client\Google */
        $client = $this->serviceLocator->get('Analytics.Client');

        $code = '4/T-cRDBtr9DbwrzXgwCRShd2a19tq.QkGENyBta8kWyjz_MlCJoi0sWOXdkAI';
        if ($code) {
            $client->authorize($code);

            d_e('Authorized');
        }

        if (!$client->isAuthorized())
            $this->redirect()->toUrl($client->getAuthUrl());



    }
}
