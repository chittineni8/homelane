<?php

namespace Codilar\UomAttribute\Model\ResourceModel\UomAttribute;

use Codilar\UomAttribute\Model\UomAttribute as Model;
use Codilar\UomAttribute\Model\ResourceModel\UomAttribute as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
