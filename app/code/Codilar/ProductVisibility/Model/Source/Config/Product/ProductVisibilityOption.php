<?php
namespace Codilar\ProductVisibility\Model\Source\Config\Product;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;


class ProductVisibilityOption extends AbstractSource
{
    protected $optionFactory;
    public function getAllOptions()
    {
        $this->_options = [];
        $this->_options[] = ['label' => 'Homelane', 'value' => 'homeLane'];
        $this->_options[] = ['label' => 'Homelane store', 'value' => 'homeLane store'];
        $this->_options[] = ['label' => 'SpaceCraft', 'value' => 'spacecraft'];

        return $this->_options;
    }
}
