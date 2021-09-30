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
namespace Webkul\MpZipCodeValidator\Block\Adminhtml\Product;

class Edit extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Webkul\Marketplace\Model\ProductFactory
     */
    protected $mpProductFactory;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context,
     * @param \Magento\Framework\App\RequestInterface $request,
     * @param \Magento\Catalog\Model\ProductFactory $productFactory,
     * @param \Webkul\Marketplace\Model\ProductFactory $mpProductFactory,
     * @param array $data = []
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Webkul\Marketplace\Model\ProductFactory $mpProductFactory,
        array $data = []
    ) {
        $this->request = $request;
        $this->productFactory = $productFactory;
        $this->mpProductFactory = $mpProductFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get Current Product Seller Id
     *
     * @return string $sellerId
     */
    public function getProductSellerId()
    {
        $id = $this->request->getParam('id');
        $sellerId = 0;
        if ($id) {
            $collection = $this->mpProductFactory->create()->getCollection();
            $collection->addFieldToFilter('mageproduct_id', ['eq' => $id]);
            foreach ($collection as $model) {
                $sellerId =$model->getSellerId();
            }
        }
        return $sellerId;
    }
}
