<?php

namespace Indigo\TableBundle\Service\Manager;

use Indigo\TableBundle\Event\TableEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LogicManager
{
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
     * @param \ArrayIterator $eventList
     */
    public function analyzeEventFlow(\ArrayIterator $eventList)
    {
        if  ($eventList->valid()) {

            foreach ($eventList as $tableEventModel) {

                $tableEvent = new TableEvent();
                $tableEvent->setTableEventModel($tableEventModel);
                $this->ed->dispatch('indigo_table.new_event',  $tableEvent);
            }
        }
    }
}
