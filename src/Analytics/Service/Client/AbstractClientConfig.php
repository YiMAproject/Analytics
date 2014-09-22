<?php
namespace Analytics\Service\Client;

use Analytics\Service\AbstractConfig;
use Analytics\Service\Client\Interfaces\ClientInterface;

abstract class AbstractClientConfig extends AbstractConfig
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * Construct
     *
     * @param mixed $client
     */
    final public function __construct($client)
    {
        $this->validateClient($client);

        $this->client = $client;
    }

    /**
     * Validate Client Passed On Constructor
     *
     * @param mixed $client Client Object
     * @return mixed
     */
    abstract public function validateClient($client);
}
