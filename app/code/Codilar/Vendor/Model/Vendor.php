<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Vendor\Model;

use Codilar\Vendor\Api\Data\VendorInterface;
use Magento\Framework\Model\AbstractModel;

class Vendor extends AbstractModel implements VendorInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Codilar\Vendor\Model\ResourceModel\Vendor::class);
    }

    /**
     * @inheritDoc
     */
    public function getVendorId()
    {
        return $this->getData(self::VENDOR_ID);
    }

    /**
     * @inheritDoc
     */
    public function setVendorId($vendorId)
    {
        return $this->setData(self::VENDOR_ID, $vendorId);
    }

    /**
     * @inheritDoc
     */
    public function getAutomatVendorId()
    {
        return $this->getData(self::AUTOMAT_VENDOR_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAutomatVendorId($automatVendorId)
    {
        return $this->setData(self::AUTOMAT_VENDOR_ID, $automatVendorId);
    }

    /**
     * @inheritDoc
     */
    public function getAutomatVendorName()
    {
        return $this->getData(self::AUTOMAT_VENDOR_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setAutomatVendorName($automatVendorName)
    {
        return $this->setData(self::AUTOMAT_VENDOR_NAME, $automatVendorName);
    }

    /**
     * @inheritDoc
     */
    public function getSapVendorId()
    {
        return $this->getData(self::SAP_VENDOR_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSapVendorId($sapVendorId)
    {
        return $this->setData(self::SAP_VENDOR_ID, $sapVendorId);
    }

    /**
     * @inheritDoc
     */
    public function getSapVendorName()
    {
        return $this->getData(self::SAP_VENDOR_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setSapVendorName($sapVendorName)
    {
        return $this->setData(self::SAP_VENDOR_NAME, $sapVendorName);
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * @inheritDoc
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * @inheritDoc
     */
    public function getLeadTime()
    {
        return $this->getData(self::LEAD_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setLeadTime($leadTime)
    {
        return $this->setData(self::LEAD_TIME, $leadTime);
    }

    /**
     * @inheritDoc
     */
    public function getBuyingPrice()
    {
        return $this->getData(self::BUYING_PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setBuyingPrice($buyingPrice)
    {
        return $this->setData(self::BUYING_PRICE, $buyingPrice);
    }

    /**
     * @inheritDoc
     */
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * @inheritDoc
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @inheritDoc
     */
    public function getVendorSku()
    {
        return $this->getData(self::VENDOR_SKU);
    }

    /**
     * @inheritDoc
     */
    public function setVendorSku($vendorSku)
    {
        return $this->setData(self::VENDOR_SKU, $vendorSku);
    }

    /**
     * @inheritDoc
     */
    public function getVendorSkuStatus()
    {
        return $this->getData(self::VENDOR_SKU_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setVendorSkuStatus($vendorSkuStatus)
    {
        return $this->setData(self::VENDOR_SKU_STATUS, $vendorSkuStatus);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}

