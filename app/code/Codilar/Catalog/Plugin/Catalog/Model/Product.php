<?php
/**
 * IsSalable plugin
 *
 * @package   Codilar\Catalog
 * @author    Shahed Jamal <shahed@codilar.com>
 * @copyright © 2021 Codilar
 * @license   See LICENSE file for license details.
 */

namespace Codilar\Catalog\Plugin\Catalog\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Http\Context;
use Magento\Store\Model\ScopeInterface;

class Product
{
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
        ScopeConfigInterface $scopeConfig,
        Configurable $configurable,
        ProductRepositoryInterface $productRepository,
        Context $context
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->context = $context;
        $this->configurable = $configurable;
        $this->productRepository = $productRepository;
    }


    /**
     * Check if is disable add to cart
     *
     * @param \Magento\Catalog\Model\Product $subject
     * @param $result
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetProductUrl(\Magento\Catalog\Model\Product $subject, $result)
    {
        $scope = ScopeInterface::SCOPE_STORE;

        if ($this->scopeConfig->getValue(self::DISABLE_ADD_TO_CART, $scope)) {
            $productId = $subject->getId();
            $parentProduct = $this->configurable->getParentIdsByChild($productId);
            if (isset($parentProduct[0])) {
                $parentId = $parentProduct[0];
                $configProductUrl = $this->productRepository->getById($parentId);
                $simpleProduct = $this->productRepository->getById($productId);
                $productUrl = $this->getHashUrl($configProductUrl, $simpleProduct);
//                $productUrl = $configProductUrl->getProductUrl();
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
