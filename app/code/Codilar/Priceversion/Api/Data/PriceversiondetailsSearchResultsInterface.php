<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Api\Data;

interface PriceversiondetailsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Priceversiondetails list.
     * @return \Codilar\Priceversion\Api\Data\PriceversiondetailsInterface[]
     */
    public function getItems();

    /**
     * Set price_version_id list.
     * @param \Codilar\Priceversion\Api\Data\PriceversiondetailsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

