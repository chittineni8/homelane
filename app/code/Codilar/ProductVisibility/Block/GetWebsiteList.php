<?php 
namespace Codilar\ProductVisibility\Block; 
class GetWebsiteList extends \Magento\Framework\View\Element\Template 
{ 
    public function __construct( 
        \Magento\Framework\View\Element\Template\Context $context, 
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory, 
        array $data = [] ) 
    {
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
        parent::__construct($context, $data);
    }
 
     /**
     * Retrieve websites collection of system
     *
     * @return Website Collection
     */
    public function getWebsiteLists()
    {
        $collection = $this->_websiteCollectionFactory->create();
        return $collection;
    }
} 