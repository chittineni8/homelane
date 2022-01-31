<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Roomtype\Api\Data;

interface RoomtypeInterface
{

    const ROOMTYPE_VALUE = 'roomtype_value';
    const ROOMTYPE_LABEL = 'roomtype_label';
    const ROOMTYPE_ID = 'roomtype_id';

    /**
     * Get roomtype_id
     * @return string|null
     */
    public function getRoomtypeId();

    /**
     * Set roomtype_id
     * @param string $roomtypeId
     * @return \Codilar\Roomtype\Roomtype\Api\Data\RoomtypeInterface
     */
    public function setRoomtypeId($roomtypeId);

    /**
     * Get roomtype_label
     * @return string|null
     */
    public function getRoomtypeLabel();

    /**
     * Set roomtype_label
     * @param string $roomtypeLabel
     * @return \Codilar\Roomtype\Roomtype\Api\Data\RoomtypeInterface
     */
    public function setRoomtypeLabel($roomtypeLabel);

    /**
     * Get roomtype_value
     * @return string|null
     */
    public function getRoomtypeValue();

    /**
     * Set roomtype_value
     * @param string $roomtypeValue
     * @return \Codilar\Roomtype\Roomtype\Api\Data\RoomtypeInterface
     */
    public function setRoomtypeValue($roomtypeValue);
}

