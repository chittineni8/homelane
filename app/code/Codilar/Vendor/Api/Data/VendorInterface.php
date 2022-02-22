<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Vendor\Api\Data;

interface VendorInterface
{

    const VENDOR_ID = 'vendor_id';
    const SAP_VENDOR_ID = 'sap_vendor_id';
    const BUYING_PRICE = 'buying_price';
    const UPDATED_AT = 'updated_at';
    const VENDOR_SKU = 'vendor_sku';
    const LEAD_TIME = 'lead_time';
    const SAP_VENDOR_NAME = 'sap_vendor_name';
    const VENDOR_SKU_STATUS = 'vendor_sku_status';
    const CREATED_AT = 'created_at';
    const SKU = 'sku';
    const AUTOMAT_VENDOR_ID = 'automat_vendor_id';
    const AUTOMAT_VENDOR_NAME = 'automat_vendor_name';
    const CITY = 'city';

    /**
     * Get vendor_id
     * @return string|null
     */
    public function getVendorId();

    /**
     * Set vendor_id
     * @param string $vendorId
     * @return \Codilar\Vendor\Vendor\Api\Data\VendorInterface
     */
    public function setVendorId($vendorId);

    /**
     * Get automat_vendor_id
     * @return string|null
     */
    public function getAutomatVendorId();

    /**
     * Set automat_vendor_id
     * @param string $automatVendorId
     * @return \Codilar\Vendor\Vendor\Api\Data\VendorInterface
     */
    public function setAutomatVendorId($automatVendorId);

    /**
     * Get automat_vendor_name
     * @return string|null
     */
    public function getAutomatVendorName();

    /**
     * Set automat_vendor_name
     * @param string $automatVendorName
     * @return \Codilar\Vendor\Vendor\Api\Data\VendorInterface
     */
    public function setAutomatVendorName($automatVendorName);

    /**
     * Get sap_vendor_id
     * @return string|null
     */
    public function getSapVendorId();

    /**
     * Set sap_vendor_id
     * @param string $sapVendorId
     * @return \Codilar\Vendor\Vendor\Api\Data\VendorInterface
     */
    public function setSapVendorId($sapVendorId);

    /**
     * Get sap_vendor_name
     * @return string|null
     */
    public function getSapVendorName();

    /**
     * Set sap_vendor_name
     * @param string $sapVendorName
     * @return \Codilar\Vendor\Vendor\Api\Data\VendorInterface
     */
    public function setSapVendorName($sapVendorName);

    /**
     * Get city
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     * @param string $city
     * @return \Codilar\Vendor\Vendor\Api\Data\VendorInterface
     */
    public function setCity($city);

    /**
     * Get lead_time
     * @return string|null
     */
    public function getLeadTime();

    /**
     * Set lead_time
     * @param string $leadTime
     * @return \Codilar\Vendor\Vendor\Api\Data\VendorInterface
     */
    public function setLeadTime($leadTime);

    /**
     * Get buying_price
     * @return string|null
     */
    public function getBuyingPrice();

    /**
     * Set buying_price
     * @param string $buyingPrice
     * @return \Codilar\Vendor\Vendor\Api\Data\VendorInterface
     */
    public function setBuyingPrice($buyingPrice);

    /**
     * Get sku
     * @return string|null
     */
    public function getSku();

    /**
     * Set sku
     * @param string $sku
     * @return \Codilar\Vendor\Vendor\Api\Data\VendorInterface
     */
    public function setSku($sku);

    /**
     * Get vendor_sku
     * @return string|null
     */
    public function getVendorSku();

    /**
     * Set vendor_sku
     * @param string $vendorSku
     * @return \Codilar\Vendor\Vendor\Api\Data\VendorInterface
     */
    public function setVendorSku($vendorSku);

    /**
     * Get vendor_sku_status
     * @return string|null
     */
    public function getVendorSkuStatus();

    /**
     * Set vendor_sku_status
     * @param string $vendorSkuStatus
     * @return \Codilar\Vendor\Vendor\Api\Data\VendorInterface
     */
    public function setVendorSkuStatus($vendorSkuStatus);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Codilar\Vendor\Vendor\Api\Data\VendorInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Codilar\Vendor\Vendor\Api\Data\VendorInterface
     */
    public function setUpdatedAt($updatedAt);
}

