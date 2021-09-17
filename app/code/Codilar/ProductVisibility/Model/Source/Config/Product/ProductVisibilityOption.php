<?php
namespace Codilar\ProductVisibility\Model\Source\Config\Product;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;


class ProductVisibilityOption extends AbstractSource
{
    protected $optionFactory;
    public function getAllOptions()
    {
        $this->_options = [];
        $this->_options[] = ['label' => 'homelane', 'value' => ' 1'];
        $this->_options[] = ['label' => 'homelane store', 'value' => '2'];
        $this->_options[] = ['label' => 'spacecraft', 'value' => '3'];

        return $this->_options;
    }
}
