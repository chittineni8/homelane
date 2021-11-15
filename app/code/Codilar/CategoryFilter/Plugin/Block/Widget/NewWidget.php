<?php
declare(strict_types=1);

namespace Codilar\CategoryFilter\Plugin\Block\Widget;

use Magento\Catalog\Block\Product\Widget\NewWidget as WidgetData;
use Codilar\CategoryFilter\ViewModel\Category;

class NewWidget
{
    private Category $viewModel;

    public function __construct(Category $viewModel)
   {
        $this->viewModel = $viewModel;
    }

    public function beforeToHtml(WidgetData $newWidget)
    {
        $newWidget->setData('categoryviewmodel', $this->viewModel);
        return [];
    }
}
