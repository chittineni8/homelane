<?php

namespace Codilar\WareHouseMapping\Model\ResourceModel\WareHouseMapping;

use Codilar\WareHouseMapping\Model\WareHouseMapping as Model;
use Codilar\WareHouseMapping\Model\ResourceModel\WareHouseMapping as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
