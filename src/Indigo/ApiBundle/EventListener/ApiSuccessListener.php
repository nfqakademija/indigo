<?php

namespace Indigo\ApiBundle\EventListener;

use Indigo\GameBundle\Entity\Game;
use Symfony\Bridge\Doctrine;
use Doctrine\ORM\EntityManager;
use Indigo\ApiBundle\Event\ApiEvent;
use Indigo\ApiBundle\Entity\Param;
use Indigo\ApiBundle\Service\Manager\ApiManager;
use Indigo\GameBundle\Entity\TableStatus;

class ApiSuccessListener
{

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
        //TODO: iskelti i tablestatus lenta
        if ($count = $event->getData()->count()) {

            $event->getData()->seek($count - 1);
            $lastTableEvent = $event->getData()->current();

            $tableStatusEntity = $this->em
                ->getRepository('IndigoGameBundle:TableStatus')
                ->findOneByTableId($lastTableEvent->getTableId());

            if (!$tableStatusEntity) {

                $game = new Game();
                $tableStatusEntity = new TableStatus();
                $tableStatusEntity->setTableId($lastTableEvent->getTableId());
                $tableStatusEntity->setGames($game);
            }

            //$tableStatusEntity->setLastApiRecordId($lastTableEvent->getId());
            $tableStatusEntity->setLastApiRecordId(616);
            $tableStatusEntity->setLastApiRecordTs($lastTableEvent->getTimeSec());
            $this->em->persist($tableStatusEntity);
            $this->em->flush();
            printf("last API event id:%u\n", $lastTableEvent->getId());
        } else {
            //TODO: jei 0 eventu - we have most fresh info ?

        }
    }
}