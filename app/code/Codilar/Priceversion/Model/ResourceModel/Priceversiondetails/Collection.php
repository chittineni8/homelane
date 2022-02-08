<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Model\ResourceModel\Priceversiondetails;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'priceversiondetails_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Codilar\Priceversion\Model\Priceversiondetails::class,
            \Codilar\Priceversion\Model\ResourceModel\Priceversiondetails::class
        );
    }
}

