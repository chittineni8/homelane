<?php

namespace Codilar\UomAttribute\Model\AttributeOption;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class AttributeValue implements \Magento\Framework\Option\ArrayInterface

{
    /** @var ScopeInterface */
    protected $scopeConfig;

    /** @var array */
    protected $items;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }


    public function getUomAttribute()
    {
        $options = [];
        $attributescode = $this->scopeConfig->getValue(
            'attribute_section/uomattribute/uomattributevalue',
            ScopeInterface::SCOPE_STORE
        );
        $options[] = ['label' => $attributescode,
            'value' => $attributescode];
        return $options;
    }

    public function toOptionArray(): array
    {
        if (is_null($this->items)) {
            $this->items = $this->getUomAttribute();
        }
        return $this->items;
    }

}
