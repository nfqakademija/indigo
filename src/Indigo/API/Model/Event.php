<?php

namespace Indigo\API\Model;

abstract class Event {


    /*    array(5) {
      'id' =>
      string(6) "181550"
      'timeSec' =>
      string(10) "1426838774"
      'usec' =>
      string(6) "751909"
      'type' =>
      string(8) "AutoGoal"
      'data' =>
      string(10) "{"team":0}"
    }
*/
    private $id;

    /**
     * @var \stdClass
     */
    private $data;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \stdClass
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \stdClass $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }




}