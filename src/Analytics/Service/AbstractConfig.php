<?php
namespace Analytics\Service;

class AbstractConfig
{
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
     * @param array $props
     */
    public function __construct($props = array())
    {
        $this->setFromArray($props);
    }

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
