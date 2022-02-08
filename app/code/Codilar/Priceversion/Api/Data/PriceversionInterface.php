<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Api\Data;

interface PriceversionInterface
{

    const STATUS = 'status';
    const VERSION_LABEL = 'version_label';
    const PRICEVERSION_ID = 'priceversion_id';
    const UPDATED_AT = 'updated_at';
    const VERSION_CODE = 'version_code';
    const CREATED_AT = 'created_at';
    const LAUNCH_DATE = 'launch_date';
    const SUB_CAT = 'sub_cat';
    const COPY_FROM_VERSION_ID = 'copy_from_version_id';
    const WEBSITE = 'website';

    /**
     * Get priceversion_id
     * @return string|null
     */
    public function getPriceversionId();

    /**
     * Set priceversion_id
     * @param string $priceversionId
     * @return \Codilar\Priceversion\Priceversion\Api\Data\PriceversionInterface
     */
    public function setPriceversionId($priceversionId);

    /**
     * Get version_code
     * @return string|null
     */
    public function getVersionCode();

    /**
     * Set version_code
     * @param string $versionCode
     * @return \Codilar\Priceversion\Priceversion\Api\Data\PriceversionInterface
     */
    public function setVersionCode($versionCode);

    /**
     * Get version_label
     * @return string|null
     */
    public function getVersionLabel();

    /**
     * Set version_label
     * @param string $versionLabel
     * @return \Codilar\Priceversion\Priceversion\Api\Data\PriceversionInterface
     */
    public function setVersionLabel($versionLabel);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Codilar\Priceversion\Priceversion\Api\Data\PriceversionInterface
     */
    public function setStatus($status);

    /**
     * Get launch_date
     * @return string|null
     */
    public function getLaunchDate();

    /**
     * Set launch_date
     * @param string $launchDate
     * @return \Codilar\Priceversion\Priceversion\Api\Data\PriceversionInterface
     */
    public function setLaunchDate($launchDate);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Codilar\Priceversion\Priceversion\Api\Data\PriceversionInterface
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
     * @return \Codilar\Priceversion\Priceversion\Api\Data\PriceversionInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get website
     * @return string|null
     */
    public function getWebsite();

    /**
     * Set website
     * @param string $website
     * @return \Codilar\Priceversion\Priceversion\Api\Data\PriceversionInterface
     */
    public function setWebsite($website);

    /**
     * Get sub_cat
     * @return string|null
     */
    public function getSubCat();

    /**
     * Set sub_cat
     * @param string $subCat
     * @return \Codilar\Priceversion\Priceversion\Api\Data\PriceversionInterface
     */
    public function setSubCat($subCat);

    /**
     * Get copy_from_version_id
     * @return string|null
     */
    public function getCopyFromVersionId();

    /**
     * Set copy_from_version_id
     * @param string $copyFromVersionId
     * @return \Codilar\Priceversion\Priceversion\Api\Data\PriceversionInterface
     */
    public function setCopyFromVersionId($copyFromVersionId);
}

