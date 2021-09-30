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
namespace Webkul\MpZipCodeValidator\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class RegionOptions extends AbstractSource
{
    /**
     * Customer Session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Backend Session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendSession;

    /**
     * Region Collection
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_regionCollection;

    /**
     * @var \Webkul\MpZipCodeValidator\Block\Adminhtml\Product\Edit
     */
    protected $productEdit;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Webkul\MpZipCodeValidator\Block\Adminhtml\Product\Edit $productEdit
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Webkul\MpZipCodeValidator\Block\Adminhtml\Product\Edit $productEdit
    ) {
        $this->_customerSession = $customerSession;
        $this->_regionCollection = $regionCollectionFactory;
        $this->_backendSession = $authSession;
        $this->productEdit = $productEdit;
    }
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $sellerId = $this->productEdit->getProductSellerId();
        $collections = $this->_regionCollection->create()
                    ->addFieldToFilter('status', 1)
                    ->addFieldToFilter('seller_id', $sellerId);
        if ($this->_options == null) {
            foreach ($collections as $region) {
                $this->_options[] = [
                    'label' => __($region->getRegionName()),
                    'value' => $region->getId(),
                ];
            }
        }
        if ($this->_options == null && !($collections->getSize())) {
            $this->_options[] = [
                'label' => __('No region available'),
                'value' => 0,
            ];
        }
        return $this->_options;
    }
    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
