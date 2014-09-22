<?php
namespace Analytics\Service\Analytic;

use Analytics\Service\AbstractConfig;

class GoogleAnalyticConfig extends AbstractConfig
{
    /**
     * Default and Needed Options
     * @link https://console.developers.google.com/
     *       to generate your
     *       client id, client secret, and to register your redirect uri.
     *
     * @var array
     */
    protected $options = [
        'account_id'    => null, // search for id from given account_name
            'account_name'  => 'Raya Media',
        'property_id'   => null, // search for id from given property_name
            'property_name' => 'Umetal',
    ];

    public function setAccountName($val)
    {
        $this->options['account_name'] = $val;

        return $this;
    }

    public function getAccountName()
    {
        return $this->options['account_name'];
    }

    public function setAccountId($val)
    {
        $this->options['account_id'] = $val;

        return $this;
    }

    public function getAccountId()
    {
        return $this->options['account_id'];
    }

    public function setPropertyName($val)
    {
        $this->options['property_name'] = $val;

        return $this;
    }

    public function getPropertyName()
    {
        return $this->options['property_name'];
    }

    public function setPropertyId($val)
    {
        $this->options['property_id'] = $val;

        return $this;
    }

    public function getPropertyId()
    {
        return $this->options['property_id'];
    }
}
