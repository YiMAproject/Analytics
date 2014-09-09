<?php
namespace Analytics\Service\Client\Interfaces;

/**
 * Interface ClientOauthInterface
 * : Clients that usually used oauth for authorization
 *
 * @package Analytics\Service
 */
interface OauthInterface extends ClientInterface
{
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
