<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpZipCodeValidator
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpZipCodeValidator\Model;

use Webkul\MpZipCodeValidator\Api\Data\RegionInterface;

class Region extends \Magento\Framework\Model\AbstractModel implements RegionInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Test Record cache tag
     */
    const CACHE_TAG = 'mpzipcodevalidator_region';

    /**
     * @var string
     */
    protected $_cacheTag = 'mpzipcodevalidator_region';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mpzipcodevalidator_region';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\MpZipCodeValidator\Model\ResourceModel\Region::class
        );
    }

    /**
     * Load object data
     *
     * @param int|null $id
     * @param string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteRegion();
        }
        return parent::load($id, $field);
    }
    
    /**
     * No route region
     *
     * @return object
     */
    public function noRouteRegion()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Webkul\MpZipCodeValidator\Api\Data\RegionInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get Seller Id
     *
     * @return int|null
     */
    public function getSellerId()
    {
        return parent::getData(self::SELLER_ID);
    }

    /**
     * Set Seller Id
     *
     * @param int $sellerId
     * @return \Webkul\MpZipCodeValidator\Api\Data\RegionInterface
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * Get Region Name
     *
     * @return int|null
     */
    public function getRegionName()
    {
        return parent::getData(self::REGION_NAME);
    }

    /**
     * Set Region Name
     *
     * @param int $regionName
     * @return \Webkul\MpZipCodeValidator\Api\Data\RegionInterface
     */
    public function setRegionName($regionName)
    {
        return $this->setData(self::REGION_NAME, $regionName);
    }

    /**
     * Get Status
     *
     * @return int|null
     */
    public function getStatus()
    {
        return parent::getData(self::STATUS);
    }

    /**
     * Set Status
     *
     * @param int $status
     * @return \Webkul\MpZipCodeValidator\Api\Data\RegionInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Created Time
     *
     * @return int|null
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * Set Created Time
     *
     * @param int $createdAt
     * @return \Webkul\MpZipCodeValidator\Api\Data\RegionInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get Updated Time
     *
     * @return int|null
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * Set Updated Time
     *
     * @param int $updatedAt
     * @return \Webkul\MpZipCodeValidator\Api\Data\RegionInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Load Product Data
     *
     * @param  object
     * @param  int
     * @return object
     */
    public function loadProduct($productFactory, $productId)
    {
        return $productFactory->create()->load($productId);
    }
}
