<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Api\Data;

interface PriceversiondetailsInterface
{

    const MIN_ORDER_QTY = 'min_order_qty';
    const STATUS = 'status';
    const PRICEVERSIONDETAILS_ID = 'priceversiondetails_id';
    const ADD_PRICE = 'add_price';
    const UPDATED_AT = 'updated_at';
    const MIN_ORDER_VALUE = 'min_order_value';
    const PRICE = 'price';
    const MULTI = 'multi';
    const CREATED_AT = 'created_at';
    const SKU = 'sku';
    const PRICE_VERSION_ID = 'price_version_id';

    /**
     * Get priceversiondetails_id
     * @return string|null
     */
    public function getPriceversiondetailsId();

    /**
     * Set priceversiondetails_id
     * @param string $priceversiondetailsId
     * @return \Codilar\Priceversion\Priceversiondetails\Api\Data\PriceversiondetailsInterface
     */
    public function setPriceversiondetailsId($priceversiondetailsId);

    /**
     * Get price_version_id
     * @return string|null
     */
    public function getPriceVersionId();

    /**
     * Set price_version_id
     * @param string $priceVersionId
     * @return \Codilar\Priceversion\Priceversiondetails\Api\Data\PriceversiondetailsInterface
     */
    public function setPriceVersionId($priceVersionId);

    /**
     * Get sku
     * @return string|null
     */
    public function getSku();

    /**
     * Set sku
     * @param string $sku
     * @return \Codilar\Priceversion\Priceversiondetails\Api\Data\PriceversiondetailsInterface
     */
    public function setSku($sku);

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return \Codilar\Priceversion\Priceversiondetails\Api\Data\PriceversiondetailsInterface
     */
    public function setPrice($price);

    /**
     * Get add_price
     * @return string|null
     */
    public function getAddPrice();

    /**
     * Set add_price
     * @param string $addPrice
     * @return \Codilar\Priceversion\Priceversiondetails\Api\Data\PriceversiondetailsInterface
     */
    public function setAddPrice($addPrice);

    /**
     * Get multi
     * @return string|null
     */
    public function getMulti();

    /**
     * Set multi
     * @param string $multi
     * @return \Codilar\Priceversion\Priceversiondetails\Api\Data\PriceversiondetailsInterface
     */
    public function setMulti($multi);

    /**
     * Get min_order_qty
     * @return string|null
     */
    public function getMinOrderQty();

    /**
     * Set min_order_qty
     * @param string $minOrderQty
     * @return \Codilar\Priceversion\Priceversiondetails\Api\Data\PriceversiondetailsInterface
     */
    public function setMinOrderQty($minOrderQty);

    /**
     * Get min_order_value
     * @return string|null
     */
    public function getMinOrderValue();

    /**
     * Set min_order_value
     * @param string $minOrderValue
     * @return \Codilar\Priceversion\Priceversiondetails\Api\Data\PriceversiondetailsInterface
     */
    public function setMinOrderValue($minOrderValue);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Codilar\Priceversion\Priceversiondetails\Api\Data\PriceversiondetailsInterface
     */
    public function setStatus($status);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Codilar\Priceversion\Priceversiondetails\Api\Data\PriceversiondetailsInterface
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
     * @return \Codilar\Priceversion\Priceversiondetails\Api\Data\PriceversiondetailsInterface
     */
    public function setUpdatedAt($updatedAt);
}

