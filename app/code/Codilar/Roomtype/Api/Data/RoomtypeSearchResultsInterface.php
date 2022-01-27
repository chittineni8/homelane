<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Roomtype\Api\Data;

interface RoomtypeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Roomtype list.
     * @return \Codilar\Roomtype\Api\Data\RoomtypeInterface[]
     */
    public function getItems();

    /**
     * Set roomtype_label list.
     * @param \Codilar\Roomtype\Api\Data\RoomtypeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

