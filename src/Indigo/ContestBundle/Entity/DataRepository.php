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
        $builder= $this->_em->createQueryBuilder();

        $builder->select('c')
                ->from('IndigoContestBundle:Data', 'c')
                ->orderBy('c.id', 'DESC');

        $return = $builder->getQuery();

        return $return->setMaxResults(1)->getResult();
    }
}