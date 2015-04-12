<?php


class Event {
    /**
     * @var \stdClass
     */
    private $data;

    /**
     * @param array $data
     */
    function __construct($data)
    {
        $this->data = $data;
    }

}