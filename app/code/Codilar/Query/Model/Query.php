<?php

namespace Codilar\Query\Model;

use Magento\Framework\Model\AbstractModel;
use Codilar\Query\Model\ResourceModel\Query as ResourceModel;


class Query extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
