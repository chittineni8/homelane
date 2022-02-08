<?php

namespace Codilar\UomAttribute\Model\AttributeOption;

/**
 * Class Mapper
 * @package Codilar\UomAttribute\Model\AttributeOption
 */
class Mapper
{
    private $items;

    /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute[] */
    private $attributes;

    /**
     * @param \Codilar\UomAttribute\Model\AttributeOption\Attribute $attributes
     */
    public function __construct(
        \Codilar\UomAttribute\Model\AttributeOption\Attribute $attributes
    )
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    private function getAttributeValue()
    {
        $items = [];
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
