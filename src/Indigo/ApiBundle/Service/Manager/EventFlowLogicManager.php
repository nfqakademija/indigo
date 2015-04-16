<?php

namespace Indigo\ApiBundle\Service\Manager;


use Indigo\ApiBundle\Event\TableEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventFlowLogicManager
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
                $tableEvent = new TableEvent();
                $tableEvent->setTableEventModel($tableEventModel);
                //var_dump($tableEventModel);
                $this->ed->dispatch('indigo_table.new_event',  $tableEvent);
            }
        }
    }
}