<?php
declare(strict_types=1);

namespace Codilar\Category\Model;

use Codilar\Category\Api\GetProductByCategoryManagementInterface;
use Exception;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\ProductFactory;
use Codilar\MiscAPI\Logger\Logger;
use Magento\Framework\Serialize\SerializerInterface;


class GetProductByCategoryManagement implements GetProductByCategoryManagementInterface
{

    protected $_productCollectionFactory;
    protected $serializer;
    protected $categoryFactory;
    protected $logger;
    protected $_storeManager;


    /**
     * @param CategoryFactory $categoryFactory
     * @param RequestInterface $request
     * @param ProductFactory $factory
     * @param Logger $logger
     * @param SerializerInterface $serializer
     */
    public function __construct(
        CategoryFactory     $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Logger              $logger,
        SerializerInterface $serializer,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    )
    {
        $this->categoryFactory = $categoryFactory;
        $this->_storeManager = $storeManager;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->_productCollectionFactory = $productCollectionFactory;


    }


      /**
      * @api
      * @param string
      * @return string
      */
    public function getProductByCategory($id)
    {
      //echo "Test";die;
        try {


                $collection = $this->_productCollectionFactory->create();
                $collection->addAttributeToSelect('*');
                $collection->addCategoriesFilter(['eq' => $id]);
                //print_r($collection->getData());
                //die;

                $category = $this->categoryFactory->create()->load($id)->getProductCollection()->addAttributeToSelect('*');

                $data = [];
                $productPositions  = $this->categoryFactory->create()->load($id)->getProductsPosition();
                //print_r($productPositions);die;
                foreach ($collection as $product) {
                $data[] = array(
                                  'sku'=>$product->getSku(),
                                  'position'=> isset($productPositions[$product->getId()])?$productPositions[$product->getId()]:0,
                                  'category_id'=>$id,
                                );
            }
            return $data;
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage() . ' ' . ' Categories Product API EXCEPTION');
            return ($e->getMessage());
        }  } catch(\Exception $e) {
          
        }//end try
    }


}
