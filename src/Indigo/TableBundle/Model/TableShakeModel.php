<?php

namespace Indigo\TableBundle\Model;

class TableShakeModel extends TableActionModel implements TableActionInterface
{
    /**
     * @param \stdClass $data
     * @return boolean
     */
    public function setData(\stdClass $data)
    {

      return true;
    }

}
