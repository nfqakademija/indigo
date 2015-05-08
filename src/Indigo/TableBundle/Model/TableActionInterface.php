<?php

namespace Indigo\TableBundle\Model;

interface TableActionInterface {

    /**
     * @param \stdClass $data
     * @return boolean
     */
    public function setData(\stdClass $data);
}