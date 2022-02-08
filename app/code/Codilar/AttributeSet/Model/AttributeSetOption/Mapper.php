<?php

namespace Codilar\AttributeSet\Model\AttributeSetOption;

/**
 * Class Mapper
 * @package Codilar\AttributeSet\Model\AttributeSetOption
 */
class Mapper
{
    /** @var array */
    public $map;

    /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute[] */
    private $attributes;

    /**
     * @param \Codilar\AttributeSet\Model\AttributeSetOption\Attribute $attribues
     */
    public function __construct(
        \Codilar\AttributeSet\Model\AttributeSetOption\Attribute $attribues
    )
    {
        $this->attributes = $attribues;
        $this->map = $this->makeMap();
    }

    /**
     * @return array
     */
    private function makeMap()
    {
        if (is_null($this->map)) {
            $this->map = $this->buildMap();
        }

        return $this->map;
    }

    /**
     * @return array
     */
    private function buildMap()
    {
        $items = [];
        $options = [];
        $Options = $this->attributes->getOptions();
        foreach ($Options as $option) {

                $options[] = [
                    'label' => $option['id'],    //attributeSetId dropdown Label in form
                    'value' => $option['id']     //attributeSetId  value  in grid
                ];
                $values = (array_slice($options,count($options)-1));
                $items[$option['value']] = $values;
        }
        return $items;
    }
}
