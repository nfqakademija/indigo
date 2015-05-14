<?php
namespace Indigo\ApiBundle\Service\Manager;

use Indigo\TableBundle\Model\TableShakeModel;
use Indigo\TableBundle\Repository\TableEventList;
use GuzzleHttp;
use Indigo\ApiBundle\Event\ApiEvent;
use Indigo\ApiBundle\Event\ApiEvents;
use Indigo\ApiBundle\Factory\EventFactory;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApiManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
    private $tables;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * @var EventDispatcherInterface
     */
    private $ed;

    /**
     * @param EventDispatcherInterface $ed
     */
    public function __construct(EventDispatcherInterface $ed)
    {
        $this->ed = $ed;
    }

    /**
     * @param $tableKey
     * @param array $query
     * @param bool $provideDemoEvents
     * @return \ArrayIterator|bool|TableEventList
     * @throws \Exception
     */
    public function getEvents($tableKey, array $query, $provideDemoEvents = false)
    {
       
        if ($provideDemoEvents === true) {

            if ($eventsJSON = $this->getDemoData()) {

                $eventList = $this->parseResponseData($eventsJSON);
                $eventList->setTableId(-1); // virtual table

                return $eventList;
            }
        }

        try {

            $table = $this->getTable($tableKey);

            $query = [
                'query' => $query,
                'auth' => $table['auth'],
                'timeout' => 5
            ];

            $response = $this->getClient()->get($table['table_api_url'], $query);
            if ($response->getStatusCode() == 200) {

                if (stripos($response->getHeader('content-type'), 'json') !== false) {

                    $data = json_decode($response->getBody());
                    if ($data->status == 'ok') {

                        $eventList = $this->parseResponseData($data);
                        $eventList->setTableId($table['table_id']);
                        //TODO: or not TODO  - sortint ? :)
                        //$eventList->uasort(array($this, 'orderByTs'));

                        $successEvent = new ApiEvent();
                        $successEvent->setData($eventList);
                        $this->ed->dispatch(ApiEvents::API_SUCCESS_EVENT, $successEvent);

                        return $eventList;
                    }
                }
            }
            throw new \Exception('API seems went down...');

        } catch (GuzzleHttp\Exception\ConnectException $e) {
            if ($provideDemoEvents == true) {
                if ($eventsJSON = $this->getDemoData()) {

                    $eventList = $this->parseResponseData($eventsJSON);
                    $eventList->setTableId(-1); // virtual table

                    return $eventList;
                }

                return new \ArrayIterator();
            }
        }

        return false;
    }

    /**
     * @param \stdClass $data
     * @return TableEventList
     */
    public function parseResponseData(\stdClass $data)
    {
        $lastTableShakeIndex = -1;
        $eventList = new TableEventList();
        $i = 0;

        foreach ($data->records as $d) {

            if (strlen($d->data) > 2) {

                $d->data = json_decode($d->data);
            } else {

                $d->data = new \stdClass();
            }

            $event = EventFactory::factory($d);
            if ($event) {

                $eventList->append($event);
                $this->logger && $this->logger->debug('added events', ['event' => $event]);
                if ($event instanceof TableShakeModel) {

                    ($lastTableShakeIndex > -1)  && $eventList->offsetUnset($lastTableShakeIndex);
                    $lastTableShakeIndex = $i;
                }
            } else {

                $this->logger && $this->logger->warning('unknown event', ['data' => $d]);
            }

            $i++;
        }

        return $eventList;
    }

    /**
     * @return GuzzleHttp\Client
     */
    protected function getClient()
    {
        if (!$this->client) {

            $this->client = new GuzzleHttp\Client();
        }

        return $this->client;
    }

    /**
     * @param $tableId
     * @return mixed
     */
    private function getTable($tableId)
    {
        if (isset($this->tables[$tableId])) {

            return  $this->tables[$tableId];
        }

        throw new \InvalidArgumentException('missing table');
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
        $this->tables = $options['tables'];
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



    /**
     * @return string
     */
    private function getDemoData() {
      return  json_decode('{"status":"ok","records":[
      {"id":"96115","timeSec":"1425520557","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:1,\u0022player\u0022:1,\u0022card_id\u0022:8462951}"},
      {"id":"96115","timeSec":"1425520558","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:1,\u0022player\u0022:1,\u0022card_id\u0022:8462951}"},

      {"id":"96115","timeSec":"1425520558","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:0,\u0022player\u0022:1,\u0022card_id\u0022:8461951}"},
      {"id":"96115","timeSec":"1425520560","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:1,\u0022player\u0022:1,\u0022card_id\u0022:8462952}"},
      {"id":"96215","timeSec":"1425522560","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:0,\u0022player\u0022:0,\u0022card_id\u0022:8463953}"},
      {"id":"96315","timeSec":"1425523560","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:1,\u0022player\u0022:0,\u0022card_id\u0022:8464954}"},
      {"id":"96511","timeSec":"1425543550","usec":"93454","type":"TableShake","data":"[]"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96315","timeSec":"1425543571","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:1,\u0022player\u0022:0,\u0022card_id\u0022:8464954}"},
      {"id":"96315","timeSec":"1425543579","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:1,\u0022player\u0022:0,\u0022card_id\u0022:8464954}"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96315","timeSec":"1425543587","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:1,\u0022player\u0022:0,\u0022card_id\u0022:8464954}"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96513","timeSec":"1425543551","usec":"527308","type":"AutoGoal","data":"{\u0022team\u0022:0}"},
      {"id":"96514","timeSec":"1425543560","usec":"484757","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96544","timeSec":"1425543914","usec":"309757","type":"AutoGoal","data":"{\u0022team\u0022:1}"}]}');
    }
}