<?php
/**
 * Created by PhpStorm.
 * User: Tadas
 * Date: 2015-03-22
 * Time: 20:54
 */

namespace Indigo\LocationsRegBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LocationRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getAllLocations()
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('l')
            ->from('IndigoLocationsRegBundle:Location', 'l');

        $query = $qb->getQuery();

        return $query->getArrayResult();
    }
}
