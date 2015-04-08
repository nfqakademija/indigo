<?php

namespace Indigo\ApiBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Indigo\ApiBundle\Event\LogicEvent;
use Indigo\ApiBundle\Model\AutoGoalModel;
use Indigo\ApiBundle\Model\CardSwipeModel;
use Indigo\ApiBundle\Model\TableShakeModel;
use Doctrine\ORM\EntityManager;
use Indigo\GameBundle\Entity\Game;
use Indigo\GameBundle\Entity\GameTime;
use Indigo\GameBundle\Entity\GameTimeRepository;
use Indigo\UserBundle\Entity\User;


class GameListener {

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
     * @param LogicEvent $event
     */
    public function onNewEvent(LogicEvent $event)
    {
        if ($event->getTableEventModel() instanceof TableShakeModel) {
            // pasizymet last shake ts
        }  elseif ($event->getTableEventModel() instanceof AutoGoalModelModel) {
            // pridet score
        } elseif ($event->getTableEventModel() instanceof CardSwipeModel) {
            /*
             * ar kurti nauja zaidima ?
             */
            $data= $event->getTableEventModel();
            $gameTimeEntity = $this->em->getRepository('IndigoGameBundle:GameTime')->findOneBy(['id' => 1 ] );
            $userEntity =   $this->em->getRepository('IndigoUserBundle:User')->findOneBy(['id' => 6 ] );

            var_dump($gameTimeEntity);

            /*
            $gameEntity = new Game();
            $gameEntity->setGametimeId($gameTimeEntity);
            $gameEntity->setTeam0Player0Id($userEntity);
            $gameEntity->setTeam0Player1Id($userEntity);
            $gameEntity->setTeam1Player0Id($userEntity);
            $gameEntity->setTeam1Player1Id($userEntity);

            $this->em->persist($gameEntity);
            $this->em->flush();
            */

            //$repo = $this->em-> getRepository('IndigoGameBundle:Game');
            //$repo->persist()

        }
    }
}