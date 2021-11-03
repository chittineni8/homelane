<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codilar\Catalog\Observer\Catalog;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;

class ProductSaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var Configurable
     */
    private $configurable;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * ProductSaveBefore constructor.
     * @param Configurable $configurable
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Configurable $configurable,
        ProductRepositoryInterface $productRepository
    ) {
        $this->configurable = $configurable;
        $this->productRepository = $productRepository;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $productId = $observer->getProduct()->getId();
        $parentProduct = $this->configurable->getParentIdsByChild($productId);
        if (isset($parentProduct[0])) {
            $parentId = $parentProduct[0];
            $configProductUrl = $this->productRepository->getById($parentId);
            $simpleProduct = $this->productRepository->getById($productId);
            $productUrl = $this->getHashUrl($configProductUrl, $simpleProduct);
            $observer->getProduct()->setData('config_product_url', $productUrl);  // you will get product object
//                $productUrl = $configProductUrl->getProductUrl();
//            return $productUrl;
        }

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
        return $parentProduct->getUrlKey() . ($options ? '#' . $options : '');
    }
}
