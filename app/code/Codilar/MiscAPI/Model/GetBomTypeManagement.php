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
use Codilar\MiscAPI\Logger\Logger;

class GetBomTypeManagement implements \Codilar\MiscAPI\Api\GetBomTypeManagementInterface
{
    protected $productRepository;
    protected $request;
    protected $logger;
    private $serializer;

    /**
     * @param ProductRepository $productRepository
     * @param RequestInterface $request
     * @param ProductFactory $factory
     */
    public function __construct(
        ProductRepository   $productRepository,
        RequestInterface    $request,
        Logger              $logger,
        ProductFactory      $factory,
        SerializerInterface $serializer)
    {
        $this->productRepository = $productRepository;
        $this->_productFactory = $factory;
        $this->request = $request;
        $this->logger = $logger;
        $this->serializer = $serializer;

    }

    /**
     * {@inheritdoc}
     */
    public function getBomType()
    {
        try {
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
            if (!empty($result)):
                $serializeData = $this->serializer->serialize($result);
                print_r($serializeData, false);
            endif;
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage() . ' ' . ' BOM TYPE API EXCEPTION');
            return ($e->getMessage());
        }//end try
    }
}

