<?php
namespace Codilar\ProductVisibility\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Codilar\ProductVisibility\Block\GetWebsiteList;

class ProductAttribute implements ObserverInterface
{ 
    
                              
    protected $getWebsiteList;
 
    /**
     * For logger, var/log
     */
    protected $_logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        GetWebsiteList  $getWebsiteList
    ) {
        
        $this->getWebsiteList = $getWebsiteList;
        $this->_logger = $logger;
       
    }
    /**
     * @param EventObserver $observer
     */

    public function execute(EventObserver  $observer)
    {  
        $inputarray=[];
        $allwebsiteids = [];
        $websites = $this->getWebsiteList->getWebsiteLists();
        foreach($websites as $website){
             $websiteid = $website->getId();
             array_push($allwebsiteids,$websiteid);
        }  
        $product = $observer->getEvent()->getProduct();
         $visibility = $product->getProductVisibility();
            if (!empty($visibility)) {
            foreach($visibility as $value){

                 if($value=="homelane") {
                    $temp = 2;
                       
                 }
                 elseif($value=='homelane_store'){
                    $temp = 3;
                
                }
                    elseif($value=='spacecraft'){
                        $temp = 4;
                    
                    }
                if (in_array($temp,$allwebsiteids)) {
                    array_push($inputarray,$temp);
                }    
            } 
           $product->setWebsiteIds($inputarray);
            }
    } 
}
