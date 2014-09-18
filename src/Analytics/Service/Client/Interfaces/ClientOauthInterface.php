<?php
namespace Analytics\Service\Client\Interfaces;
use Analytics\Service\Client\AbstractClientConfig;

/**
 * Interface ClientOauthInterface
 * : Clients that usually used oauth for authorization
 *
 * @package Analytics\Service
 */
interface ClientOauthInterface
{
    /**
     * Authorize Client
     * - store authorization tokens in storage like session
     * - return true on success and false on failure
     *
     * @param string $getBackCode The Code Returned from AuthURL
     *
     * @return boolean
     */
    public function authorize($getBackCode);

    /**
     * Is Client Authorized?
     *
     * @return boolean
     */
    public function isAuthorized();

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
     * @return boolean
     */
    public function revokeAccess();
}
