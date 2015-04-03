<?php
namespace  Indigo\API\Manager;

use Indigo\API\Repository\EventList;
use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;

class Manager {
    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     *
     */
    function __construct($url, $options)
    {
        $this->url = $url;
        $this->options = $options;
    }

    /**
     * @return GuzzleHttp\Client;
     */
    protected function getClient()
    {
        if (!$this->client) {
            $this->client = new GuzzleHttp\Client();
        }
        return $this->client;
    }

    /**
     * @return EventList|Event[]
     */
    public function getEvents ()
    {
        try {
            $response = $this->getClient()->get($this->url, $this->options);
        } catch (ClientException $e) {

        }
        if ($response->getStatusCode() == 200) {
            if (stripos($response->getHeader('content-type'), 'json') !== false) {
                $data = json_decode($response->getBody(), true);
                if ($data['status'] == 'ok') {
                    return new EventList( $data['records']);
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }



}