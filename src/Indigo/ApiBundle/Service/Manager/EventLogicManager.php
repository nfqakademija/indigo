<?php

namespace Indigo\ApiBundle\Service\Manager;


use Indigo\ApiBundle\Event\LogicEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventLogicManager
{
    /**
     * @var EventDispatcher
     */
    private $ed;

    public function __construct(EventDispatcherInterface $ed)
    {
        $this->ed = $ed;
    }

    /**
     * @param \ArrayIterator $eventList
     */
    public function analyzeEventFlow(\ArrayIterator $eventList)
    {
        $TableShake =  null;

        if  ($eventList->valid()) {

            foreach ($eventList as $tableEventModel) {
                $tableEvent = new LogicEvent();
                $tableEvent->setTableEventModel($tableEventModel);
                $this->ed->dispatch('indigo_logic.new_table_event',  $tableEvent);
            }
        }
    }
}