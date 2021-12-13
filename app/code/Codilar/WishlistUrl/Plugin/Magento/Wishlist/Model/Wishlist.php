<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codilar\WishlistUrl\Plugin\Magento\Wishlist\Model;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class Wishlist
{
    /**
     * @var Attribute
     */
    private $attribute;
    /**
     * @var Product
     */
    private $productModel;

    /**
     * Wishlist constructor.
     * @param Attribute $attribute
     * @param Product $productModel
     */
    public function __construct(
        Attribute $attribute,
        Product $productModel
    ) {
        $this->attribute = $attribute;
        $this->productModel = $productModel;
    }

    public function beforeAddNewItem(
        \Magento\Wishlist\Model\Wishlist $subject,
        $product,
        $buyRequest = null,
        $forciblySetQty = false
    ) {
        if (isset($buyRequest) && $buyRequest != null && !empty($buyRequest->getData('super_attribute'))) {
            $attributeCode = [];
            $attributeValue = [];
            foreach ($buyRequest->getData('super_attribute') as $key => $item) {
                $attributeCode[] = $this->attribute->load($key)->getAttributeCode();
                $attributeValue[] = $item;
            }
            $_children = $product->getTypeInstance()->getUsedProducts($product);
            foreach ($_children as $child) {
                $count = 0;
                foreach ($attributeCode as $key => $code) {
                    if ($child->getData($code) == $attributeValue[$key]) {
                        $count++;
                    }
                }
                if ($count == count($attributeCode)) {
                    $product = $this->productModel->load($child->getID());
                }
            }
        }
        return [$product, $buyRequest, $forciblySetQty];
    }
}
