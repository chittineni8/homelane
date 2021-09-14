<?php
namespace Codilar\QueryFrom\Model;

use Magento\Framework\Model\AbstractModel;

class Query extends AbstractModel{

    protected function _construct()
    {
        $this->_init(\Codilar\QueryFrom\Model\ResourceModel\Query::class);
    }
}
