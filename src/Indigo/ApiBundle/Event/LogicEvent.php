<?php

namespace Indigo\ApiBundle\Event;

use Indigo\ApiBundle\Model\TableActionInterface;
use Symfony\Component\EventDispatcher\Event;

class LogicEvent  extends Event
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