<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PriceversionRepositoryInterface
{

    /**
     * Save Priceversion
     * @param \Codilar\Priceversion\Api\Data\PriceversionInterface $priceversion
     * @return \Codilar\Priceversion\Api\Data\PriceversionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Codilar\Priceversion\Api\Data\PriceversionInterface $priceversion
    );

    /**
     * Retrieve Priceversion
     * @param string $priceversionId
     * @return \Codilar\Priceversion\Api\Data\PriceversionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($priceversionId);

    /**
     * Retrieve Priceversion matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Codilar\Priceversion\Api\Data\PriceversionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Priceversion
     * @param \Codilar\Priceversion\Api\Data\PriceversionInterface $priceversion
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Codilar\Priceversion\Api\Data\PriceversionInterface $priceversion
    );

    /**
     * Delete Priceversion by ID
     * @param string $priceversionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($priceversionId);
}

