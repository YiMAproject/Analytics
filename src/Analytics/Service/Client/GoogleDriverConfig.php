<?php
namespace Analytics\Service\Client;

class GoogleDriverConfig extends AbstractDriverConfig
{
    /**
     * @var \Google_Client
     */
    protected $client;

    /**
     * Default and Needed Options
     * @link https://console.developers.google.com/
     *       to generate your
     *       client id, client secret, and to register your redirect uri.
     *
     * @var array
     */
    protected $options = [
        'client_id'        => null,
        'client_secret'    => null,
        'redirect_uri'     => null,
        'developer_key'    => null,
        'scopes'           => array('https://www.googleapis.com/auth/analytics.readonly'),
        'application_name' => null,
    ];

    public function setScopes(array $scopes)
    {
        $this->options['scopes'] = $scopes;

        $this->client->setScopes($scopes);

        return $this;
    }

    public function setDeveloperKey($key)
    {
        $this->options['developer_key'] = $key;

        $this->client->setDeveloperKey($key);

        return $this;
    }

    public function setRedirectUri($uri)
    {
        $this->options['redirect_uri'] = $uri;

        $this->client->setRedirectUri($uri);

        return $this;
    }

    public function setClientSecret($cs)
    {
        $this->options['client_secret'] = $cs;

        $this->client->setClientSecret($cs);

        return $this;
    }

    public function setClientId($cid)
    {
        $this->options['client_id'] = $cid;

        $this->client->setClientId($cid);

        return $this;
    }

    public function setApplicationName($name)
    {
        $this->options['application_name'] = $name;

        $this->client->setApplicationName($name);

        return $this;
    }

    /**
     * Validate Client Passed On Constructor
     *
     * @param mixed $client
     * @throws \Exception
     * @return mixed
     */
    public function validateClient($client)
    {
        if (!$client instanceof \Google_Client)
            throw new \Exception('Invalid Client Object Provided.');
    }
}
