<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Roomtype\Model;

use Codilar\Roomtype\Api\Data\RoomtypeInterface;
use Magento\Framework\Model\AbstractModel;

class Roomtype extends AbstractModel implements RoomtypeInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Codilar\Roomtype\Model\ResourceModel\Roomtype::class);
    }

    /**
     * @inheritDoc
     */
    public function getRoomtypeId()
    {
        return $this->getData(self::ROOMTYPE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRoomtypeId($roomtypeId)
    {
        return $this->setData(self::ROOMTYPE_ID, $roomtypeId);
    }

    /**
     * @inheritDoc
     */
    public function getRoomtypeLabel()
    {
        return $this->getData(self::ROOMTYPE_LABEL);
    }

    /**
     * @inheritDoc
     */
    public function setRoomtypeLabel($roomtypeLabel)
    {
        return $this->setData(self::ROOMTYPE_LABEL, $roomtypeLabel);
    }

    /**
     * @inheritDoc
     */
    public function getRoomtypeValue()
    {
        return $this->getData(self::ROOMTYPE_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setRoomtypeValue($roomtypeValue)
    {
        return $this->setData(self::ROOMTYPE_VALUE, $roomtypeValue);
    }
}

