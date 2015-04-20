<?php


namespace Indigo\ApiBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ApiEvent extends Event
{
    private $data;

    /**
     * @param \ArrayIterator $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return \ArrayIterator
     */
    public function getData()
    {
        return $this->data;
    }
}