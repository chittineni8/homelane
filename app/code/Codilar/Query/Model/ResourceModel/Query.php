<?php

namespace Codilar\Query\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;


class Query extends AbstractDb
{
    const MAIN_TABLE = 'query';
    const ID_FIELD_NAME = 'id';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }
}
