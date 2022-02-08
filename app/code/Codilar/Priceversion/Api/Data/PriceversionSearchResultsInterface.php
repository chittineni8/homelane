<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Api\Data;

interface PriceversionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Priceversion list.
     * @return \Codilar\Priceversion\Api\Data\PriceversionInterface[]
     */
    public function getItems();

    /**
     * Set version_code list.
     * @param \Codilar\Priceversion\Api\Data\PriceversionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

