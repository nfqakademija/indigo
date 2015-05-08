<?php

namespace Indigo\TableBundle\Event;

use Indigo\TableBundle\Model\TableActionInterface;
use Symfony\Component\EventDispatcher\Event;

class TableEvent  extends Event
{

    /**
     * @var TableActionInterface
     */
    private $model;


    /**
     * @return TableActionInterface
     */
    public function getTableEventModel()
    {
        return $this->model;
    }

    /**
     * @param TableActionInterface $model
     * @return $this
     */
    public function setTableEventModel(TableActionInterface $model)
    {
        $this->model = $model;
        return $this;
    }

}