<?php
namespace Analytics\Service\Client\Interfaces;

/**
 * Interface ClientInterface
 *
 * @package Analytics\Service
 */
interface ClientInterface
{
    /**
     * Authorize Client
     * - store authorization tokens in storage like session
     * - return true on success and false on failure
     *
     * @return boolean
     */
    public function authorize();

    /**
     * Is Client Authorized?
     * : with looking over strategy over authorize
     *
     * @return boolean
     */
    public function isAuthorized();

    /**
     * Get Client Engine Object
     *
     * @return mixed
     */
    public function getEngine();
}
