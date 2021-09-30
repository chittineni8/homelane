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
namespace Webkul\MpZipCodeValidator\Block\Product;

use \Magento\Store\Model\ScopeInterface;

class ViewOnProduct extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var Magento\Customer\Model\Address
     */
    protected $_address;

    /**
     * @var Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Address $address
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Address $address,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_address = $address;
        $this->scopeConfig = $scopeConfig;
        $this->_product = $product;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get Customer Zipcode
     *
     * @return string
     */
    public function getCustomerZipcode()
    {
        if ($this->_customerSession->getCustomerId()) {
            $customerAddressId = $this->_customerSession->getCustomer()->getDefaultShipping();
            $postcode = $this->_address->load($customerAddressId)->getPostcode();
            return $postcode;
        }
        return '';
    }

    /**
     * Get Product
     *
     * @return Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        $id = $this->getRequest()->getParam('id');
        return $this->_product->load($id);
    }

    /**
     * Check if zip code is configured for product
     *
     * @return bool
     **/
    public function ifZipCodeConfiguredForProduct()
    {
        if ($this->enabledForAll()) {
            return true;
        }
        $product = $this->getProduct();
        $type = $product->getZipCodeValidation();
        $config = $this->getConfigApplyValue();
        if ($type && !$config) {
            return false;
        }
        if ($product->getAvailableRegion()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Zip Code Field Message
     *
     * @return string
     */
    public function getZipcodeMessage()
    {
        return $this->scopeConfig->getValue(
            'mpzipcode/general/zipcodemessage',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Check if zip code is enabled for all
     *
     * @return bool
     **/
    public function enabledForAll()
    {
        return $this->scopeConfig->getValue(
            'mpzipcode/general/applyto',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get json helper object
     *
     * @return \Magento\Framework\Json\Helper\Data
     */
    public function getJsonHelper() : \Magento\Framework\Json\Helper\Data
    {
        return $this->jsonHelper;
    }

    /**
     * Get Zip Code Validation Value
     *
     * @param int $productId
     * @return int
     */
    public function getZipCodeValidationValue($productId)
    {
        $zipCodeValidation = $this->_product->load($productId)->getZipCodeValidation();
        return $zipCodeValidation;
    }

    /**
     * Get Configuration Apply Value
     *
     * @return boolean
     */
    public function getConfigApplyValue()
    {
        return $this->_scopeConfig->getValue(
            'mpzipcode/general/applyto',
            ScopeInterface::SCOPE_STORE
        );
    }
}
