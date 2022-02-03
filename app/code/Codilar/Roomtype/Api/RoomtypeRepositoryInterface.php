<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Roomtype\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface RoomtypeRepositoryInterface
{

    /**
     * Save Roomtype
     * @param \Codilar\Roomtype\Api\Data\RoomtypeInterface $roomtype
     * @return \Codilar\Roomtype\Api\Data\RoomtypeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Codilar\Roomtype\Api\Data\RoomtypeInterface $roomtype
    );


    /**
     * @return mixed
     */
    public function get();

    /**
     * Retrieve Roomtype matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Codilar\Roomtype\Api\Data\RoomtypeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Roomtype
     * @param \Codilar\Roomtype\Api\Data\RoomtypeInterface $roomtype
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Codilar\Roomtype\Api\Data\RoomtypeInterface $roomtype
    );

    /**
     * Delete Roomtype by ID
     * @param string $roomtypeId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($roomtypeId);
}

