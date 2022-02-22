<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Vendor\Model\ResourceModel\Vendor;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'vendor_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Codilar\Vendor\Model\Vendor::class,
            \Codilar\Vendor\Model\ResourceModel\Vendor::class
        );
    }
}

