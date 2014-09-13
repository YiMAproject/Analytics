<?php
namespace Analytics\Service\Client\Interfaces;
use Analytics\Service\Client\AbstractClientConfig;

/**
 * Interface ClientOauthInterface
 * : Clients that usually used oauth for authorization
 *
 * @package Analytics\Service
 */
interface ClientOauthInterface extends ClientInterface
{
    /**
     * Get Client Engine Object
     *
     * @return mixed
     */
    public function getEngine();

    /**
     * Client Configuration
     *
     * @return AbstractClientConfig
     */
    public function config();

    /**
     * Get Authorization Url
     *
     * @return string
     */
    public function getAuthUrl();

    /**
     * Usually Code Returned After AuthURL Confirm
     * : used by authorize in oauth methods as access token
     *
     * @param string $accToken Access Token
     *
     * @return mixed
     */
    public function setAuthToken($accToken);

    /**
     * Get AuthToken
     *
     * @return string
     */
    public function getAuthToken();

    /**
     * Revoke Access From Client
     *
     * @return $this
     */
    public function revokeAccess();

    /**
     * Has Refresh Token
     *
     * @return boolean
     */
    public function hasRefreshToken();
}
