<?php
namespace Analytics\Service\Client;

use Analytics\Service\Client\Interfaces\ClientInterface;

abstract class AbstractClientConfig
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * Default Options
     *
     * @var array
     */
    protected $options = [
        // 'sample_option' => '', cast options to SampleOption Setter Method
    ];

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

    /**
     * Set one or more configuration properties
     *
     * @param array $options
     *
     * @return $this
     */
    public function setFromArray(array $options)
    {
        $options = array_merge($this->options, $options);

        foreach ($options as $key => $value) {
            $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (!method_exists($this, $setter))
                continue;

            $this->{$setter}($value);
        }

        return $this;
    }

    /**
     * Cast to array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->options;
    }
}
