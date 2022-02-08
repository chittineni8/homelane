<?php

namespace Codilar\BrandAttribute\Model\AttributeOption;

use Codilar\BrandAttribute\Model\AttributeOption\Attribute;

/**
 * Class Mapper
 * @package Codilar\BrandAttribute\Model\AttributeOption
 */
class Mapper
{
    private $items;

    /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute[] */

    private $attributes;


    public function __construct(
        \Codilar\BrandAttribute\Model\AttributeOption\Attribute $attribues
    )
    {
        $this->attributes = $attribues;
    }

    /**
     * @return array
     */
    private function getAttributeValue()
    {
        $options = [];
        foreach ($this->attributes->getAttributes() as $attribute) {

            foreach ($attribute->getOptions() as $option) {
                if (empty($option->getValue())) {
                    continue;
                }
                $options[] = [
                    'label' => $option->getLabel(), 'value' => $option->getValue()
                ];
            }

        }
        return $options;

    }

    public function toOptionArray()
    {
        if (is_null($this->items)) {
            $this->items = $this->getAttributeValue();
        }
        return $this->items;
    }
}
