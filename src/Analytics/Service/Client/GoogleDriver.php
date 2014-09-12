<?php
namespace Analytics\Service\Client;

use Analytics\Service\Client\Interfaces\OauthClientInterface;

class GoogleDriver implements OauthClientInterface
{
    const SESSION_STORAGE_KEY = 'googleanalytic_session_key';

    /**
     * @var \Google_Client
     */
    protected $engine;

    /**
     * Default and Needed Options
     *
     * @var array
     */
    protected $options = array(
        'application'   => null,
        'client_id'     => null,
        'redirect_uri'  => null,
        'developer_key' => null,
        'developer_key' => null,
        'scopes'        => array('https://www.googleapis.com/auth/analytics.readonly'),
    );

    /**
     * Construct
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $options = array_merge($this->options, $options);
        $this->setOptions($options);

        $client = $this->getEngine();
        $client->setApplicationName($options['application']);

        // Visit https://console.developers.google.com/ to generate your
        // client id, client secret, and to register your redirect uri.
        $client->setClientId($options['client_id']);
        $client->setClientSecret($options['client_secret']);
        $client->setRedirectUri($options['redirect_uri']);
        $client->setDeveloperKey($options['developer_key']);
        $client->setScopes($options['scopes']);

        $this->engine = $client;
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
     * Authorize Client
     * - store authorization tokens in storage like session
     * - return true on success and false on failure
     *
     * @throws \Exception
     * @return boolean
     */
    public function authorize()
    {
        $getBackCode = $this->getRequest()->getQuery('code');
        if ($getBackCode)
        {
            $auth = $client->authenticate($getBackCode);
            $auth = json_decode($auth, true);

            if (!isset($auth['refresh_token'])) {
                throw new \Exception('Not Refresh Token Found.');
            }

            /**
             * After your application receives the refresh token, it may obtain new access tokens at any time.
             * @see https://developers.google.com/accounts/docs/OAuth2WebServer#refresh
             */
            Client::setRefreshToken($auth['refresh_token']);

            $this->session->user['analytics_token'] = $client->getAccessToken();

            // the code url parameter is stripped from the URL to keep things looking neat
            $this->_redirect($this->getCurrentURL());
        }
        /* _________------------------================ ``````````````````````````````` ================------------------_________ */

        $refreshToken = Client::getRefreshToken();
        if ($refreshToken) {
            $this->view->refreshToken = $refreshToken;
        }
    }

    /**
     * Is Client Authorized?
     * : with looking over strategy over authorize
     *
     * @return boolean
     */
    public function isAuthorized()
    {
        return ($this->engine->getAccessToken() && !$this->engine->isAccessTokenExpired());
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
        $accessToken = $this->session->google['analytic_token'];
        if ($accessToken)
            $this->setAuthToken($accessToken);

        if (!$this->engine->getAccessToken() || $this->engine->isAccessTokenExpired()) {
            $this->engine->refreshToken($this->getRefreshToken());

            $this->session->google['analytic_token'] = $this->engine->getAccessToken();
        }
    }

    /**
     * Revoke Access From Client
     *
     * @return $this
     */
    public function revokeAccess()
    {
        $refreshToken = null;
        if ($this->hasRefreshToken())
            $refreshToken = $this->getRefreshToken();

        $this->engine->revokeToken($refreshToken);

        /* @TODO Settings: remove refresh token */
        // ...

    }

    /**
     * Has Refresh Token
     *
     * @return boolean
     */
    public function hasRefreshToken()
    {
        // TODO: Implement hasRefreshToken() method.
    }

    /**
     * Get Refresh Token
     *
     * @return string
     */
    public function getRefreshToken()
    {

    }
}
