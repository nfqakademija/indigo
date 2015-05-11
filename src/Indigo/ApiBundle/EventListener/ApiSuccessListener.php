<?php

namespace Indigo\ApiBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Indigo\ApiBundle\Event\ApiEvent;
use Indigo\GameBundle\Entity\TableStatus;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Bridge\Doctrine;

class ApiSuccessListener implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param ApiEvent $event
     */
    public function onResponseSuccess(ApiEvent $event)
    {
        if ($count = $event->getData()->count()) {

            $event->getData()->seek($count - 1);
            $lastTableEvent = $event->getData()->current();

            $tableStatusEntity = $this->em
                ->getRepository('IndigoGameBundle:TableStatus')
                ->findOneByTableId($lastTableEvent->getTableId());

            if (!$tableStatusEntity) {

                $tableStatusEntity = new TableStatus();
                $tableStatusEntity->setTableId($lastTableEvent->getTableId());
                $tableStatusEntity->setGame(null);
            }

            $tableStatusEntity->setLastApiRecordTs($lastTableEvent->getTimeSec());
            $tableStatusEntity->setLastApiRecordId($lastTableEvent->getId());
            $tableStatusEntity->setLastUpdateTs(time());
            printf ("setting last update ts: %d and id: %d\n", time(), $lastTableEvent->getId());
            $this->em->persist($tableStatusEntity);
            $this->em->flush();
            $this->logger && $this->logger->debug('EVENT DUMP SUCCESSFUL', ['last_event_id' => $lastTableEvent->getId()]);
        } else {

            $this->logger && $this->logger->warning('no events!', ['events' => $event->getData()]);
        }
    }
}