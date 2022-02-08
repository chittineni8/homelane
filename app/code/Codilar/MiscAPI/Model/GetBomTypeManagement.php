<?php
declare(strict_types=1);

namespace Codilar\MiscAPI\Model;

use Codilar\MiscAPI\Api\GetBomTypeManagementInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tests\NamingConvention\true\mixed;
use Magento\Framework\Serialize\SerializerInterface;

class GetBomTypeManagement implements \Codilar\MiscAPI\Api\GetBomTypeManagementInterface
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
     * {@inheritdoc}
     */
    public function getBomType()
    {
        $params = json_decode(file_get_contents("php://input"), true);

        foreach ($params as $value) {

            foreach ($value as $item) {
                $productCollection = $this->_productFactory->create()->getCollection()->addAttributeToSelect('*')
                    ->addFieldToFilter('sku', array('eq' => $item));

                foreach ($productCollection as $erp) {
                    $result[$item] = $erp->getData('bom_type');
                }

            }
        }

        $serializeData = $this->serializer->serialize($result);

        print_r($serializeData, false);
    }
}

