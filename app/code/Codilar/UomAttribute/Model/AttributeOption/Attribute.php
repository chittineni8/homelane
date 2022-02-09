<?php

namespace Codilar\UomAttribute\Model\AttributeOption;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Attribute implements \Magento\Framework\Option\ArrayInterface

{
    /** @var ScopeInterface */
    protected $scopeConfig;

    /** @var CollectionFactory */

    protected $collectionFactory;

    /** @var array */
    protected $items;

    /**
     * @param CollectionFactory $collectionFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CollectionFactory    $collectionFactory,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->collectionFactory = $collectionFactory;

    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if (is_null($this->items)) {
            $this->items = $this->getOptions();
        }
        return $this->items;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute[]|\Magento\Framework\DataObject[]
     */
    public function getAttributes()
    {
        $attributescode = $this->scopeConfig->getValue(
            'attribute_section/uomattribute/uomattributevalue',
            ScopeInterface::SCOPE_STORE
        );
        $collection = $this->collectionFactory->create();
        if (!empty($attributescode)) {
            $collection->addFieldToFilter('attribute_code', $attributescode);
        }

        return $collection->getItems();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $items = [];
        foreach ($this->getAttributes() as $attribute) {
            $items[] = [
                'label' => $attribute->getStoreLabel(), 'value' => $attribute->getName(),
            ];
        }
        return $items;

    }
}
