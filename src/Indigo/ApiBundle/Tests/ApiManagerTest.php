<?php

namespace Indigo\ApiBundle\Tests;


class ApiManagerTest extends \PHPUnit_Framework_TestCase
{
    public function apiEventProvider()
    {
        return [
            [
                '{"status":"ok","records":[{"id":"96010","timeSec":"1425520550","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:0,\u0022player\u0022:1,\u0022card_id\u0022:8461951}"}]}'

            ],
            [
                '{"status":"ok","records":[{"id":"96010","timeSec":"1425520550","usec":"485733","type":"CardSwipe","data":"{\u0022team\u0022:0,\u0022player\u0022:1,\u0022card_id\u0022:8461951}"}]}'
            ],
        ];
    }

    private function prepareEventData($tableEventJson)
    {
        $tableEvent = json_decode($tableEventJson);
        $tableEvent->data = json_decode($tableEvent->data);

        return $tableEvent;
    }

    /**
     * @dataProvider apiEventProvider
     */
    public function testIfApiDoesNotFailOnUnknownTableEvent($tableEventJson)
    {
        $managerMock = $this->getManagerMock();

        $managerMock->method('getClient')->will($this->returnValue($this->getClientMock()));

        $managerMock->parseResponseData($this->prepareEventData($tableEventJson));
    }


    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Indigo\ApiBundle\Service\Manager\ApiManager
     */
    private function getManagerMock()
    {
        $mock = $this->getMockBuilder('Indigo\ApiBundle\Service\Manager\ApiManager');
        $mock->disableOriginalConstructor();
        $mock->setMethods(['getClient', 'getTable']);


        return $mock->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getClientMock()
    {
        $mock = $this->getMockBuilder('GuzzleHttp\Client');
        $mock->disableOriginalConstructor();
        return $mock->getMock();
    }
}
