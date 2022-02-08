<?php

namespace Codilar\UomAttribute\Model;

use Magento\Framework\Model\AbstractModel;
use Codilar\UomAttribute\Model\ResourceModel\UomAttribute as ResourceModel;


class UomAttribute extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
