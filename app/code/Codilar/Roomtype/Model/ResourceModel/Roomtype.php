<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Roomtype\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Roomtype extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('codilar_roomtype_roomtype', 'roomtype_id');
    }
}

