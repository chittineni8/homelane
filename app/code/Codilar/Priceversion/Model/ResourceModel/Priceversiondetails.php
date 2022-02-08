<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Priceversiondetails extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('codilar_priceversion_priceversiondetails', 'priceversiondetails_id');
    }
}

