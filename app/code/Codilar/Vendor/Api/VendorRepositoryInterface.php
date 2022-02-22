<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Vendor\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface VendorRepositoryInterface
{

    /**
     * Save Vendor
     * @param \Codilar\Vendor\Api\Data\VendorInterface $vendor
     * @return \Codilar\Vendor\Api\Data\VendorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Codilar\Vendor\Api\Data\VendorInterface $vendor
    );

    /**
     * Retrieve Vendor
     * @param string $vendorId
     * @return \Codilar\Vendor\Api\Data\VendorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($vendorId);

    /**
     * Retrieve Vendor matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Codilar\Vendor\Api\Data\VendorSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Vendor
     * @param \Codilar\Vendor\Api\Data\VendorInterface $vendor
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Codilar\Vendor\Api\Data\VendorInterface $vendor
    );

    /**
     * Delete Vendor by ID
     * @param string $vendorId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($vendorId);
}

