<?php
declare(strict_types=1);

namespace Codilar\MiscAPI\Model;

use Codilar\MiscAPI\Api\GetPriceBySkuManagementInterface;
use Exception;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\ProductFactory;
use Codilar\MiscAPI\Logger\Logger;
use Magento\Framework\Serialize\SerializerInterface;


class GetPriceBySkuManagement implements GetPriceBySkuManagementInterface
{

    protected $productRepository;
    protected $serializer;
    protected $categoryFactory;
    protected $logger;
    protected $factory;
    protected $request;
    protected $_storeManager;


    /**
     * @param CategoryFactory $categoryFactory
     * @param RequestInterface $request
     * @param ProductFactory $factory
     * @param Logger $logger
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryFactory     $categoryFactory,
        RequestInterface    $request,
        ProductFactory      $factory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Logger              $logger,
        SerializerInterface $serializer
    )
    {
        $this->productRepository = $productRepository;
        $this->categoryFactory = $categoryFactory;
        $this->request = $request;
        $this->_productFactory = $factory;
        $this->_storeManager = $storeManager;
        $this->logger = $logger;
        $this->serializer = $serializer;

    }


      /**
      * @api
      * @param string
      * @return string
      */
    public function getPriceBySku($sku)
    {
        try {
           $storeManagerDataList = $this->_storeManager->getStores();
           $data = array();

           $product = $this->productRepository->get($sku);

           $price = array();
           foreach ($storeManagerDataList as $key => $value) {
                $item = $this->productRepository->get($sku, false, $key);
                $price[] = array(
                                    'store'=>$value['code'],
                                    'price'=>$item->getPrice(),
                                    'final_price'=>$item->getFinalPrice(),
                                    'minimal_price' => $item->getMinimalPrice(),
                                    'min_price' => $item->getMinPrice(),
                                    'max_price' => $item->getMaxPrice()
                                  );
           }
           $data = [
                    'product_id' => $product->getId(),
                    'sku' => $product->getSku(),
                    'price' => $price
           ];
           $response = ['result' => ['status' => 200, 'message' => 'Success', 'details' => $data]];
           return $response;
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage() . ' ' . ' City Level SKU API EXCEPTION');
            return ($e->getMessage());
        }//end try
    }


}
