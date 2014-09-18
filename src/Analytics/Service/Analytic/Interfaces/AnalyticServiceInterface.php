<?php
namespace Analytics\Service\Analytic\Interfaces;

use Analytics\Service\Client\Interfaces\ClientInterface;

/**
 * Interface ServiceAnalyticsInterface
 *
 * @package Analytics\Service
 */
interface AnalyticServiceInterface
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

    /**
     * Set Start Date
     *
     * @param \DateTime $dateTime DateTime Object
     *
     * @return $this
     */
    public function setFromDate(\DateTime $dateTime);

    /**
     * Get Start Date
     *
     * @return \DateTime
     */
    public function getFromDate();

    /**
     * Set Last Date
     *
     * @param \DateTime $dateTime DateTime Object
     *
     * @return $this
     */
    public function setTillDate(\DateTime $dateTime);

    /**
     * Get Last Date
     *
     * @return \DateTime
     */
    public function getTillDate();

    // --- Features Implementation -------------------------------------------------------------------------------

    /**
     * Get Traffic Keywords
     *
     * @return array
     */
    public function getTrafficKeywords();
}
