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
namespace Webkul\MpZipCodeValidator\Block\Product;

use Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory;

class AddRegion extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\MpZipCodeValidator\Model\RegionFactory
     */
    protected $_region;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CollectionFactory $regionCollectionFactory
     * @param \Magento\Catalog\Model\Product $product
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CollectionFactory $regionCollectionFactory,
        \Magento\Catalog\Model\Product $product,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_region = $regionCollectionFactory;
        $this->_product = $product;
        parent::__construct($context, $data);
    }

    /**
     * Get all regions
     *
     * @return Webkul\MpZipCodeValidator\Model\ResourceModel\Region\Collection
     */
    public function getAllregions()
    {
        $sellerId = $this->_customerSession->getCustomerId();
        $collection = $this->_region->create()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('seller_id', $sellerId);
        return $collection;
    }
    /**
     * Get main Product type by Id
     *
     * @return string
     */
    public function getProductType()
    {
        $productId = $this->getRequest()->getParam('id');
        return $this->_product->load($productId)->getTypeId();
    }
}
