<?php
namespace Analytics\Service\Client;

use Analytics\Service\Client\Interfaces\ClientOauthInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Session\Container;

class Google implements
    ClientOauthInterface,
    ServiceManagerAwareInterface
{
    const SESSION_STORAGE_KEY = 'googleanalytic_session_key';

    /**
     * @var \Google_Client
     */
    protected $engine;

    /**
     * @var GoogleConfig
     */
    protected $config;

    /**
     * @var Container
     */
    protected $session;

    /**
     * : to store refreshToken in settings
     * @var ServiceManager
     */
    protected $sm;

    /**
     * Construct
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->config()
            ->setFromArray($options);

        $this->session = new Container(self::SESSION_STORAGE_KEY);
    }

    /**
     * Get Client Engine Object
     *
     * @return \Google_Client
     */
    public function getEngine()
    {
        if (!$this->engine) {
            $client = new \Google_Client();
            /**
             * When your application receives a refresh token, it is important to store that refresh token for future use.
             * If your application loses the refresh token, it will have to re-prompt the user for consent before obtaining
             * another refresh token. If you need to re-prompt the user for consent, include the approval_prompt parameter
             * in the authorization code request, and set the value to force
             */
            $client->setAccessType('offline'); // In some cases, your application may need to access a Google API when the user is not present

            $this->engine = $client;
        }

        return $this->engine;
    }

    /**
     * Client Configuration
     *
     * @return GoogleConfig
     */
    public function config()
    {
        if (!$this->config)
            $this->config = new GoogleConfig($this->getEngine());

        return $this->config;
    }

    /**
     * Authorize Client
     * - store authorization tokens in storage like session
     * - return true on success and false on failure
     *
     * @param string $getBackCode The Code Returned from AuthURL
     *
     * @throws \Exception
     * @return boolean
     */
    public function authorize($getBackCode)
    {
        $auth = $this->engine->authenticate($getBackCode);
        $auth = json_decode($auth, true);

        /**
         * After your application receives the refresh token, it may obtain new access tokens at any time.
         * @see https://developers.google.com/accounts/docs/OAuth2WebServer#refresh
         */
        if (isset($auth['refresh_token']))
            $this->setRefreshToken($auth['refresh_token']);

        $this->setAuthToken($this->engine->getAccessToken());

        return true;
    }

    /**
     * Is Client Authorized?
     * : with looking over strategy over authorize
     *
     * @return boolean
     */
    public function isAuthorized()
    {
        return ($this->getAuthToken() && !$this->engine->isAccessTokenExpired());
    }

    /**
     * Get Authorization Url
     *
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->engine->createAuthUrl();
    }

    /**
     * Usually Code Returned After AuthURL Confirm
     * : used by authorize in oauth methods as access token
     *
     * @param string $accToken Access Token
     *
     * @return mixed
     */
    public function setAuthToken($accToken)
    {
        $this->session->token = $this->engine->getAccessToken();

        $this->engine->setAccessToken($accToken);

        return $this;
    }

    /**
     * Get AuthToken
     *
     * @return string
     */
    public function getAuthToken()
    {
        $accessToken = $this->session->token;
        if ($accessToken)
            $this->setAuthToken($accessToken);

        if (!$this->engine->getAccessToken() || $this->engine->isAccessTokenExpired()) {
            // Refresh the token if expired
            try {
                $this->engine->refreshToken($this->getRefreshToken());
                $this->session->token = $this->engine->getAccessToken();
            } catch (\Exception $e)
            {
                // Invalid Refresh Token, And Current Token is Invalid
                return false;
            }

        }

        return $this->engine->getAccessToken();
    }

    /**
     * Revoke Access From Client
     *
     * @return $this
     */
    public function revokeAccess()
    {
        $refreshToken = null;
        if ($this->getRefreshToken())
            $refreshToken = $this->getRefreshToken();

        $this->engine->revokeToken($refreshToken);

        // Remove Refresh Token From Storage ................
        $this->setRefreshToken(null);
    }

    /**
     * Get Refresh Token
     *
     * @return string
     */
    protected function getRefreshToken()
    {
        $settStorage = $this->getSettingStorage()
            ->get('analytics');

        return $settStorage->get('refresh_token')->value;
    }

    protected function setRefreshToken($token)
    {
        $settStorage   = $this->getSettingStorage();
        $storageEntity = $settStorage->get('analytics');
        $refreshToken  = $storageEntity->get('refresh_token');
        $refreshToken->value = $token;
        $storageEntity->set('refresh_token', $refreshToken);

        $settStorage->save($storageEntity);
    }

    /**
     * Set service manager
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
    }

    /**
     * Get Settings Storage
     * : to retrieve and write some settings
     *
     * @return \yimaSettings\Service\Settings
     */
    protected function getSettingStorage()
    {
        /** @var $settings \yimaSettings\Service\Settings */
        $settings = $this->sm->get('yimaSettings');

        return $settings;
    }
}
