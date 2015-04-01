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

    /**
     * @return array
     */
    public function getLastContest(){
        $repository = $this->getDoctrine()
            ->getRepository('IndigoContestBundle:Data');

        $return = $repository->findAll();

        return $return->getResult();
    }
}