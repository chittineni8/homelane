<?php

namespace Codilar\NewWidget\Block\Product\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Registry;
use Codilar\Catalog\Registry\CurrentProduct;



class  WishlistPrice extends Template
{
    protected $_customerSession;

    protected $currentproduct;

    protected $productFactory;

    protected $registry;


    public function __construct(
        CurrentProduct              $currentProduct,
        \Magento\Framework\Registry $registry,
        ProductFactory              $productFactory
    )
    {

        $this->currentProduct   = $currentProduct;
        $this->registry         = $registry;
        $this->productFactory   = $productFactory;
    }

/**
 * @throws \Magento\Framework\Exception\NoSuchEntityException
 */
public
function getCurrentProductId(): ?int
{
    return $this->currentProduct->get()->getId();
}

public
function getCurrentProduct()
{
    $pro = $this->registry->registry('current_product');
    return $pro->getId();

}

public
function getDiscountPercents($id)
{
    $product = $this->productFactory->create()->load($id);
    $productPrice = $product->getPrice();
    $productFinalPrice = $product->getFinalPrice();
    if ($productFinalPrice < $productPrice):
        $_Percent = 100 - round(($productFinalPrice / $productPrice) * 100);
        return $_Percent;
    else:
        return null;
    endif;

}

public
function getCustomerId()
{
    $customer = $this->_customerSession->create();
    return $customer->getCustomer()->getId();
}

}
