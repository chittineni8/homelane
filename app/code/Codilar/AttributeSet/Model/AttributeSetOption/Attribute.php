<?php

namespace Codilar\AttributeSet\Model\AttributeSetOption;

use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class AttributeOptions
 * @package Codilar\AttributeSet\Model\AttributeSetOption
 */
class Attribute implements \Magento\Framework\Option\ArrayInterface
{


    /** @var array */
    private $items;
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;
    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;


    public function __construct(
        AttributeSetRepositoryInterface $attributeSetRepository,
        AttributeSetFactory             $attributeSetFactory,
        EavSetupFactory                 $eavSetupFactory
    )
    {
        $this->attributeSetRepository = $attributeSetRepository;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->eavSetupFactory = $eavSetupFactory;
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
    public function getOptions()
    {
        $options = [['label' => 'Default', 'value' => 'Default', 'id' => '4']];
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->getEntityTypeId('catalog_product');
        $attributeSetIds = $eavSetup->getAllAttributeSetIds();
        foreach ($attributeSetIds as $attributeSetId) {

            $attributeSet = $this->attributeSetRepository->get($attributeSetId)->getAttributeSetName();
            if ($attributeSet !== 'Default') {
                $options[] = [
                    'label' => $attributeSet,
                    'value' => $attributeSet,
                    'id' => $attributeSetId
                ];
            }
        }
        return $options;
    }


}
