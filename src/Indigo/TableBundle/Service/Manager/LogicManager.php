<?php

namespace Indigo\TableBundle\Service\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Entity\TableStatus;
use Indigo\GameBundle\Entity\TableStatusRepository;
use Indigo\GameBundle\Event\GameEvents;
use Indigo\TableBundle\Event\TableEvent;
use Indigo\TableBundle\Model\TableActionModel;
use Indigo\TableBundle\Model\TableShakeModel;
use Indigo\TableBundle\Repository\TableEventList;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LogicManager
{
    /**
     * @var EventDispatcherInterface
     */
    private $ed;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TimeoutManager
     */
    private $tm;

    /**
     * @param EventDispatcherInterface $ed
     */
    public function __construct(EventDispatcherInterface $ed, EntityManagerInterface $em, TimeoutManager $tm)
    {
        $this->ed = $ed;
        $this->em = $em;
        $this->tm = $tm;
    }

    /**
     * @param TableEventList $eventList
     */
    public function analyzeEventFlow(TableEventList $eventList)
    {
        $tableStatusEntity = null;
        if  ($eventList->valid()) {

            if ($tableId = $eventList->getTableId()) {

                if ($tableStatusEntity = $this->getTableStatus($tableId)) {
                    /** @var TableStatus $tableStatusEntity */
                    $lastEventTs = $tableStatusEntity->getLastApiRecordTs();
                }
            }

            foreach ($eventList as $tableEventModel) {

                if ($tableStatusEntity) {

                    if ($this->checkTimeout($lastEventTs, $tableEventModel)) {

                        $this->tm->handleTimeout($tableStatusEntity);
                    }

                    $lastEventTs = $tableEventModel->getTimeSec();
                    if ($tableEventModel instanceof TableShakeModel) {

                        continue;
                    }
                }
                $tableEvent = new TableEvent();
                $tableEvent->setTableEventModel($tableEventModel);
                $this->ed->dispatch('indigo_table.new_event',  $tableEvent);
            }

            if ($tableStatusEntity) {

                $tableStatusEntity->setLastApiRecordTs($lastEventTs);
                //TODO jei laikai neprasilenkia per timeouto ts, tada state -> busy
                $tableStatusEntity->setBusy(TableStatusRepository::STATUS_BUSY);
                $this->em->persist($tableStatusEntity);
                $this->em->flush();
            }
        }
    }

    /**
     * @param int $tableId
     * @return mixed
     */
    private function getTableStatus($tableId)
    {
        $tableStatusEntity = $this->em
            ->getRepository('IndigoGameBundle:TableStatus')
            ->findOneByTableId((int)$tableId);

        return $tableStatusEntity;
    }

    private function checkTimeout($lastEventTs,TableActionModel $tableEventModel)
    {
        return (bool) ($tableEventModel->getTimeSec() - $lastEventTs > TableStatusRepository::TIMEOUT);
    }
}
