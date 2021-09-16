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

use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Http\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;

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

    const DISABLE_ADD_TO_CART = 'catalog/frontend/catalog_frontend_disable_add_to_cart';

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
        $productId = $subject->getId();
        $parentProduct = $this->configurable->getParentIdsByChild($productId);
        if (isset($parentProduct[0])) {
            $parentId = $parentProduct[0];
            $productUrl = $this->productRepository->getById($parentId)
                ->getProductUrl();
            return $productUrl;
        }
        return $result;
    }
}
