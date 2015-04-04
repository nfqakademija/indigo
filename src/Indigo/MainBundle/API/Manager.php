<?php
namespace Indigo\MainBundle\API;

use Indigo\API\Repository\EventList;
use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use Indigo\MainBundle\Event\ApiEvent;
use Indigo\MainBundle\Event\ApiEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Manager
{
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
     * @var EventDispatcher
     */
    private $ed;

    /**
     * @param EventDispatcherInterface $ed
     */
    function __construct(EventDispatcherInterface $ed)
    {
        $this->ed = $ed;
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
    public function getEvents(array $query)
    {
        try {
            $query = [
                'query' => $query,
                'auth' => $this->options['auth'],
            ];



            $response = $this->getClient()->get($this->url, $query);

            if ($response->getStatusCode() == 200) {
                if (stripos($response->getHeader('content-type'), 'json') !== false) {
                    $data = json_decode($response->getBody(), true);

                    if ($data['status'] != 'ok') {
                        throw new \Exception('invalid api response');
                    }

                    $successEvent = new ApiEvent();
                    $successEvent->setData($data['records']);

                    $this->ed->dispatch(ApiEvents::API_SUCCESS_EVENT, $successEvent);




                    $eventList = new EventList();
                    foreach ($data['records'] as $d) {
                        $event = EventFactory::factory($d);
                        $eventList->append($event);
                    }

                    return $eventList;
                }
            }
        } catch (\Exception $e) {
            $failureEvent = new ApiEvent();
            $this->ed->dispatch(ApiEvents::API_FAILED_EVENT, $failureEvent);

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
        $this->url = $options['table_api_url'];

        unset($options['table_api_url']);
        $this->options = $options;
    }
}