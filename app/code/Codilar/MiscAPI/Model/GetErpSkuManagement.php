<?php
declare(strict_types=1);

namespace Codilar\MiscAPI\Model;

use Codilar\MiscAPI\Api\GetErpSkuManagementInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tests\NamingConvention\true\mixed;
use Magento\Framework\Serialize\SerializerInterface;


class GetErpSkuManagement implements GetErpSkuManagementInterface
{
    protected $productRepository;
    protected $request;
    private $serializer;

    /**
     * @param ProductRepository $productRepository
     * @param RequestInterface $request
     * @param ProductFactory $factory
     */
    public function __construct(
        ProductRepository   $productRepository,
        RequestInterface    $request,
        ProductFactory      $factory,
        SerializerInterface $serializer)
    {
        $this->productRepository = $productRepository;
        $this->_productFactory = $factory;
        $this->request = $request;
        $this->serializer = $serializer;

    }


    /**
     * @param string $sku
     * @return mixed|void
     * @throws NoSuchEntityException
     */
    public function getErpSku()
    {

        $params = json_decode(file_get_contents("php://input"), true);

        foreach ($params as $value) {

            foreach ($value as $item) {
                $productCollection = $this->_productFactory->create()->getCollection()->addAttributeToSelect('*')
                    ->addFieldToFilter('sku', array('eq' => $item));

                foreach ($productCollection as $erp) {
                    $result[$item] = $erp->getData('erp_sku_id');

                }

            }
        }
        $serializeData = $this->serializer->serialize($result);

        print_r($serializeData, false);
    }
}

