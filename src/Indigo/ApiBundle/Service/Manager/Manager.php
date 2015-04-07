<?php
namespace Indigo\ApiBundle\Service\Manager;

use Indigo\ApiBundle\Repository\TableEventList;
use GuzzleHttp;
use Indigo\ApiBundle\Event\ApiEvent;
use Indigo\ApiBundle\Event\ApiEvents;
use Indigo\ApiBundle\Factory\EventFactory;
use MyProject\Proxies\__CG__\stdClass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class Manager
{
    const LAST_RECORD_TS = 'api.last_record_ts';
    const LAST_RECORD_ID = 'api.last_record_id';
    const LAST_RECORDS_COUNT = 'api.last_record_count';


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
                    $data = json_decode($response->getBody());
                    //var_dump($data);

                    if ($data->status != 'ok') {
                        throw new \Exception('invalid api response');
                    }
                    //TODO: susirikiuoti atbuline tvarka
                    //TODO: tableshake'o eventa issisaugoti

                    $eventList = new TableEventList();
                    foreach ($data->records as $d) {

                        if (strlen($d->data) > 2) {
                            $d->data = json_decode($d->data);
                        } else {
                            $d->data = new \stdClass();
                        }
                        $event = EventFactory::factory($d);

                        $eventList->append($event);
                    }
                    //TODO: or not TODO  - sortint ? :)
                    $eventList->uasort(array($this, 'orderByTs'));

                    $successEvent = new ApiEvent();
                    $successEvent->setData($eventList);

                    $this->ed->dispatch(ApiEvents::API_SUCCESS_EVENT, $successEvent);

                   return $eventList;
                }
            }
        } catch (\Exception $e) {
            print $e->getMessage();
            /*
             * $failureEvent = new ApiEvent();
                $this->ed->dispatch(ApiEvents::API_FAILED_EVENT, $failureEvent);
            */

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

    /**
     * @param \stdClass
     * @return int
     */
    public function orderByTs ($a, $b) {
        if  ($a->getTimeWithUsec() ==  $b->getTimeWithUsec()) {
            return 0;
        }
        return ($a->getTimeWithUsec() > $b->getTimeWithUsec()) ? 1 : -1;
    }
}