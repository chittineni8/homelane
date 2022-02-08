<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PriceversiondetailsRepositoryInterface
{

    /**
     * Save Priceversiondetails
     * @param \Codilar\Priceversion\Api\Data\PriceversiondetailsInterface $priceversiondetails
     * @return \Codilar\Priceversion\Api\Data\PriceversiondetailsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Codilar\Priceversion\Api\Data\PriceversiondetailsInterface $priceversiondetails
    );

    /**
     * Retrieve Priceversiondetails
     * @param string $priceversiondetailsId
     * @return \Codilar\Priceversion\Api\Data\PriceversiondetailsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($priceversiondetailsId);

    /**
     * Retrieve Priceversiondetails matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Codilar\Priceversion\Api\Data\PriceversiondetailsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Priceversiondetails
     * @param \Codilar\Priceversion\Api\Data\PriceversiondetailsInterface $priceversiondetails
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Codilar\Priceversion\Api\Data\PriceversiondetailsInterface $priceversiondetails
    );

    /**
     * Delete Priceversiondetails by ID
     * @param string $priceversiondetailsId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($priceversiondetailsId);
}

