<?php

namespace Analytics\Service\Analytic;

use Analytics\Service\Analytic\Interfaces\AnalyticServiceInterface;
use Analytics\Service\Client\Interfaces\ClientInterface;

class GoogleAnalyticService implements AnalyticServiceInterface
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * Set Service Client Object
     *
     * @param ClientInterface $client
     *
     * @return $this
     */
    public function setClient(ClientInterface $client)
    {
        $engine = $client->getEngine();
        if (! $engine instanceof \Google_Client)
            throw new \Exception(
                sprintf(
                    'The Client Object Must Instanceof Google_Client. %s given.',
                    get_class($client)
                )
            );

        $this->client = $client;
    }

    /**
     * Get Client Object
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    // -----------------------------------------------------------------------------------------------------

    /**
     * Get Traffic Keywords
     *
     * @return array
     */
    public function getTrafficKeywords()
    {
        // TODO: Implement getTrafficKeywords() method.
    }

    // -----------------------------------------------------------------------------------------------------

    /**
     * Set Start Date
     *
     * @param \DateTime $dateTime DateTime Object
     *
     * @return $this
     */
    public function setFromDate(\DateTime $dateTime)
    {
        // TODO: Implement setFromDate() method.
    }

    /**
     * Get Start Date
     *
     * @return \DateTime
     */
    public function getFromDate()
    {
        // TODO: Implement getFromDate() method.
    }

    /**
     * Set Last Date
     *
     * @param \DateTime $dateTime DateTime Object
     *
     * @return $this
     */
    public function setTillDate(\DateTime $dateTime)
    {
        // TODO: Implement setTillDate() method.
    }

    /**
     * Get Last Date
     *
     * @return \DateTime
     */
    public function getTillDate()
    {
        // TODO: Implement getTillDate() method.
    }
}
