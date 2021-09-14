<?php
namespace Codilar\QueryFrom\Model\ResourceModel\Query;

use Codilar\QueryFrom\Model\Query;
use Codilar\QueryFrom\Model\ResourceModel\Query as ResourceModelQuery;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Query::class, ResourceModelQuery::class);
    }
}
