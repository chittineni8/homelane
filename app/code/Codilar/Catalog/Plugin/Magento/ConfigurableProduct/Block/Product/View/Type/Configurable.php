<?php
namespace Codilar\Catalog\Plugin\Magento\ConfigurableProduct\Block\Product\View\Type;

class Configurable
{
    /**
     * https://magento.stackexchange.com/questions/281021/how-to-change-product-name-dynamically-in-configurable-product-when-click-swatch
     *
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject
     * @param $result
     * @return false|string
     */
    public function afterGetJsonConfig(\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject, $result)
    {
        $jsonResult = json_decode($result, true);
        foreach ($subject->getAllowProducts() as $simpleProduct) {
            $id = $simpleProduct->getId();
            foreach ($simpleProduct->getAttributes() as $attribute) {
                if (in_array($attribute->getAttributeCode(), ['name'])) { // <= Here you can put any attribute you want to see dynamic
                    $code = $attribute->getAttributeCode();
                    $value = (string)$attribute->getFrontend()->getValue($simpleProduct);
                    $jsonResult['dynamic'][$code][$id] = [
                        'value' => $value
                    ];
                }
            }
        }
        $result = json_encode($jsonResult);
        return $result;
    }
}
