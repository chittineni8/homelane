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

class CheckoutPage
{
    const DEFAULT_CONFIG = 2;
    const SPECIFIC_PRODUCT = 0;
    const ALL_REGIONS = 3;
    
    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequestFactory $rateRequestFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Catalog\Model\Product $product
     * @param \Webkul\MpZipCodeValidator\Model\AssignProduct $assignProduct
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcode
     * @param \Webkul\MpZipCodeValidator\Model\Region $region
     */
    public function __construct(
        \Magento\Quote\Model\Quote\Address\RateRequestFactory $rateRequestFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Webkul\MpZipCodeValidator\Model\AssignProduct $assignProduct,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcode,
        \Webkul\MpZipCodeValidator\Model\Region $region
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_rateRequestFactory = $rateRequestFactory;
        $this->_cart = $cart;
        $this->_productFactory = $productFactory;
        $this->_assignProduct = $assignProduct;
        $this->_zipcode = $zipcode;
        $this->_region = $region;
    }

    /**
     * Around Request ShippingRates
     *
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $requestItem
     * @return boolean
     */
    public function aroundRequestShippingRates(
        \Magento\Quote\Model\Quote\Address $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $requestItem = null
    ) {
        $moduleEnable = $this->scopeConfig->getValue(
            'mpzipcode/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $allowOnlyZipCodeAtCheckout = $this->scopeConfig->getValue(
            'mpzipcode/general/enablecheckout',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$moduleEnable) {
            return $proceed($requestItem);
        }
        if (!$allowOnlyZipCodeAtCheckout) {
            return $proceed($requestItem);
        }
        $flag = true;
        $address = $this->_cart->getQuote()->getShippingAddress();
        $zip = $address->getPostcode();
        $items = $this->_cart->getQuote()->getItemsCollection();

        foreach ($items as $item) {
            $productId = $item->getProductId();
            $product = $this->_region->loadProduct($this->_productFactory, $productId);
            $productZipCodeValidation = $product->getZipCodeValidation();
            if (!$product->getAvailableRegion()) {
                continue;
            }
            if ($productZipCodeValidation == self::SPECIFIC_PRODUCT && !empty($product->getAvailableRegion())) {
                $regionIds = explode(',', $product->getAvailableRegion());
                $collection = $this->_zipcode->create()
                ->addFieldToFilter(
                    'region_zipcode_from',
                    ['lteq' => $zip]
                )
                ->addFieldToFilter(
                    'region_zipcode_to',
                    ['gteq' => $zip]
                )
                ->addFieldToFilter(
                    'region_id',
                    ['in', $regionIds]
                );
                if (!$collection->getSize()) {
                    $flag = false;
                }
            } elseif ($productZipCodeValidation == self::DEFAULT_CONFIG) {
                $flag = true;
            } elseif ($productZipCodeValidation == self::ALL_REGIONS) {
                $flag = true;
            } elseif ($productZipCodeValidation == 0) {
                $flag = true;
            }
        }
        if ($flag) {
            return $proceed($requestItem);
        } else {
            return false;
        }
    }
}
