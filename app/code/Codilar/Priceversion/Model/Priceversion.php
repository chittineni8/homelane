<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Model;

use Codilar\Priceversion\Api\Data\PriceversionInterface;
use Magento\Framework\Model\AbstractModel;

class Priceversion extends AbstractModel implements PriceversionInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Codilar\Priceversion\Model\ResourceModel\Priceversion::class);
    }

    /**
     * @inheritDoc
     */
    public function getPriceversionId()
    {
        return $this->getData(self::PRICEVERSION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPriceversionId($priceversionId)
    {
        return $this->setData(self::PRICEVERSION_ID, $priceversionId);
    }

    /**
     * @inheritDoc
     */
    public function getVersionCode()
    {
        return $this->getData(self::VERSION_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setVersionCode($versionCode)
    {
        return $this->setData(self::VERSION_CODE, $versionCode);
    }

    /**
     * @inheritDoc
     */
    public function getVersionLabel()
    {
        return $this->getData(self::VERSION_LABEL);
    }

    /**
     * @inheritDoc
     */
    public function setVersionLabel($versionLabel)
    {
        return $this->setData(self::VERSION_LABEL, $versionLabel);
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
    public function getLaunchDate()
    {
        return $this->getData(self::LAUNCH_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setLaunchDate($launchDate)
    {
        return $this->setData(self::LAUNCH_DATE, $launchDate);
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

    /**
     * @inheritDoc
     */
    public function getWebsite()
    {
        return $this->getData(self::WEBSITE);
    }

    /**
     * @inheritDoc
     */
    public function setWebsite($website)
    {
        return $this->setData(self::WEBSITE, $website);
    }

    /**
     * @inheritDoc
     */
    public function getSubCat()
    {
        return $this->getData(self::SUB_CAT);
    }

    /**
     * @inheritDoc
     */
    public function setSubCat($subCat)
    {
        return $this->setData(self::SUB_CAT, $subCat);
    }

    /**
     * @inheritDoc
     */
    public function getCopyFromVersionId()
    {
        return $this->getData(self::COPY_FROM_VERSION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCopyFromVersionId($copyFromVersionId)
    {
        return $this->setData(self::COPY_FROM_VERSION_ID, $copyFromVersionId);
    }
}

