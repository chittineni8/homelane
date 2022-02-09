<?php

namespace Codilar\UomAttribute\Model\AttributeOption;

/**
 * Class Mapper
 * @package Codilar\UomAttribute\Model\AttributeOption
 */
class Mapper
{
    public  $map;
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
        $this->map = $this->makeMap();
    }
    private function makeMap()
    {
        if (is_null($this->map)) {
            $this->map = $this->getAttributeValue();
        }

        return $this->map;
    }
    /**
     * @return array
     */
    private function getAttributeValue(): array
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
            $items[$attribute->getName()] = $options;
        }
        return $items;

    }

    public function toOptionArray(): array
    {
        if (is_null($this->items)) {
            $this->items = $this->getAttributeValue();
        }
        return $this->items;
    }
}
