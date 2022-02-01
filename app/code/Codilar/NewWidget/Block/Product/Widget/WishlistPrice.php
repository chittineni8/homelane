<?php

namespace Codilar\NewWidget\Block\Product\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;


class  WishlistPrice extends Template
{
    protected $productFactory;

    protected $registry;


    public function __construct(
        Context                     $context,
        \Magento\Framework\Registry $registry,
        ProductFactory              $productFactory,
        array                       $data = []

    )
    {

        $this->registry = $registry;
        $this->productFactory = $productFactory;

    }


    public function getDiscountPercents($id)
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

}
