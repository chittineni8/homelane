<?php

namespace Codilar\CategoryFilter\Controller\Category;

use \Magento\Catalog\Model\CategoryFactory;
use \Magento\Catalog\Model\CategoryRepository;
use \Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\DataObject;

class Title extends Action
{
    protected $url;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository;
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface;
     */
    protected $_storeManager;

    protected $resultPageFactory;

    public function __construct(
                 \Magento\Framework\UrlInterface $url,
                 \Magento\Store\Model\StoreManagerInterface $storeManager,
                 \Magento\Catalog\Model\CategoryRepository $categoryRepository,
                \Magento\Catalog\Model\CategoryFactory $categoryFactory,
                \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collecionFactory,
                \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Context $context
    ) {

        $this->url                       = $url;
        $this->_storeManager             = $storeManager;
        $this->categoryRepository        = $categoryRepository;
        $this->_categoryFactory          = $categoryFactory;
        $this->_collectionFactory        = $collecionFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $categoryTitle = $params['name'];
        $collection = $this->_categoryFactory
                ->create()
                ->getCollection()
                ->addAttributeToFilter('name',$categoryTitle)
                ->setPageSize(1);
                        if ($collection->getSize()) {
                            $categoryId = $collection->getFirstItem()->getId();
                        }
                if (!empty($categoryId)){
                    $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
                    $url = $category->getUrl();
                    //print_r($url);
                    $redirect = $this->resultRedirectFactory->create();

                    $redirect->setUrl($url);
                }else{
                    $this->messageManager->addErrorMessage(__("Category Not Found"));

                    $redirect = $this->resultRedirectFactory->create();
                    $redirect->setUrl($this->_redirect->getRefererUrl());
                }
        return $redirect;
      }
    }
