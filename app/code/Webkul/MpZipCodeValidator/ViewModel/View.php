<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpZipCodeValidator
 * @author    Webkul <support@webkul.com>
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\MpZipCodeValidator\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class View implements ArgumentInterface
{
    /**
     * @var \Webkul\MpAssignProduct\Helper\Data
     */
    protected $mpAssignHelperData;

    /**
     * @var \Webkul\Marketplace\Helper\DataFactory
     */
    protected $mpHelperDataFactory;

    /**
     * @var \Magento\Checkout\Helper\CartFactory
     */
    protected $cartHelperFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\DataFactory
     */
    protected $priceHelperDataFactory;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $catalogHelperImageFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param \Magento\Framework\ObjectManagerInterface     $objectManager
     * @param \Webkul\Marketplace\Helper\DataFactory        $mpHelperDataFactory
     * @param \Magento\Checkout\Helper\CartFactory          $cartHelperFactory
     * @param \Magento\Framework\Pricing\Helper\DataFactory $priceHelperDataFactory
     * @param \Magento\Catalog\Helper\ImageFactory          $catalogHelperImageFactory
     * @param \Magento\Framework\Json\Helper\Data           $jsonHelper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface     $objectManager,
        \Webkul\Marketplace\Helper\DataFactory        $mpHelperDataFactory,
        \Magento\Checkout\Helper\CartFactory          $cartHelperFactory,
        \Magento\Framework\Pricing\Helper\DataFactory $priceHelperDataFactory,
        \Magento\Catalog\Helper\ImageFactory          $catalogHelperImageFactory,
        \Magento\Framework\Json\Helper\Data           $jsonHelper
    ) {
        /**
         * This objectManager used for create the object of \Webkul\MpAssignProduct\Helper\Data class,
         *  because MpAssignProduct module is different module.
         */
        $this->mpAssignHelperData  = $objectManager->create(\Webkul\MpAssignProduct\Helper\Data::class);
        $this->mpHelperDataFactory = $mpHelperDataFactory;
        $this->cartHelperFactory = $cartHelperFactory;
        $this->priceHelperDataFactory = $priceHelperDataFactory;
        $this->catalogHelperImageFactory = $catalogHelperImageFactory;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Get assignproduct helper data object
     *
     * @return object
     */
    public function getMpAssignHelperData() : object
    {
        return $this->mpAssignHelperData;
    }
    
    /**
     * Get helper data object
     *
     * @return object
     */
    public function getMpHelperData() : object
    {
        return $this->mpHelperDataFactory->create();
    }

    /**
     * Get Cart helper object
     *
     * @return object
     */
    public function getCartHelper() : object
    {
        return $this->cartHelperFactory->create();
    }
    
    /**
     * Get price helper data object
     *
     * @return object
     */
    public function getPriceHelper() : object
    {
        return $this->priceHelperDataFactory->create();
    }

    /**
     * Get catalog helper image object
     *
     * @return object
     */
    public function getCatalogHelperImage() : object
    {
        return $this->catalogHelperImageFactory->create();
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
}
