<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Roomtype\Model\ResourceModel\Roomtype;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'roomtype_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Codilar\Roomtype\Model\Roomtype::class,
            \Codilar\Roomtype\Model\ResourceModel\Roomtype::class
        );
    }
}

