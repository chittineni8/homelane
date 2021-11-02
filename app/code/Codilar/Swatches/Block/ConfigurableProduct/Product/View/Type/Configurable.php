<?php

namespace Codilar\Swatches\Block\ConfigurableProduct\Product\View\Type;

use Closure;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as ProductConfigurable;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;

class Configurable
{
    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var Json
     */
    protected Json $json;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Json $json
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Json $json
    ) {
        $this->productRepository = $productRepository;
        $this->json = $json;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getProductById($id): ProductInterface
    {
        return $this->productRepository->getById($id);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function aroundGetJsonConfig(ProductConfigurable $subject, Closure $proceed) {
        $sname = [];
        $config = $proceed();
        $config = $this->json->unserialize($config);

        foreach ($subject->getAllowProducts() as $product) {
            $id = $product->getId();
            $productDetail = $this->getProductById($id);
            $name[$id] = $productDetail->getName();
        }
        $config['sname'] = $sname;

        return $this->json->serialize($config);
    }
}
