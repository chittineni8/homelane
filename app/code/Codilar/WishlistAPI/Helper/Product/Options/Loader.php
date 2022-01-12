<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\WishlistAPI\Helper\Product\Options;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Api\Data\OptionInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Api\Data\OptionValueInterfaceFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Catalog\Model\ProductFactory;

/**
 * Class Loader
 * @package Custom\Module\Helper\Product\Options
 */
class Loader extends \Magento\ConfigurableProduct\Helper\Product\Options\Loader
{
    /**
     * @var OptionValueInterfaceFactory
     */
    private $optionValueFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

     /**
     * @var ProductRepository
     */
    protected $productFactory;

    /**
     * Loader constructor.
     * @param OptionValueInterfaceFactory $optionValueFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        OptionValueInterfaceFactory $optionValueFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ProductFactory $productFactory
    ) {
        $this->optionValueFactory = $optionValueFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->productFactory = $productFactory;
    }

    /**
     * @param ProductInterface $product
     * @return OptionInterface[]
     */
    public function load(ProductInterface $product): array
    {
        $options = [];
        /** @var Configurable $typeInstance */
        $typeInstance = $product->getTypeInstance();
        if ($typeInstance instanceof \Magento\Catalog\Model\Product\Type\Simple ||
            $typeInstance instanceof \Magento\Bundle\Model\Product\Type) {
            return $options;
        }

        $attributeCollection = $typeInstance->getConfigurableAttributeCollection($product);
        $this->extensionAttributesJoinProcessor->process($attributeCollection);
        foreach ($attributeCollection as $attribute) {
            $values = [];
            $attributeOptions = $attribute->getOptions();
            if (is_array($attributeOptions)) {
                foreach ($attributeOptions as $option) {
                    /** @var \Magento\ConfigurableProduct\Api\Data\OptionValueInterface $value */
                    $value = $this->optionValueFactory->create();
                    $value->setValueIndex($option['value_index']);
                    $values[] = $value;
                }
            }
            $attribute->setValues($values);
            $options[] = $attribute;
        }

        return $options;
    }


    public function getDiscountPercent($id){
        $product = $this->productFactory->create();
        $productPrice = $product->load($id)->getPrice();
        $productFinalPrice = $product->load($id)->getFinalPrice();

        if($productFinalPrice < $productPrice): 
         $_Percent = 100 - round(($productFinalPrice / $productPrice)*100); 
         return $_Percent . '%'; 
        
        else:
        return null;
       endif;


    }
}