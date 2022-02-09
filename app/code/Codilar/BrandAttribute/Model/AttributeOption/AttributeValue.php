<?php

namespace Codilar\BrandAttribute\Model\AttributeOption;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class AttributeValue implements \Magento\Framework\Option\ArrayInterface

{
    /** @var ScopeInterface */
    protected $scopeConfig;

    /** @var array */
    protected $items;

    protected $collectionFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CollectionFactory    $collectionFactory,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    public function getBrandAttributeValue()
    {
        $options = [];
        $attributescode = $this->scopeConfig->getValue(
            'attribute_section/uomattribute/brandattributevalue',
            ScopeInterface::SCOPE_STORE
        );
        $collection = $this->collectionFactory->create();
        if (!empty($attributescode)) {
            $collection->addFieldToFilter('attribute_code', $attributescode);
        }
        return $collection->getItems();
    }

    public function getOptions()
    {
        $items = [];
        foreach ($this->getBrandAttributeValue() as $attribute) {
            $items[] = [
                'label' => $attribute->getStoreLabel(), 'value' => $attribute->getName(),
            ];
        }
        return $items;
    }
    public function toOptionArray(): array
    {
        if (is_null($this->items)) {
            $this->items = $this->getOptions();
        }
        return $this->items;
    }

}
