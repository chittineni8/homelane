<?php

namespace Codilar\Query\Model\ResourceModel\Query;

use Codilar\Query\Model\Query as Model;
use Codilar\Query\Model\ResourceModel\Query as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
