<?php
declare(strict_types=1);

namespace Codilar\MiscAPI\Model;

use Codilar\MiscAPI\Api\GetSkuBySearchManagementInterface;
use Exception;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\ProductFactory;
use Codilar\MiscAPI\Logger\Logger;
use Magento\Framework\Serialize\SerializerInterface;


class GetSkuBySearchManagement implements GetSkuBySearchManagementInterface
{

    protected $serializer;
    protected $categoryFactory;
    protected $logger;
    protected $factory;
    protected $request;


    /**
     * @param CategoryFactory $categoryFactory
     * @param RequestInterface $request
     * @param ProductFactory $factory
     * @param Logger $logger
     * @param SerializerInterface $serializer
     */
    public function __construct(
        CategoryFactory     $categoryFactory,
        RequestInterface    $request,
        ProductFactory      $factory,
        Logger              $logger,
        SerializerInterface $serializer
    )
    {
        $this->categoryFactory = $categoryFactory;
        $this->request = $request;
        $this->_productFactory = $factory;
        $this->logger = $logger;
        $this->serializer = $serializer;

    }


    /**
     * @return mixed|string|void
     */
    public function getSkuBySearch()
    {
        try {
            $params = $this->request->getParams();
            if ($params):
                $id = $params['catId'];
                $text = $params['search'];
                $collection = $this->getCategory($id)->getProductCollection()->addAttributeToSelect('*');
                $details = [];
                foreach ($collection as $items) {
                    $name = $items->getName();
                    $sku = $items->getSku();
                    if (str_contains($sku, $text) || str_contains($name, $text)) {
                        $details[] = ['name' => $name, 'sku' => $sku, 'brand' => $items->getManufacturer(),
                            'final_price' => $items->getFinalPrice(), 'price' => $items->getPrice(),
                            'bom_type' => $items->getBomType(), 'type' => $items->getTypeId(),
                            'category' => $this->getCategoryByProductId($items->getId())


                        ];
                    }
                }
                if (!empty($details)):
                    $Response = ['status' => 200, 'message' => 'Success', 'results' => $details];
                    $serializeData = $this->serializer->serialize($Response);
                    print_r($serializeData);
                else:
                    $Response = ['status' => 400, 'message' => 'No Items Found'];
                    $serializeData = $this->serializer->serialize($Response);
                    print_r($serializeData);

                endif;
            endif;
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage() . ' ' . ' ERP SEARCH API EXCEPTION');
            return ($e->getMessage());
        }//end try
    }

    /**
     * @param $catId
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory($catId)
    {

        $category = $this->categoryFactory->create()->load($catId);
        return $category;
    }


    /**
     * @return CategoryFactory
     */
    public function getCategoryByProductId($pid)
    {
        $product = $this->_productFactory->create()->load($pid);
        $cats = $product->getCategoryIds();
        if (count($cats)) {
//            $firstCategoryId = $cats[0];
            foreach ($cats as $cat) {

                $_category = $this->categoryFactory->create()->load($cat);
            }
            return $_category->getName();
        }
    }
}

