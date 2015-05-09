<?php

namespace Indigo\ContestBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class ContestRepository extends EntityRepository implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function getNextContest()
    {
        try {
            
            $qb = $this->_em->createQueryBuilder();
            $qb->select('c')
                ->from('IndigoContestBundle:Contest', 'c')
                ->where('c.contestStartingDate > :date')
                ->orderBy('c.contestStartingDate', 'ASC')
                ->setMaxResults(1)
                ->setParameter('date', new \DateTime());

            return  $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {

            $this->logger && $this->logger->warning('no next constest' . $e->getMessage());
        }

        return null;
    }
}