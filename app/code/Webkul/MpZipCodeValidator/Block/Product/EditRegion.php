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

class EditRegion extends \Magento\Framework\View\Element\Template
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
     * @var \Webkul\MpZipCodeValidator\Model\AssignProduct
     */
    protected $_assignProduct;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CollectionFactory $regionCollectionFactory
     * @param \Magento\Catalog\Model\Product $product
     * @param \Webkul\MpZipCodeValidator\Model\AssignProduct $assignProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CollectionFactory $regionCollectionFactory,
        \Magento\Catalog\Model\Product $product,
        \Webkul\MpZipCodeValidator\Model\AssignProduct $assignProduct,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_region = $regionCollectionFactory;
        $this->_product = $product;
        $this->_assignProduct = $assignProduct;
        parent::__construct($context, $data);
    }

    /**
     * GetProduct
     *
     * @param  int $id
     * @return object
     */
    public function getProduct($id)
    {
        return $this->_product->load($id);
    }

    /**
     * Get All seller's regions
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
     * Get Product Id from assign id
     *
     * @return Integer
     */
    public function getProductId()
    {
        return $this->_assignProduct
            ->getCollection()
            ->addFieldToFilter('assign_id', (int)$this->getRequest()->getParam('id'))
            ->setPageSize(1)
            ->getFirstItem();
    }
}
