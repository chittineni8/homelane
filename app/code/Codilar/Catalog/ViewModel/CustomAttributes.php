<?php
/**
 * @package     homelane
 * @author      Codilar Technologies
 * @link        https://www.codilar.com/
 * @copyright © 2021 Codilar Technologies Pvt. Ltd.. All rights reserved.
 */

namespace Codilar\Catalog\ViewModel;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Codilar\Catalog\Registry\CurrentProduct;

class CustomAttributes implements ArgumentInterface
{

    /**
     * @var CurrentProduct
     */
    private CurrentProduct $currentProduct;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;


    /**
     * @param CurrentProduct $currentProduct
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        CurrentProduct $currentProduct,
        ProductRepositoryInterface $productRepository
    )
    {

        $this->currentProduct = $currentProduct;
        $this->productRepository = $productRepository;
    }


    public function getCurrentProductId(): ?int
    {
        return $this->currentProduct->get()->getId();
    }

    /**
     * @param $productId
     * @return int|mixed
     * @throws NoSuchEntityException
     */
    public function getProductGroup($productId) {

        $product = $this->productRepository->getById($productId);
        $productGroup = $product->getAttributeText('product_group');

        if (!empty($productGroup)) {
            return $productGroup;
        } else {
            return 0;
        }
    }

    /**
     * @param $productId
     * @return int|mixed
     * @throws NoSuchEntityException
     */
    public function getProductBrand($productId) {
        $product = $this->productRepository->getById($productId);
        $productBrand = $product->getAttributeText('brand');

        if (!empty($productBrand)) {
            return $productBrand;
        } else {
            return 0;
        }
    }

}
