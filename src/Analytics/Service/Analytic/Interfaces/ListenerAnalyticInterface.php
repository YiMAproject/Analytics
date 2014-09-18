<?php
namespace Analytics\Service\Analytic\Interfaces;

use Zend\EventManager\SharedListenerAggregateInterface;

/**
 * Interface ListenerAnalyticInterface
 *
 * : Services that extend this interface on module init
 *   attached as an aggregate to event manager
 *
 * @package Analytics\Service\Analytic\Interfaces
 */
interface ListenerAnalyticInterface extends
    AnalyticServiceInterface,
    SharedListenerAggregateInterface
{

}
