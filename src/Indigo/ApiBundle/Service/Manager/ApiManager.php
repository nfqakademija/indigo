<?php
namespace Indigo\ApiBundle\Service\Manager;

use Indigo\ApiBundle\Model\TableShakeModel;
use Indigo\ApiBundle\Repository\TableEventList;
use GuzzleHttp;
use Indigo\ApiBundle\Event\ApiEvent;
use Indigo\ApiBundle\Event\ApiEvents;
use Indigo\ApiBundle\Factory\EventFactory;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class ApiManager
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
     * @param $tableKey
     * @param array $query
     * @param bool $tryTestOnFailure
     * @return \ArrayIterator|bool|TableEventList
     * @throws \Exception
     */
    public function getEvents($tableKey, array $query, $tryTestOnFailure = false)
    {

        if ($eventsJSON = $this->getDemoData()) {

            $eventList = $this->parseResponseData($eventsJSON);
            $eventList->setTableId(-1); // virtual table

            return $eventList;
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
            if ($tryTestOnFailure) {
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
     * @param \stdClass $data
     * @return TableEventList
     */
    private function parseResponseData(\stdClass $data)
    {
        $TableShakeModel = null;
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
            $eventList->append($event);

            if ($event instanceof TableShakeModel) {

                if ($lastTableShakeIndex > -1) {

                    $eventList->offsetUnset($lastTableShakeIndex);
                }
                $lastTableShakeIndex = $i;
            }
            $i++;
        }

        return $eventList;
    }

    /**
     * @return string
     */
    private function getDemoData() {
      return  json_decode('{"status":"ok","records":[
      {"id":"96015","timeSec":"1425520560","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:0,\u0022player\u0022:1,\u0022card_id\u0022:8461951}"},
      {"id":"96115","timeSec":"1425520560","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:1,\u0022player\u0022:1,\u0022card_id\u0022:8462951}"},
      {"id":"96215","timeSec":"1425522560","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:0,\u0022player\u0022:0,\u0022card_id\u0022:8463951}"},
      {"id":"96315","timeSec":"1425523560","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:1,\u0022player\u0022:0,\u0022card_id\u0022:8464951}"},
      {"id":"96511","timeSec":"1425543550","usec":"93454","type":"TableShake","data":"[]"},
      {"id":"96512","timeSec":"1425543551","usec":"169652","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96513","timeSec":"1425543551","usec":"527308","type":"AutoGoal","data":"{\u0022team\u0022:0}"},
      {"id":"96514","timeSec":"1425543560","usec":"484757","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96515","timeSec":"1425543560","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:0,\u0022player\u0022:1,\u0022card_id\u0022:8469951}"},
      {"id":"96515","timeSec":"1425543561","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:0,\u0022player\u0022:1,\u0022card_id\u0022:8469951}"},
      {"id":"96516","timeSec":"1425543568","usec":"400106","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96517","timeSec":"1425543568","usec":"401041","type":"CardSwipe","data":"{\u0022team\u0022:0,\u0022player\u0022:1,\u0022card_id\u0022:8469934}"},
      {"id":"96518","timeSec":"1425543574","usec":"856993","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96519","timeSec":"1425543574","usec":"857931","type":"CardSwipe","data":"{\u0022team\u0022:1,\u0022player\u0022:0,\u0022card_id\u0022:8469934}"},
      {"id":"96520","timeSec":"1425543581","usec":"794230","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96521","timeSec":"1425543581","usec":"795173","type":"CardSwipe","data":"{\u0022team\u0022:0,\u0022player\u0022:0,\u0022card_id\u0022:8469951}"},
      {"id":"96523","timeSec":"1425543570","usec":"134255","type":"TableShake","data":"[]"},
      {"id":"96525","timeSec":"1425543570","usec":"138302","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96527","timeSec":"1425543572","usec":"7159","type":"TableShake","data":"[]"},
      {"id":"96529","timeSec":"1425543574","usec":"863239","type":"TableShake","data":"[]"},
      {"id":"96531","timeSec":"1425543574","usec":"874316","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96533","timeSec":"1425543576","usec":"226701","type":"TableShake","data":"[]"},
      {"id":"96535","timeSec":"1425543578","usec":"67659","type":"TableShake","data":"[]"},
      {"id":"96537","timeSec":"1425543578","usec":"67835","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96539","timeSec":"1425543901","usec":"685291","type":"TableShake","data":"[]"},
      {"id":"96540","timeSec":"1425543901","usec":"685465","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96541","timeSec":"1425543903","usec":"78746","type":"TableShake","data":"[]"},
      {"id":"96542","timeSec":"1425543907","usec":"302682","type":"AutoGoal","data":"{\u0022team\u0022:1}"},
      {"id":"96543","timeSec":"1425543907","usec":"303637","type":"CardSwipe","data":"{\u0022team\u0022:0,\u0022player\u0022:1,\u0022card_id\u0022:8469934}"},
      {"id":"96544","timeSec":"1425543914","usec":"309757","type":"AutoGoal","data":"{\u0022team\u0022:1}"}]}');
    }
}