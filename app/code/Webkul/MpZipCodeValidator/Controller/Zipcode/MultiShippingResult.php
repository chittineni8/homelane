<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpZipCodeValidator
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpZipCodeValidator\Controller\Zipcode;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Controller\ResultFactory;

class MultiShippingresult extends Action
{
    /**
     * @var Constant variables
     */
    const NO_VALIDATION = 1;
    const DEFAULT_CONFIG = 2;
    const SPECIFIC_PRODUCT = 0;
    const ALL_REGIONS = 3;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Webkul\Marketplace\Model\ProductFactory
     */
    protected $mpProductFactory;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory
     */
    protected $regionCollection;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $zipcodeCollection;

    /**
     * @var \Webkul\MpZipCodeValidator\Helper\Data
     */
    protected $zipHelper;

    /**
     * @var \Magento\Multishipping\Block\Checkout\Shipping
     */
    protected $multiAddress;

    /**
     * @param Context $context,
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
     * @param \Magento\Catalog\Model\ProductFactory $productFactory,
     * @param \Webkul\Marketplace\Model\ProductFactory $mpProductFactory,
     * @param \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory $regionCollection,
     * @param \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection
     * @param \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
     * @param \Magento\Multishipping\Block\Checkout\Shipping $multiAddress
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Webkul\Marketplace\Model\ProductFactory $mpProductFactory,
        \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory $regionCollection,
        \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection,
        \Webkul\MpZipCodeValidator\Helper\Data $zipHelper,
        \Magento\Multishipping\Block\Checkout\Shipping $multiAddress
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productFactory = $productFactory;
        $this->mpProductFactory = $mpProductFactory;
        $this->regionCollection = $regionCollection;
        $this->zipcodeCollection = $zipcodeCollection;
        $this->zipHelper = $zipHelper;
        $this->multiAddress = $multiAddress;
        parent::__construct($context);
    }

    /**
     * MultiShippingResult execute function
     *
     * @return json
     */
    public function execute()
    {
        $allowOnlyZipCodeAtCheckout = $this->scopeConfig->getValue(
            'mpzipcode/general/enablecheckout',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $addresses = $this->multiAddress->getAddresses();
        $data = [];
        $result = [];
        $resultZip = [];
        foreach ($addresses as $address) {
            $zipcode = $address->getData('postcode');
            $items = $this->multiAddress->getAddressItems($address);
            $productIds = [];
            foreach ($items as $item) {
                $productIds[] = $item->getProductId();
            }
            foreach ($productIds as $productId) {
                $product = $this->productFactory->create()->load($productId);
                $productZipCodeValidation = $product->getZipCodeValidation();
                if ($productZipCodeValidation != self::NO_VALIDATION) {
                    list($available, $productName, $zips) = $this->getProductRegion(
                        $productId,
                        $zipcode
                    );
                    if (!$available) {
                        if (!in_array($productName, $result)) {
                            array_push($result, $productName);
                        }
                        if (!in_array($zips, $resultZip)) {
                            array_push($resultZip, $zips);
                        }
                    }
                }
            }
        }
        if ($allowOnlyZipCodeAtCheckout == 1) {
            if (!empty($result) && !empty($resultZip)) {
                $productNames = implode(", ", $result);
                $zipcodeData = implode(", ", $resultZip);
    
                $verb = count($result) > 1 ? "are" : "is" ;
                $data['message'] = __(
                    "%1 %2 not available at %3 ",
                    $productNames,
                    $verb,
                    $zipcodeData
                );
            }
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
    }

    /**
     * Get Product Region
     *
     * @param [string] $productId
     * @param [string] $zipcode
     * @return array
     */
    public function getProductRegion($productId, $zipcode)
    {
        $result = 1;
        $product = $this->productFactory->create()->load($productId);
        $productName = $product->getName();
        $regionIds = $this->validateZipCode($productId);
        try {
            if (!empty($regionIds)) {
                if (is_numeric($zipcode)) {
                    $zipcode = (int)$zipcode;
                    $zipcodeModel = $this->zipcodeCollection->create()
                    ->addFieldToFilter(
                        'region_zipcode_from',
                        ['lteq' => $zipcode]
                    )
                    ->addFieldToFilter(
                        'region_zipcode_to',
                        ['gteq' => $zipcode]
                    )
                    ->addFieldToFilter(
                        'region_id',
                        ['in', $regionIds]
                    );
                } else {
                    $zipcodeModel = $this->zipcodeCollection->create()
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
                }
                $result = $zipcodeModel->getSize();
            } else {
                $result = 0;
            }
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "Controller_ZipCode_MultiShipping getProductRegion : ".$e->getMessage()
            );
        }
        return [
            $result,
            $productName,
            $zipcode
        ];
    }

    /**
     * Validate Regions
     *
     * @param string $productId
     * @return array
     */
    public function validateZipcode($productId)
    {
        $regionIds = [];
        try {
            $product = $this->productFactory->create()->load($productId);
            $productZipCodeValidation = $product->getZipCodeValidation();
            $applyStatus = $this->getApplyStatus();
            if ($applyStatus) {
                $sellerId = 0;
                $collection = $this->mpProductFactory->create()->getCollection();
                $collection->addFieldToFilter(
                    'mageproduct_id',
                    ['eq' => $productId]
                );
                foreach ($collection as $model) {
                    $sellerId =$model->getSellerId();
                }
                if ($sellerId == '0') {
                    if ($productZipCodeValidation == self::DEFAULT_CONFIG) {
                        $availableregions = $this->getConfigRegion();
                        $regionIds = explode(',', $availableregions);
                    } elseif ($productZipCodeValidation == self::SPECIFIC_PRODUCT &&
                    !empty($product->getAvailableRegion())) {
                        $availableregions = $product->getAvailableRegion();
                        $regionIds = explode(',', $availableregions);
                    } elseif ($productZipCodeValidation == self::ALL_REGIONS) {
                        $regionIds = $this->getAllRegions($sellerId);
                    } elseif ($productZipCodeValidation == 0) {
                        $availableregions = $this->getConfigRegion();
                        $regionIds = explode(',', $availableregions);
                    }
                } else {
                    $collection = $this->regionCollection->create();
                    $collection->addFieldToFilter(
                        'seller_id',
                        ['eq' => $sellerId]
                    );
                    $sellerRegionIds = [];
                    foreach ($collection as $model) {
                        $sellerRegionIds[] = $model->getId();
                    }
                    $availableRegionIds = $this->productFactory->create()
                        ->load($productId)
                        ->getAvailableRegion();
                    if ($productZipCodeValidation == self::DEFAULT_CONFIG) {
                        if (empty($sellerRegionIds)) {
                            $availableregions = $this->getConfigRegion();
                        } else {
                            $availableregions = $this->getAvailableRegionIds(
                                $availableRegionIds,
                                $productId
                            );
                        }
                        $regionIds = explode(',', $availableregions);
                    } elseif ($productZipCodeValidation == self::SPECIFIC_PRODUCT && $availableRegionIds) {
                        if (empty($sellerRegionIds)) {
                            $availableRegions = $this->getConfigRegion();
                        } else {
                            $availableregions = $this->getAvailableRegionIds(
                                $availableRegionIds,
                                $productId
                            );
                        }
                        $regionIds = explode(',', $availableregions);
                    } elseif ($productZipCodeValidation == self::ALL_REGIONS) {
                        if (empty($sellerRegionIds)) {
                            $availableregions = $this->getConfigRegion();
                            $regionIds = explode(',', $availableregions);
                        } else {
                            $regionIds = $sellerRegionIds;
                        }
                    } else {
                        if (empty($sellerRegionIds)) {
                            $availableregions = $this->getConfigRegion();
                        } else {
                            $availableregions = $this->getAvailableRegionIds(
                                $availableRegionIds,
                                $productId
                            );
                        }
                        $regionIds = explode(',', $availableregions);
                    }
                }
            } else {
                $availableregions = $product->getAvailableRegion();
                if ($availableregions && !empty($availableregions) && $availableregions!=="") {
                    $regionIds = explode(',', $availableregions);
                }
            }
            if (!empty($regionIds)) {
                $enabledRegions = $this->regionCollection->create()
                ->addFieldToFilter('id', ['in' => $regionIds])
                ->addFieldToFilter('status', ['eq' => 1])
                ->addFieldToSelect('id');
                $regionIds = $enabledRegions->getColumnValues('id');
            } else {
                $regionIds = ['0'];
            }
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "Controller_ZipCode_Delete validateZipCode : ".$e->getMessage()
            );
        }
        return $regionIds;
    }

