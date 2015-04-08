<?php

namespace Indigo\ApiBundle\EventListener;

use Symfony\Bridge\Doctrine;
use Doctrine\ORM\EntityManager;
use Indigo\ApiBundle\Event\ApiEvent;
use Indigo\ApiBundle\Entity\Param;
use Indigo\ApiBundle\Service\Manager\Manager;

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
        if ($count = $event->getData()->count()) {
            $event->getData()->seek($count - 1);
            $lastTableEvent = $event->getData()->current();

            if (! $paramLastRecordId = $this->em->getRepository('IndigoApiBundle:Param')->findOneBy([ 'param' => Manager::LAST_RECORD_ID])) {
                $paramLastRecordId = new Param();
            }

            if (! $paramLastRecordTs = $this->em->getRepository('IndigoApiBundle:Param')->findOneBy([ 'param' => Manager::LAST_RECORD_TS])) {
                $paramLastRecordTs = new Param();
            }

            if (! $paramLastRecordCount = $this->em->getRepository('IndigoApiBundle:Param')->findOneBy([ 'param' => Manager::LAST_RECORDS_COUNT])) {
                $paramLastRecordCount = new Param();
            }

            $paramLastRecordId->setParamAndValue(Manager::LAST_RECORD_ID, $lastTableEvent->getId());
            $paramLastRecordTs->setParamAndValue(Manager::LAST_RECORD_TS, $lastTableEvent->getTimeSec());
            $paramLastRecordCount->setParamAndValue(Manager::LAST_RECORDS_COUNT, $count);

            $this->em->persist($paramLastRecordId);
            $this->em->persist($paramLastRecordTs);
            $this->em->persist($paramLastRecordCount);

            $this->em->flush();
        } else {
            //TODO: jei 0 eventu - we have most fresh info ?

        }
    }
}