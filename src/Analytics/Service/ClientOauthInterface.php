<?php
namespace Analytics\Service;

/**
 * Interface ClientOauthInterface
 *
 * @package Analytics\Service
 */
interface ClientOauthInterface extends ClientInterface
{
    /**
     * Get Authorization Url
     *
     * @return string
     */
    public function getAuthUrl();

    /**
     * Usually Code Returned After AuthURL Confirm
     * : used by authorize in oauth methods
     *
     * @return mixed
     */
    public function setAuthToken();

    /**
     * Revoke Access From Client
     *
     * @return $this
     */
    public function revokeAccess();
}