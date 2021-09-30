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

use Webkul\MpZipCodeValidator\Api\Data\AssignProductInterface;

class AssignProduct extends \Magento\Framework\Model\AbstractModel implements AssignProductInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Test Record cache tag
     */
    const CACHE_TAG = 'mpzipcodevalidator_assignproduct';

    /**
     * @var string
     */
    protected $_cacheTag = 'mpzipcodevalidator_assignproduct';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mpzipcodevalidator_assignproduct';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\MpZipCodeValidator\Model\ResourceModel\AssignProduct::class
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
            return $this->noRouteAssignProduct();
        }
        return parent::load($id, $field);
    }

    /**
     * No route assignproduct
     *
     * @return object
     */
    public function noRouteAssignProduct()
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
     * @return \Webkul\MpZipCodeValidator\Api\Data\AssignProductInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get Assign Id
     *
     * @return int|null
     */
    public function getAssignId()
    {
        return parent::getData(self::ASSIGN_ID);
    }

    /**
     * Set Assign Id
     *
     * @param int $assignId
     * @return \Webkul\MpZipCodeValidator\Api\Data\AssignProductInterface
     */
    public function setAssignId($assignId)
    {
        return $this->setData(self::ASSIGN_ID, $assignId);
    }

    /**
     * Get Product Id
     *
     * @return int|null
     */
    public function getProductId()
    {
        return parent::getData(self::PRODUCT_ID);
    }

    /**
     * Set Product Id
     *
     * @param int $productId
     * @return \Webkul\MpZipCodeValidator\Api\Data\AssignProductInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get Region Ids
     *
     * @return int|null
     */
    public function getRegionIds()
    {
        return parent::getData(self::REGION_IDS);
    }

    /**
     * Set Region Ids
     *
     * @param int $regionIds
     * @return \Webkul\MpZipCodeValidator\Api\Data\AssignProductInterface
     */
    public function setRegionIds($regionIds)
    {
        return $this->setData(self::REGION_IDS, $regionIds);
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
     * @return \Webkul\MpZipCodeValidator\Api\Data\AssignProductInterface
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
     * @return \Webkul\MpZipCodeValidator\Api\Data\AssignProductInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
