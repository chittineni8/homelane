<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Vendor\Api\Data;

interface VendorSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Vendor list.
     * @return \Codilar\Vendor\Api\Data\VendorInterface[]
     */
    public function getItems();

    /**
     * Set automat_vendor_id list.
     * @param \Codilar\Vendor\Api\Data\VendorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

