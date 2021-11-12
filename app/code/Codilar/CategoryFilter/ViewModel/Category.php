<?php

namespace Codilar\CategoryFilter\ViewModel;

use \Magento\Catalog\Model\CategoryFactory;
use \Magento\Catalog\Model\CategoryRepository;
use \Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
Class Category implements ArgumentInterface
{
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
     * @param Template\Context                                        $context
     * @param \Magento\Catalog\Model\CategoryFactory                  $_categoryFactory
     * @param \Magento\Catalog\Model\CategoryRepository;              $categoryRepository
     * @param \Magento\Store\Model\StoreManagerInterface;             $_storeManager
     * @param array                                                   $data
     */
    


    public function __construct(
                 \Magento\Framework\UrlInterface $url,
                 \Magento\Store\Model\StoreManagerInterface $storeManager,
                 \Magento\Catalog\Model\CategoryRepository $categoryRepository
                // \Magento\Catalog\Model\CategoryFactory $categoryFactory,
                // \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collecionFactory
            )
            {
                $this->url                       = $url;
                $this->_storeManager             = $storeManager;
                $this->categoryRepository        = $categoryRepository;
                // $this->_categoryFactory          = $categoryFactory;
                // $this->_collectionFactory        = $collecionFactory;
            }

     public function getCategoryUrl($categoryId)
     {
                $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
               return $category->getUrl();

      }
}      
