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

namespace Webkul\MpZipCodeValidator\Block\Adminhtml\Region\Edit\Tab;

class Products extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @param \Webkul\MpZipCodeValidator\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * @param \Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory
     */

    protected $mpProductCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Webkul\MpZipCodeValidator\Model\RegionFactory $regionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory $mpProductCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Webkul\MpZipCodeValidator\Model\RegionFactory $regionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory $mpProductCollectionFactory,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->regionFactory = $regionFactory;
        $this->mpProductCollectionFactory = $mpProductCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }
    
    /**
     * Prepare collection
     *
     * return $this
     */
    protected function _prepareCollection()
    {
        $regionId = $this->getRequest()->getParam('id');
        $collection = $this->productCollectionFactory->create();
        $sellerId = 0;
        if ($regionId) {
            $regionCollection = $this->regionFactory->create()->getCollection();
            $regionCollection->addFieldToFilter('id', ['eq' => $regionId]);
            $data = $regionCollection->getData();
            $sellerId = $data[0]['seller_id'];
        }
        if ($sellerId) {
            $mpCollection = $this->mpProductCollectionFactory->create()
                ->addFieldToFilter('seller_id', $sellerId);
            $sellerProductIds = $this->getSellerProductIds($mpCollection);
            $collection->addFieldToFilter('entity_id', ['in' => $sellerProductIds]);
        } else {
            $mpCollection =$this->mpProductCollectionFactory->create();
            $sellerProductIds = $this->getSellerProductIds($mpCollection);
            if ($sellerProductIds) {
                $collection->addFieldToFilter('entity_id', ['nin' => $sellerProductIds]);
            }
        }
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('price');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Get Seller Product Ids
     *
     * @param array $mpCollection
     * @return array $sellerProductIds
     */
    public function getSellerProductIds($mpCollection)
    {
        $sellerProductIds = [];
        foreach ($mpCollection as $mpModel) {
            $sellerProductIds[] = $mpModel->getMageproductId();
        }
        return $sellerProductIds;
    }

    /**
     * Prepare Grid Columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_product',
            [
                'type' => 'checkbox',
                'name' => 'in_product',
                'index' => 'entity_id',
                'values' => $this->_getSelectedProducts(),
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Product Name'),
                'index' => 'name',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Product Sku'),
                'index' => 'sku',
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Product Price'),
                'type' => 'currency',
                'index' => 'price',
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', ['_current' => true]);
    }

    /**
     * @return array|null
     */
    public function _getSelectedProducts()
    {
        $product = array_keys($this->getSelectedProducts());
        return $product;
    }

    /**
     * Retrieve selected products
     *
     * @return array
     */
    public function getSelectedProducts()
    {
        $regionId = $this->getRequest()->getParam('id');
        $product = [];
        $productIds = $this->getProductIds($regionId);
        foreach ($productIds as $productId) {
            $product[$productId] = $productId;
        }
        return $product;
    }

    /**
     * Get region assign product ids
     *
     * @param string $regionId
     * @return array
     */
    public function getProductIds($regionId)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter('available_region', ['finset' => $regionId]);
        $productIds = [];
        foreach ($collection as $model) {
            $productIds[] = $model->getId();
        }
        return $productIds;
    }

    /**
     * Get already selected product ids
     *
     * @return string
     */
    public function getSelectedProductIds()
    {
        $regionId = $this->getRequest()->getParam('id');
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter('available_region', ['finset' => $regionId]);
        $productIds = [];
        foreach ($collection as $model) {
            $productIds[] = $model->getId();
        }
        $productIds = implode(',', $productIds);
        return $productIds;
    }
}
