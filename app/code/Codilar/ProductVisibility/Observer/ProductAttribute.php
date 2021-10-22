<?php
namespace Codilar\ProductVisibility\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class ProductAttribute implements ObserverInterface
{ 
    
                              

    /**
     * For logger, var/log
     */
    protected $_logger;

   

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */

    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        
        $this->_logger = $logger;
       
    }
    /**
     * @param EventObserver $observer
     */

    public function execute(EventObserver  $observer)
    {  
        $inputarray=[];
        $product = $observer->getEvent()->getProduct();
         $visibility = $product->getProductVisibility();
            if (!empty($visibility)) {
            foreach($visibility as $value){

                 if($value=="homelane") {
                        $temp = 2;
                       
                 }
                 elseif($value=='homelane store'){
                    $temp = 3;
                
                }
                    elseif($value=='spacecraft'){
                        $temp = 4;
                    
                    }
                 array_push($inputarray,$temp);
            } 
           $product->setWebsiteIds($inputarray);
           //$product->setWebsiteIds(array(2));
            }
    } 
}
