<?php
/**
 * ZipCodeOptions.php
 *
 * @package     Homelane
 * @description overriding functionality of Webkul_MpZipCodeValidator module
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * overriding functionality of Webkul_MpZipCodeValidator module
 */
namespace Codilar\ExtendedMpZipCodeValidator\Ui\DataProvider\Product\Form\Modifier;

use Webkul\MpZipCodeValidator\Ui\DataProvider\Product\Form\Modifier\ZipCodeOptions as Data;

class ZipCodeOptions extends Data
{
    /**
     * Override parent class modifyMeta method
     *
     * @param array $meta
     * @return array $meta
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $productId = $this->locator->getProduct()->getId();
        $isAssociated = false;
        if (!empty($productId)) {
            $product = $this->configurableProductType->getParentIdsByChild($productId);
            if (!empty($product)) {
                $isAssociated = true;
            }
        }
        $productType = $this->locator->getProduct()->getTypeId();
        $allowedTypes = [
            "simple",
            "configurable",
            "bundle",
            "grouped"
        ];
        if ($this->getEnableDisable() && in_array($productType, $allowedTypes) && !($isAssociated)) {
            $this->createZipCodeValidationPanel();
            $this->createZipCodeRegionsPanel();
        }
        return $this->meta;
    }
}
