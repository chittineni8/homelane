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
namespace Webkul\MpZipCodeValidator\Plugin;

use Webkul\MpAssignProduct\Helper\Data;
use Webkul\MpZipCodeValidator\Block\Product\ViewOnProduct;

class DataPlugin
{
    /**
     * @var ViewOnProduct
     */
    protected $_voproduct;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_resquest;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\AssignProductFactory
     */
    protected $_assignProductFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @param ViewOnProduct $voproduct
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Webkul\MpZipCodeValidator\Model\AssignProductFactory $assignProductFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        ViewOnProduct $voproduct,
        \Magento\Framework\App\RequestInterface $request,
        \Webkul\MpZipCodeValidator\Model\AssignProductFactory $assignProductFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->_voproduct = $voproduct;
        $this->_request = $request;
        $this->_assignProductFactory = $assignProductFactory;
        $this->_date = $date;
    }

    /**
     * After Get All Assigned Products
     *
     * @param Data $subject
     * @param array $result
     * @return void
     */
    public function afterGetAllAssignedProducts(Data $subject, $result)
    {
        $data = [];
        $text = __("Enter Delivery Zipcode");
        $postcode = $this->_voproduct->getCustomerZipcode();
        foreach ($result as $key => $value) {
            $value['zipform'] = '<div class="wk-zcv-zipbox">
                <div class="wk-zcv-zip">
                    <div class="wk-zcv-wrapper">
                        <div class="wk-zcv-zipcodeform-seller">
                            <form autocomplete="off">
                                <input 
                                    type="text" 
                                    name="zipcode" 
                                    placeholder="'.$text.'" 
                                    class="wk-zcv-zipform'.$value["seller_id"].'" 
                                    title="zipcode" 
                                    data-id="'.$value["assign_id"].'" 
                                    seller-data-id="'.$value["seller_id"].'" 
                                    value="'.$postcode.'" 
                                    autocomplete="off"/>
                                <div 
                                    id="wk-zcv-check'.$value["seller_id"].'" 
                                    data-pro-id="'.$value["assign_id"].'" 
                                    data-id="'.$value["seller_id"].'">
                                    <span>Check</span>
                                </div>
                            </form>
                            <div class="wk-zcv-zipcookie'.$value["seller_id"].'">
                                <ul id="wk-zcv-addr'.$value["seller_id"].'"></ul>
                                <ul id="wk-zcv-cookie'.$value["seller_id"].'"></ul>
                                <ul id="wk-zcv-login'.$value["seller_id"].'"></ul>
                            </div>
                        </div>
                        <div class="wk-zcv-loader'.$value["seller_id"].'"></div>
                    </div>
                    <div class="wk-zcv-ziperror'.$value["seller_id"].'" id="wk-zcv-error"></div>
                    <div class="wk-zcv-zipsuccess'.$value["seller_id"].'"></div>
                </div>
            </div>';
            $data[] = $value;
        }
        return $data;
    }

    /**
     * After Process Assign Product
     *
     * @param Data $subject
     * @param array $result
     * @return void
     */
    public function afterProcessAssignProduct(
        Data $subject,
        $result
    ) {
        if (isset($result['assign_id']) && $result['assign_id']) {
            $regionIds = 0;
            $id = 0;
            $assignId = $result['assign_id'];
            $productId = $result['product_id'];
            $data = $this->_request->getPostValue();
            if (isset($data['product']['available_region']) && !empty($data['product']['available_region'])) {
                $regionIds = implode(",", $data['product']['available_region']);
            }
            $collection = $this->_assignProductFactory
                        ->create()
                        ->getCollection()
                        ->addFieldToFilter('assign_id', $assignId);
            foreach ($collection as $value) {
                $id = $value->getId();
            }
            if (!$id) {
                $model = $this->_assignProductFactory->create();
                $setData['assign_id'] = $assignId;
                $setData['product_id'] = $productId;
                $setData['region_ids'] = $regionIds;
                $setData['created_at'] = $this->_date->gmtDate();
                $setData['updated_at'] = $this->_date->gmtDate();
                $model->setData($setData)->save();
            } else {
                $this->_assignProductFactory->create()
                    ->load($id)
                    ->setAssignId($assignId)
                    ->setProductId($productId)
                    ->setRegionIds($regionIds)
                    ->setUpdatedAt($this->_date->gmtDate())
                    ->save();
            }
        }
        return $result;
    }
}
