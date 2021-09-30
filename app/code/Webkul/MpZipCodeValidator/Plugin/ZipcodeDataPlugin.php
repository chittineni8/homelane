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

use Webkul\MpAssignProduct\Block\Product\Products;
use Webkul\MpZipCodeValidator\Block\Product\ViewOnProduct;

class ZipcodeDataPlugin
{
    /**
     * @var ViewOnProduct
     */
    protected $_voproduct;

    /**
     * @param ViewOnProduct $voproduct
     */
    public function __construct(
        ViewOnProduct $voproduct
    ) {
        $this->_voproduct = $voproduct;
    }

    /**
     * AfterGetProductsArray
     *
     * @return string $result
     */
    public function afterGetProductsArray(Products $subject, $result)
    {
        $product = $this->_voproduct->getProduct();
        $productId = $product->getId();
        $zipCodeValidation = $product->getZipCodeValidation();
        $type = $product->getTypeId();
        $stock = $product->isSalable();
        $configValue = $this->_voproduct->getConfigApplyValue();
        $ifZipCodeConfiguredForProduct = $this->_voproduct->ifZipCodeConfiguredForProduct();

        if ($type != 'virtual' && $type != 'downloadable'
        && $type != 'etickets' && $type != 'booking'
        && $type != 'giftcard' && $stock && $ifZipCodeConfiguredForProduct
        && $zipCodeValidation != '1'
        ) {

            $finalValue = $result['headings'];
            $zipcodeArray = ['Zip Code'];
            $finalValue = array_merge($finalValue, $zipcodeArray);
            $result['headings'] = $finalValue;

            if (isset($result['data'])) {
                foreach ($result['data'] as $key => $value) {
                    $zipHtml = '<div class="wk-zcv-zipbox">';
                    $zipHtml .= '<div class="wk-zcv-zip">';
                    $zipHtml .= '<div class="wk-zcv-wrapper">';
                    $zipHtml .= '<div class="wk-zcv-zipcodeform">';
                    $zipHtml .= '<form autocomplete="off">';
                    $zipHtml .= '<input type="text" name="zipcode" 
                    placeholder="Enter Delivery Zipcode" {wk-ap-zipform} 
                    style="width: 140%!important;" title="zipcode" data-id="0" 
                    seller-data-id="0" autocomplete="off"/>';
                    $zipHtml .= '<div id="wk-ap-check0" {data-pro-id} 
                    style="position: unset!important;color: #1979c3; 
                    cursor:pointer;" {data-id}><span>Check</span></div>';
                    $zipHtml .= '</form>';
                    $zipHtml .= '<div class="wk-zcv-zipcookie0">';
                    $zipHtml .= '<ul id="wk-zcv-addr0"></ul>';
                    $zipHtml .= '<ul id="wk-zcv-cookie0"></ul>';
                    $zipHtml .= '<ul id="wk-zcv-login0"></ul>';
                    $zipHtml .= '</div>';
                    $zipHtml .= '</div>';
                    $zipHtml .= '<div class="wk-zcv-loader0"></div>';
                    $zipHtml .= '</div>';
                    $zipHtml .= '<div {wk-ap-ziperror} id="wk-zcv-error"
                    style="color: #ff0000; font-size: 14px"></div>';
                    $zipHtml .= '<div {wk-ap-zipsuccess} 
                    style="color: #008000; font-size:14px"></div>';
                    $zipHtml .= '</div>';
                    $zipHtml .= '</div>';

                    $zipHtml = str_replace('{data-pro-id}', 'data-pro-id='.$value['assignId'], $zipHtml);
                    $zipHtml = str_replace('{data-id}', 'data-id='.$value['sellerId'], $zipHtml);
                    $zipHtml = str_replace('{wk-ap-zipform}', 'class=wk-ap-zipform'.$value['sellerId'], $zipHtml);
                    $zipHtml = str_replace('{wk-ap-ziperror}', 'class=wk-ap-ziperror'.$value['sellerId'], $zipHtml);
                    $zipHtml = str_replace('{wk-ap-zipsuccess}', 'class=wk-ap-zipsuccess'.$value['sellerId'], $zipHtml);
                    $result['data'][$key]['additionalColumnInfo'] = $zipHtml;
                    $zipHtml = '';
                }
            }
            return $result;
        } else {
            return $result;
        }
    }
}