    /**
     * Get Config Value
     *
     * @return boolean
     */
    public function getConfigValue()
    {
        return $this->scopeConfig->getValue(
            'mpzipcode/general/enable',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get apply to all status value
     *
     * @return boolean
     */
    public function getApplyStatus()
    {
        return $this->scopeConfig->getValue(
            'mpzipcode/general/applyto',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get available region ids
     *
     * @param array $availableRegion
     * @param string $productId
     * @return array
     */
    public function getAvailableRegionIds($availableRegion, $productId)
    {
        if (empty($availableRegion)) {
            $regionIds = $this->getConfigRegion();
        } else {
            $regionIds = $this->productFactory->create()
                ->load($productId)
                ->getAvailableRegion();
        }
        return $regionIds;
    }

    /**
     * Get Configuration Region Ids
     *
     * @return string
     */
    public function getConfigRegion()
    {
        return $this->scopeConfig->getValue(
            'mpzipcode/general/regionids',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get All regions
     *
     * @param string $sellerId
     * @return array
     */
    public function getAllRegions($sellerId)
    {
        $collection = $this->regionCollection->create()
            ->addFieldToFilter(
                'seller_id',
                ['eq' => $sellerId]
            );
        $regionIds = [];
        foreach ($collection as $model) {
            $regionIds[] = $model->getId();
        }
        return $regionIds;
    }
}
