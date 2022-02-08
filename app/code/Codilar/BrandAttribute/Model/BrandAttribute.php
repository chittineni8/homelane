<?php

namespace Codilar\BrandAttribute\Model;

use Magento\Framework\Model\AbstractModel;
use Codilar\BrandAttribute\Model\ResourceModel\BrandAttribute as ResourceModel;


class BrandAttribute extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
