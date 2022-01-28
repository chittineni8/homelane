<?php
/**
 * ConfigSkuUpdate.php
 *
 * @package     Homelane
 * @author      Abhinav Vinayak
 * @copyright   2022 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 */

namespace Codilar\ConfigurableSku\Cron;


use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Action;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Zend_Log;
use Zend_Log_Writer_Stream;

class ConfigSkuUpdate
{
    protected $productRepository;
    protected $logger;
    protected $product;
    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     * @param CollectionFactory $productCollectionFactory
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType
     * @param Configurable $configurable
     * @param Product $product
     * @param Action $action
     */
    public function __construct(
        Context                                                                    $context,
        StoreManagerInterface                                                      $storeManager,
        ProductRepositoryInterface                                                 $productRepository,
        LoggerInterface                                                            $logger,
        CollectionFactory                                                          $productCollectionFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType,
        Configurable                                                               $configurable,
        Product                                                                    $product,
        Action                                                                     $action)
    {
        $this->storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->configurable = $configurable;
        $this->action = $action;
        $this->logger = $logger;
        $this->configurableProductType = $configurableProductType;
        $this->productRepository = $productRepository;
        $this->product = $product;

    }

    /**
     * @return void
     */
    public function execute()
    {
        $writer = new Zend_Log_Writer_Stream(BP . '/var/log/cronn.log');
        $logger = new Zend_Log();
        $logger->addWriter($writer);
        $logger->info('eee');
        try {
            $collection = $this->_productCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('type_id', Type::TYPE_SIMPLE);

            foreach ($collection as $items) {

                $simpleId = $items->getId();
                $simpleConfigSku = $items->getConfigSku();

                $product = $this->configurableProductType->getParentIdsByChild($simpleId);
                if ($product) {
                    $parentId = $this->getParentProductId($simpleId);
                    $parentSku = $this->product->load($parentId)->getSku();
                    if ($simpleConfigSku && $simpleConfigSku != $parentSku) {
                        $items->setCustomAttribute('config_sku', $parentSku);
                        $items->save();
                    } elseif (empty($simpleConfigSku)) {
                        $items->setCustomAttribute('config_sku', $parentSku);
                        $items->save();
                    }
                }
            }
            return $items;
        } catch (Exception $e) {
            $this->loggerResponse->critical($e->getMessage());
        }//end try
    }


    /**
     * @param $childProductId
     * @return false|mixed
     */
    public function getParentProductId($childProductId)
    {
        $parentConfigObject = $this->configurable->getParentIdsByChild($childProductId);
        if ($parentConfigObject) {
            return $parentConfigObject[0];
        }
        return false;
    }
}
