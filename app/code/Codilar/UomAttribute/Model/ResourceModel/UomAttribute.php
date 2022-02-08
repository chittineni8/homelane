<?php

namespace Codilar\UomAttribute\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;


class UomAttribute extends AbstractDb
{
    const MAIN_TABLE = 'uomattribute';
    const ID_FIELD_NAME = 'id';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }
}
