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

use Webkul\MpZipCodeValidator\Api\Data\ZipcodeInterface;

class Zipcode extends \Magento\Framework\Model\AbstractModel implements ZipcodeInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Test Record cache tag
     */
    const CACHE_TAG = 'mpzipcodevalidator_zipcode';

    /**
     * @var string
     */
    protected $_cacheTag = 'mpzipcodevalidator_zipcode';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mpzipcodevalidator_zipcode';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode::class
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
            return $this->noRouteGallery();
        }
        return parent::load($id, $field);
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
     * Get Region Id
     *
     * @return int|null
     */
    public function getRegionId()
    {
        return parent::getData(self::REGION_ID);
    }

    /**
     * Set Region Id
     *
     * @param int $regionId
     * @return \Webkul\MpZipCodeValidator\Api\Data\ZipcodeInterface
     */
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * Get Region Zipcode
     *
     * @return int|null
     */
    public function getRegionZipcode()
    {
        return parent::getData(self::REGION_ZIPCODE);
    }

    /**
     * Set Region Zipcode
     *
     * @param int $regionZipcode
     * @return \Webkul\MpZipCodeValidator\Api\Data\ZipcodeInterface
     */
    public function setRegionZipcode($regionZipcode)
    {
        return $this->setData(self::REGION_ZIPCODE, $regionZipcode);
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
     * @return \Webkul\MpZipCodeValidator\Api\Data\ZipcodeInterface
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
     * @return \Webkul\MpZipCodeValidator\Api\Data\ZipcodeInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Remove Item
     *
     * @param object $item
     */
    private function removeItem($item)
    {
        $item->delete();
    }
}
