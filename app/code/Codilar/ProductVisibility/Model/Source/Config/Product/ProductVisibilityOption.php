<?php
namespace Codilar\ProductVisibility\Model\Source\Config\Product;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;


class ProductVisibilityOption extends AbstractSource
{
    protected $optionFactory;
    public function getAllOptions()
    {
        $this->_options = [];
        $this->_options[] = ['label' => 'Homelane', 'value' => 'homelane'];
        $this->_options[] = ['label' => 'Homelane store', 'value' => 'homelane_store'];
        $this->_options[] = ['label' => 'Spacecraft', 'value' => 'spacecraft'];

        return $this->_options;
    }
}
