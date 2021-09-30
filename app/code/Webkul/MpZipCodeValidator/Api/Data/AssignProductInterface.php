<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpZipCodeValidator
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpZipCodeValidator\Api\Data;

/**
 * Marketplace Zipcode Validator Region interface.
 * @api
 */
interface AssignProductInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID           = 'id';
    /**#@-*/

    const ASSIGN_ID    = 'assign_id';

    const REGION_IDS   = 'region_ids';
    
    const PRODUCT_ID   = 'product_id';

    const CREATED_AT   = 'created_at';
    
    const UPDATED_AT   = 'updated_at';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\MpZipCodeValidator\Api\Data\AssignProductInterface
     */
    public function setId($id);

    /**
     * Get Assign Id
     *
     * @return int|null
     */
    public function getAssignId();

    /**
     * Set Assign Id
     *
     * @param int $assignId
     * @return \Webkul\MpZipCodeValidator\Api\Data\AssignProductInterface
     */
    public function setAssignId($assignId);

    /**
     * Get Product Id
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * Set Product Id
     *
     * @param int $productId
     * @return \Webkul\MpZipCodeValidator\Api\Data\AssignProductInterface
     */
    public function setProductId($productId);

    /**
     * Get Region Ids
     *
     * @return int|null
     */
    public function getRegionIds();

    /**
     * Set Region Ids
     *
     * @param int $regionIds
     * @return \Webkul\MpZipCodeValidator\Api\Data\AssignProductInterface
     */
    public function setRegionIds($regionIds);

    /**
     * Get Created Time
     *
     * @return int|null
     */
    public function getCreatedAt();

    /**
     * Set Created Time
     *
     * @param int $createdAt
     * @return \Webkul\MpZipCodeValidator\Api\Data\AssignProductInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get Updated Time
     *
     * @return int|null
     */
    public function getUpdatedAt();

    /**
     * Set Updated Time
     *
     * @param int $updatedAt
     * @return \Webkul\MpZipCodeValidator\Api\Data\AssignProductInterface
     */
    public function setUpdatedAt($updatedAt);
}
