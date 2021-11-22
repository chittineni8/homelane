<?php
/**
 *
 * @package   Codilar\WishlistUrl
 * @author    Abhinav Vinayak <abhinav.v@codilar.com>
 * @copyright © 2021 Codilar
 * @license   See LICENSE file for license details.
 */

namespace Codilar\WishlistUrl\Plugin\Wishlist\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Http\Context;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class Data
{


    protected $logger;
    /**
     * Scope config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * HTTP Context
     * Customer session is not initialized yet
     *
     * @var Context
     */
    protected $context;

    const DISABLE_ADD_TO_CART = 'catalog/frontend/catalog_frontend_change_simple_product_url_to_config';

    /**
     * @var Configurable
     */
    private $configurable;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * SalablePlugin constructor.
     *
     * @param ScopeConfigInterface $scopeConfig ScopeConfigInterface
     * @param Configurable $configurable
     * @param ProductRepositoryInterface $productRepository
     * @param Context $context Context
     */
    public function __construct(
        LoggerInterface            $logger,
        ScopeConfigInterface       $scopeConfig,
        Configurable               $configurable,
        ProductRepositoryInterface $productRepository,
        Context                    $context
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->context = $context;
        $this->configurable = $configurable;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }


    /**
     * @param \Magento\Wishlist\Helper\Data $subject
     * @param $result
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetProductUrl(\Magento\Wishlist\Helper\Data $subject, $result, $item, $additional = [])
    {

        $scope = ScopeInterface::SCOPE_STORE;

        if ($this->scopeConfig->getValue(self::DISABLE_ADD_TO_CART, $scope)) {
            $productId = $item->getProductId();
            $parentProduct = $this->configurable->getParentIdsByChild($productId);
            if (isset($parentProduct[0])) {
                $parentId = $parentProduct[0];
                $configProductUrl = $this->productRepository->getById($parentId);
                $simpleProduct = $this->productRepository->getById($productId);
                $productUrl = $this->getHashUrl($configProductUrl, $simpleProduct);
//                $productUrl = $configProductUrl->getProductUrl();
                $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
                $logger = new \Zend_Log();
                $logger->addWriter($writer);
                $logger->info($productId);
                return $productUrl;
            }


        }


        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Product $parentProduct
     * @param \Magento\Catalog\Model\Product $simpleProduct
     * @return string Hashed Url
     */
    public function getHashUrl($parentProduct, $simpleProduct)
    {
        $configType = $parentProduct->getTypeInstance();
        $attributes = $configType->getConfigurableAttributesAsArray($parentProduct);
        $options = [];
        foreach ($attributes as $attribute) {
            $id = $attribute['attribute_id'];
            $value = $simpleProduct->getData($attribute['attribute_code']);
            $options[$id] = $value;
        }
        $options = http_build_query($options);
        return $parentProduct->getProductUrl() . ($options ? '#' . $options : '');
    }
}
