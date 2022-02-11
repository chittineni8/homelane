<?php

namespace Codilar\WareHouseMapping\Model;

use Magento\Framework\Model\AbstractModel;
use Codilar\WareHouseMapping\Model\ResourceModel\WareHouseMapping as ResourceModel;


class WareHouseMapping extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
