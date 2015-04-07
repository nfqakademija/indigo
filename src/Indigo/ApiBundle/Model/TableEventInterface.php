<?php

namespace Indigo\ApiBundle\Model;


interface TableEventInterface {

    /**
     * @param array $data
     * @return boolean
     */
    public function setData(array $data);
}