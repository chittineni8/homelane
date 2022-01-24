<?php

namespace Codilar\RelatedProducts\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Model\ProductFactory;


Class RelatedProduct implements ArgumentInterface
{

    protected $productFactory;


    public function __construct(ProductFactory $productFactory)
            {
                $this->productFactory  = $productFactory;
            }

    public function getRelatedProductDiscountPercents($id){
        $product = $this->productFactory->create();
        $productPrice = $product->load($id)->getPrice();
        $productFinalPrice = $product->load($id)->getFinalPrice();

        if($productFinalPrice < $productPrice):
            $_Percent = 100 - round(($productFinalPrice / $productPrice)*100);
            return $_Percent . '%';

        else:
            return null;
        endif;


    }
}
