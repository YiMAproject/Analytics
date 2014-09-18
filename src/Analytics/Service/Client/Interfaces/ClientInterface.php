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
