<?php

namespace Codilar\BrandAttribute\Model\ResourceModel\BrandAttribute;

use Codilar\BrandAttribute\Model\BrandAttribute as Model;
use Codilar\BrandAttribute\Model\ResourceModel\BrandAttribute as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
