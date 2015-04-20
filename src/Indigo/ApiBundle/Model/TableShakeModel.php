<?php


namespace Indigo\ApiBundle\Model;


class TableShakeModel extends TableActionModel implements TableActionInterface
{
    /**
     * @param \stdClass $data
     * @return boolean
     */
    public function setData(\stdClass $data) {
      return true;
    }

}
/*
 *         {"id":"96511","timeSec":"1425543550","usec":"93454","type":"TableShake",
        "data":"[]"},
 */
