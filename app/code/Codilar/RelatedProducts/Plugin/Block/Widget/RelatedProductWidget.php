<?php
declare(strict_types=1);

namespace Codilar\RelatedProducts\Plugin\Block\Widget;

use Magento\Catalog\Block\Product\ProductList\Related as RelatedData;
use Codilar\RelatedProducts\ViewModel\RelatedProduct;

class RelatedProductWidget
{
    private RelatedProduct $viewModel;


    public function __construct(RelatedProduct $viewModel)
   {
        $this->viewModel = $viewModel;
    }

    public function beforeToHtml(RelatedData $newWidget)
    {
        $newWidget->setData('relatedproductviewmodel', $this->viewModel);
        return [];
    }
}
