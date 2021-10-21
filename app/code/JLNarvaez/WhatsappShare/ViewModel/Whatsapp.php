<?php
/**
 * @author Jose Luis Narváez (jlnarvaez)
 */

namespace JLNarvaez\WhatsappShare\ViewModel;

use JLNarvaez\WhatsappShare\Registry\CurrentProduct;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Directory\Model\Currency;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Url\EncoderInterface;

/**
 * Class Whatsapp
 * @package JLNarvaez\WhatsappShare\ViewModel
 */
class Whatsapp implements ArgumentInterface
{
    /** @var ScopeConfigInterface */
    private $scopeConfig;
    /** @var CurrentProduct */
    private $currentProduct;
    /** @var EncoderInterface */
    private $encoder;
    /** @var Currency */
    private $currency;

    /**
     * Whatsapp constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param CurrentProduct $currentProduct
     * @param EncoderInterface $encoder
     * @param Currency $currency
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CurrentProduct $currentProduct,
        EncoderInterface $encoder,
        Currency $currency
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->currentProduct = $currentProduct;
        $this->encoder = $encoder;
        $this->currency = $currency;
    }

    /**
     * Get if module is enabled or not
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->getConfigValue('jlnarvaez_whatsappshare/params/enabled');
    }

    /**
     * Get if product name must be showed in Whatsapp Message
     * @return bool
     */
    public function showProductName()
    {
        return (bool) $this->getConfigValue('jlnarvaez_whatsappshare/params/show_product_name');
    }

    /**
     * Get if product description must be showed in Whatsapp Message
     * @return bool
     */
    public function showProductDesc()
    {
        return (bool) $this->getConfigValue('jlnarvaez_whatsappshare/params/show_product_desc');
    }

    /**
     * Get if product price must be showed in Whatsapp message
     * @return bool
     */
    public function showProductPrice()
    {
        return (bool) $this->getConfigValue('jlnarvaez_whatsappshare/params/show_product_price');
    }

    /**
     * Get Whatsapp message to send
     * @return string
     */
    public function getWhatsappMessage()
    {
        $strShare = '';
        $currentProduct = $this->getCurrentProduct();

        if ($this->showProductName()) {
            $strShare = $this->appendText($strShare, '*' . $currentProduct->getName() . '*');
        }

        if ($this->showProductDesc()) {
            $strShare = $this->appendText($strShare, strip_tags($currentProduct->getDescription()));
        }

        if ($this->showProductPrice()) {
            $strShare = $this->appendText(
                $strShare,
                number_format($currentProduct->getPrice(), 2) . ' ' .
                $this->currency->getCurrencySymbol());
        }

        $strShare = $this->appendText($strShare, $currentProduct->getProductUrl());

        return rawurlencode($strShare);
    }

    /**
     * Append text to string
     * @param string $str Original string
     * @param string $textToAppend String to append, to previous parameter
     * @return string
     */
    private function appendText($str, $textToAppend)
    {
        if ($str !== '') {
            $str .= "\n\n" . $textToAppend;
        } else {
            $str .= $textToAppend;
        }
        return $str;
    }

    /**
     * Get current product
     * @return ProductInterface
     */
    private function getCurrentProduct()
    {
        return $this->currentProduct->get();
    }

    /**
     * Get value in core_config_data
     * @param string $path Path in database
     * @return mixed
     */
    private function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }
}