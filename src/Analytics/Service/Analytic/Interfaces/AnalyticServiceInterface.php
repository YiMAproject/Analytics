<?php
namespace Analytics\Service\Analytic\Interfaces;

interface AnalyticServiceInterface extends
    ClientAwareAnalyticInterface
{
    /**
     * Set Start Date
     *
     * @param \DateTime $dateTime DateTime Object
     *
     * @return $this
     */
    public function setDateFrom(\DateTime $dateTime);

    /**
     * Get Start Date
     *
     * @return \DateTime
     */
    public function getDateFrom();

    /**
     * Set Last Date
     *
     * @param \DateTime $dateTime DateTime Object
     *
     * @return $this
     */
    public function setDateTill(\DateTime $dateTime);

    /**
     * Get Last Date
     *
     * @return \DateTime
     */
    public function getDateTill();

    // --- Features Implementation -------------------------------------------------------------------------------

    /**
     * Get Traffic Keywords
     *
     * @return array
     */
    public function getTrafficKeywords();
}
