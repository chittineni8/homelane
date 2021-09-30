<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpZipCodeValidator
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpZipCodeValidator\Plugin;

use \Webkul\Marketplace\Controller\Product\SaveProduct;

class BeforeSaveProduct
{
    /**
     * Around Save Product Data
     *
     * @param SaveProduct $subject
     * @param \Closure $proceed
     * @param int $sellerId
     * @param array $wholedata
     * @return void
     */
    public function aroundSaveProductData(SaveProduct $subject, \Closure $proceed, $sellerId, $wholedata)
    {
        if (!isset($wholedata['product']['available_region'])) {
            $wholedata['product']['available_region'] = [0];
        }
        return $result = $proceed($sellerId, $wholedata);
    }
}
