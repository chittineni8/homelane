<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Model;

use Codilar\Priceversion\Api\Data\PriceversiondetailsInterface;
use Magento\Framework\Model\AbstractModel;

class Priceversiondetails extends AbstractModel implements PriceversiondetailsInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Codilar\Priceversion\Model\ResourceModel\Priceversiondetails::class);
    }

    /**
     * @inheritDoc
     */
    public function getPriceversiondetailsId()
    {
        return $this->getData(self::PRICEVERSIONDETAILS_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPriceversiondetailsId($priceversiondetailsId)
    {
        return $this->setData(self::PRICEVERSIONDETAILS_ID, $priceversiondetailsId);
    }

    /**
     * @inheritDoc
     */
    public function getPriceVersionId()
    {
        return $this->getData(self::PRICE_VERSION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPriceVersionId($priceVersionId)
    {
        return $this->setData(self::PRICE_VERSION_ID, $priceVersionId);
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
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function getAddPrice()
    {
        return $this->getData(self::ADD_PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setAddPrice($addPrice)
    {
        return $this->setData(self::ADD_PRICE, $addPrice);
    }

    /**
     * @inheritDoc
     */
    public function getMulti()
    {
        return $this->getData(self::MULTI);
    }

    /**
     * @inheritDoc
     */
    public function setMulti($multi)
    {
        return $this->setData(self::MULTI, $multi);
    }

    /**
     * @inheritDoc
     */
    public function getMinOrderQty()
    {
        return $this->getData(self::MIN_ORDER_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setMinOrderQty($minOrderQty)
    {
        return $this->setData(self::MIN_ORDER_QTY, $minOrderQty);
    }

    /**
     * @inheritDoc
     */
    public function getMinOrderValue()
    {
        return $this->getData(self::MIN_ORDER_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setMinOrderValue($minOrderValue)
    {
        return $this->setData(self::MIN_ORDER_VALUE, $minOrderValue);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
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

