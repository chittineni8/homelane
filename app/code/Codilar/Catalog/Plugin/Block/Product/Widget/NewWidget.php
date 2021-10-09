<?php
declare(strict_types=1);

namespace Codilar\Catalog\Plugin\Block\Product\Widget;

use Magento\Catalog\Block\Product\Widget\NewWidget as WidgetData;
use Codilar\Catalog\ViewModel\Category;

class NewWidget
{
    private Category $viewModel;

    public function __construct(Category $viewModel) {
        $this->viewModel = $viewModel;
    }

    public function beforeToHtml(WidgetData $newWidget)
    {
        $newWidget->setData('viewModel', $this->viewModel);
        return [];
    }
}
