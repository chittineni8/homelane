<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Observer\Catalog;

class ProductSaveAfter implements \Magento\Framework\Event\ObserverInterface
{

      /**
      * @var \Codilar\Priceversion\Model\PriceversionDetailsFactory
      */
      protected $_priceverisondetailsFactory;

      protected $dataPersistor;

      /**
       * @param \Magento\Backend\App\Action\Context $context
       * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
       */
      public function __construct(
          \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
          \Codilar\Priceversion\Model\PriceversiondetailsFactory $priceverisondetailsFactory
      ) {
          $this->dataPersistor = $dataPersistor;
          $this->_priceverisondetailsFactory = $priceverisondetailsFactory;
        //  parent::__construct($context);
      }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
      $_product = $observer->getProduct();  // you will get product object
       $_sku=$_product->getSku(); // for sku
       $status=$_product->getStatus();
       print_r($_product->getSkuType());
       if($status != 1){
         $status = 0;
       }
      if(isset($status)){
        $versions = $this->_priceverisondetailsFactory->create()->getCollection()->getData();
        $details = array();
          foreach($versions as $version) {
                if($version['sku'] == $_sku){

                    $version['status'] = $status;
                  //  unset($version['priceversiondetails_id']);
                    $details[] = $version;
                }
          }

      }
      if(!empty($details)){
        $myModel = $this->_priceverisondetailsFactory->create();

        // Inserting data using for loop
        foreach ($details as $detail) {
          $myModel->addData($detail);
          $myModel->save();
          $myModel->unsetData(); // this line is necessary to save multiple records
        }
      }

      return $this;
    }
}
