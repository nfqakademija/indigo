<?php


namespace Indigo\ApiBundle\Model;


class TableShakeEvent extends TableEvent implements TableEventInterface
{
    /**
     * @param array $data
     * @return boolean
     */
    public function setData(array $data) {
      return true;
    }

}
/*
 *         {"id":"96511","timeSec":"1425543550","usec":"93454","type":"TableShake",
        "data":"[]"},
 */
