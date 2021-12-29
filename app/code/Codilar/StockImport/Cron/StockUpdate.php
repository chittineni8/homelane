<?php

namespace Codilar\StockImport\Cron;

class StockUpdate
{

	protected $productRepository;
	protected $csv;
	protected $logger;
	protected $product;

    public function __construct(
     \Magento\Framework\App\Action\Context $context,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
    \Psr\Log\LoggerInterface $logger,
    \Magento\Framework\File\Csv $csv,
    \Magento\Catalog\Model\Product $product,
    \Magento\Catalog\Model\Product\Action $action)
    {
    	 $this->storeManager = $storeManager;
    	  $this->action = $action;
    	  $this->logger = $logger;
    	  $this->csv = $csv;
    	  $this->productRepository = $productRepository;
    	  $this->product = $product;
       
    }

    public function execute()
	{
		try{

		$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/cron.log');
		$logger = new \Zend_Log();
		$logger->addWriter($writer);
		$logger->info(__METHOD__);
		 
            /* Some logic that could throw an Exception */
            $file = '/var/www/html/var/var_import/stock/';
          	$DestinationRoot  = '/var/www/html/var/var_import/stock/processed';
          	
           	if(!is_dir($DestinationRoot))
           	{
              mkdir($DestinationRoot,0777,true);
            }
			// get current directory path
			// set file pattern
			$file .= "*.csv";
			// copy filenames to array
			$files = array();
			$files = glob($file);
			// sort files by last modified date
			usort($files, function($x, $y) {
			    return filemtime($x) > filemtime($y);
			});
			foreach($files as $item){
			$i = 0;
			$csv = $this->csv->getData($item);
			foreach ($csv as $row => $data ) 
		   	{ 

            if($this->product->getIdBySku($data[0])) 
		   	{
	          //Load product by SKU
         	  $product = $this->productRepository->get($data[0]); 
         	  $id=$product->getId();
         	 $storeCode=$data[1];
        
              //Need to update data
         	  $websiteId = $this->storeManager->getWebsite()->getId();
              $store = $this->storeManager->getStore(); 
              $storeId = $this->getStoreIdByCode($storeCode);
              $zipvalidate = !empty($data)?$data[3]:"null";
              $this->action->updateAttributes([$id],['available_region' => $data[2], 'zip_code_validation' => $zipvalidate],$storeId);
              }
       		  $i++;
       		if(sizeof($csv)<=$i){
                  $file1 = basename($item); 
                  $DestinationFile = $DestinationRoot."/".$file1; // Create the destination filename 
                  rename($item, $DestinationFile); // rename the file     
       
      				}
     			}
   			}
 		
				return $this;
             } catch (Exception $e) {
            $this->loggerResponse->critical($e->getMessage());
        }//end try
	}



public function getStoreIdByCode($code)
    {
        try {
            $storeData = $this->storeManager->getStore($code);
            $storeCode = (string)$storeData->getId();
        } catch (LocalizedException $localizedException) {
            $storeCode = null;
            $this->logger->error($localizedException->getMessage());
        }
        return $storeCode;
    }
}