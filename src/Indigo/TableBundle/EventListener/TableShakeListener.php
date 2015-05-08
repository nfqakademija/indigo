<?php


namespace Indigo\TableBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Indigo\GameBundle\Entity\TableStatus;
use Indigo\TableBundle\Event\TableEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Indigo\TableBundle\Model\TableShakeModel;

class TableShakeListener {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $eventModel
     * @return bool
     */
    public function accepts($eventModel)
    {
        return ($eventModel instanceof TableShakeModel);
    }

    /**
     * @param TableEvent $event
     */
    public function onEvent(TableEvent $event)
    {
        $tableEventModel = $event->getTableEventModel();

        if (!$this->accepts($tableEventModel)) {
            return;
        }
        
        printf (" - TableShake!! event: %d, on table: %u\n", $tableEventModel->getId(), $tableEventModel->getTableId());
        $tableStatusEntity = $this->em->getRepository('IndigoGameBundle:TableStatus')
            ->findOneById($tableEventModel->getTableId());
        if ($tableStatusEntity) {

            $tableStatusEntity->setLastTableShakeTs((new \DateTime())->getTimestamp());
            $this->em->persist($tableStatusEntity);
            $this->em->flush();
        } else {

            printf("nerastas stalas!\n");
        }
    }
}