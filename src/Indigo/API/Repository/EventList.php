<?php

namespace Indigo\API\Repository;

class EventList extends \ArrayIterator{



    /**
     * @return Event
     */
    public  function current()
    {
        return new Event(parent::current());
    }
}