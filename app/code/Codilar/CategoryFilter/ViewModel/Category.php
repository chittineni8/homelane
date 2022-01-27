<?php

namespace Codilar\CategoryFilter\ViewModel;

use \Magento\Catalog\Model\CategoryFactory;
use \Magento\Catalog\Model\CategoryRepository;
use \Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Model\ProductFactory;

class Category implements ArgumentInterface
{
    protected $productFactory;

    protected $url;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    // protected $_categoryFactory;

    // /**
    //  * @var \Magento\Catalog\Model\CategoryRepository;
    //  */
    protected $categoryRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface;
     */
    protected $_storeManager;

    /**
     * @param Template\Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $_categoryFactory
     * @param \Magento\Catalog\Model\CategoryRepository;              $categoryRepository
     * @param \Magento\Store\Model\StoreManagerInterface;             $_storeManager
     * @param array $data
     */


    public function __construct(
        ProductFactory                             $productFactory,
        \Magento\Framework\UrlInterface            $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryRepository  $categoryRepository
        // \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        // \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collecionFactory
    )
    {
        $this->productFactory = $productFactory;
        $this->url = $url;
        $this->_storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        // $this->_categoryFactory          = $categoryFactory;
        // $this->_collectionFactory        = $collecionFactory;
    }

    public function getCategoryUrl($categoryId)
    {
        if (empty($categoryId)) {

            $categoryId = 8;
        }
        $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());

        return $category->getUrl();

    }

    public function getDiscountPercents($id)
    {
        $product = $this->productFactory->create()->load($id);
        $productPrice = $product->getPrice();
        $productFinalPrice = $product->getFinalPrice();
        if ($productFinalPrice < $productPrice):
            $_Percent = 100 - round(($productFinalPrice / $productPrice) * 100);
            return $_Percent;
        else:
            return null;
        endif;

    }

}
