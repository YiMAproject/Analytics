<?php
namespace Analytics\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class IndexController
 *
 * @package Analytics\Controller\Admin
 */
class IndexController extends AbstractActionController
{
    /**
     * Redirect OAuth Back
     * : it's placed out of admin so can access by all users
     *
     * - get auth back code
     * - renew refresh code
     */
    public function oauthbackcodeAction()
    {
        $request = $this->getRequest();

        $getBackCode = $request->getQuery('code');
        if ($getBackCode) {
            /** @var $client \Analytics\Service\Client\Google */
            $client = $this->serviceLocator->get('Analytics.Client');
            try {
                $result = $client->authorize($getBackCode);
                if ($result) {
                    // redirect to page
                    $url = $this->url()->fromRoute(
                        \yimaAdminor\Module::ADMIN_DEFAULT_ROUTE_NAME,
                        array('module' => 'Analytics', 'controller' => 'Index', 'action' => 'access')
                    );

                    $this->redirect()->toUrl($url);

                    return $this->getResponse();
                }
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        }

        die('Access Denied.');
    }
}
