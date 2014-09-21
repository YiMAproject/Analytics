<?php
namespace Analytics\Service\Analytic\Interfaces;

use Analytics\Service\Client\Interfaces\ClientInterface;

interface ClientAwareAnalyticInterface
{
    /**
     * Set Service Client Object
     *
     * @param ClientInterface $client
     *
     * @return $this
     */
    public function setClient(ClientInterface $client);

    /**
     * Get Client Object
     *
     * @return ClientInterface
     */
    public function getClient();
}
