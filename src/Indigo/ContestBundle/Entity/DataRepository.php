<?php
/**
 * Created by PhpStorm.
 * User: Tadas
 * Date: 2015-04-01
 * Time: 16:34
 */

namespace Indigo\ContestBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DataRepository extends EntityRepository{
    public function getLastContest()
    {
        $repository = $this->getDoctrine()
            ->getRepository('ContestBundle:Data');

        $return = $repository->findAll();

        return $return->getResult();
    }
}