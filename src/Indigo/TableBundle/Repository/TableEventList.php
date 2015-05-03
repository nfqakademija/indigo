<?php

namespace Indigo\TableBundle\Repository;


class TableEventList extends \ArrayIterator
{
    /**
     * @var integer
     */
    private $tableId;

    /**
     * @return int
     */
    public function getTableId()
    {
        return $this->tableId;
    }

    /**
     * @param int $tableId
     */
    public function setTableId($tableId)
    {
        $this->tableId = $tableId;
    }



}