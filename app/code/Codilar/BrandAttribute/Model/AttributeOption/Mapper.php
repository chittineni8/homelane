<?php

namespace Codilar\BrandAttribute\Model\AttributeOption;

use Codilar\BrandAttribute\Model\AttributeOption\AttributeValue;

/**
 * Class Mapper
 * @package Codilar\BrandAttribute\Model\AttributeOption
 */
class Mapper
{
    public $map;
    private $items;

    /**
     * @param \Codilar\UomAttribute\Model\AttributeOption\AttributeValue $attributes
     */

    private $attributes;


    public function __construct(
        \Codilar\BrandAttribute\Model\AttributeOption\AttributeValue $attributes
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
        foreach ($this->attributes->getBrandAttributeValue() as $attribute) {
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
