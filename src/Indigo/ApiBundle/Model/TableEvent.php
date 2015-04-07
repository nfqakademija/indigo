<?php

namespace Indigo\ApiBundle\Model;

abstract class TableEvent {

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $timeSec;

    /**
     * @var int
     */
    private $usec;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $table_id = 1;




    public function __construct($data)
    {

        $this->setId($data['id']);
        $this->setTimeSec($data['timeSec']);
        $this->setUsec($data['usec']);
        $this->setType($data['type']);
        $this->setData(json_decode($data['data'], true));
    }

    /**
     * @return int
     */
    public function getTableId()
    {
        return $this->table_id;
    }

    /**
     * @param int $table_id
     */
    public function setTableId($table_id)
    {
        $this->table_id = $table_id;
    }
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getTimeSec()
    {
        return $this->timeSec;
    }

    /**
     * @param int $timeSec
     */
    public function setTimeSec($timeSec)
    {
        $this->timeSec = $timeSec;
    }

    /**
     * @return int
     */
    public function getUsec()
    {
        return $this->usec;
    }

    /**
     * @param int $usec
     */
    public function setUsec($usec)
    {
        $this->usec = $usec;
    }

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
    public function setData(array $data)
    {
        $this->data = $data;
    }




}