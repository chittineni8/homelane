<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Model\ResourceModel\Priceversion;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'priceversion_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Codilar\Priceversion\Model\Priceversion::class,
            \Codilar\Priceversion\Model\ResourceModel\Priceversion::class
        );
    }
}

