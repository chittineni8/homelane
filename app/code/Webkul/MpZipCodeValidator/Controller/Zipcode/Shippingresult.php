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

class Shippingresult extends Action
{
    /**
     * @var Constant variables
     */
    const NO_VALIDATION = 1;
    const DEFAULT_CONFIG = 2;
    const SPECIFIC_PRODUCT = 0;
    const ALL_REGIONS = 3;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $zipcodeCollection;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory
     */
    protected $regionCollection;

    /**
     * @var \Webkul\Marketplace\Model\ProductFactory
     */
    protected $mpProductFactory;

    /**
     * @var \Webkul\MpZipCodeValidator\Helper\Data
     */
    protected $zipHelper;

    /**
     * @param Context $context
     * @param \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory $regionCollection
     * @param \Webkul\Marketplace\Model\ProductFactory $mpProductFactory
     * @param \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
     */
    public function __construct(
        Context $context,
        \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory $regionCollection,
        \Webkul\Marketplace\Model\ProductFactory $mpProductFactory,
        \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
    ) {
        $this->zipcodeCollection = $zipcodeCollection;
        $this->scopeConfig = $scopeConfig;
        $this->productFactory = $productFactory;
        $this->regionCollection = $regionCollection;
        $this->mpProductFactory = $mpProductFactory;
        $this->zipHelper = $zipHelper;
        parent::__construct($context);
    }

    /**
     * @return json
     */
    public function execute()
    {
        $allowOnlyZipCodeAtCheckout = $this->scopeConfig->getValue(
            'mpzipcode/general/enablecheckout',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $data = [];
        if ($this->getConfigValue()) {
            $result = [];
            $zip = $this->getRequest()->getParam('zip');
            $productIds = $this->getRequest()->getParam('productId');
            foreach ($productIds as $id) {
                $product = $this->productFactory->create()->load($id);
                $productZipCodeValidation = $product->getZipCodeValidation();
                if ($allowOnlyZipCodeAtCheckout == 1) {
                    if ($productZipCodeValidation != 1) {
                        list($available, $productName) = $this->getProductRegion(
                            $id,
                            $zip
                        );
                        if (!$available) {
                            $result[] = $productName;
                        }
                    }
                }
            }
            if ($zip && $zip!=="" && !empty($result)) {
                $productNames = implode(", ", $result);
                $verb = count($result) > 1 ? "are" : "is" ;
                $data['message'] = __(
                    "%1 %2 not available at %3",
                    $productNames,
                    $verb,
                    $zip
                );
            }
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
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
     * Get Product Regions
     *
     * @param [string] $id
     * @param [string] $zip
     * @return array
     */
    public function getProductRegion($id, $zip)
    {
        $result = 1;
        $product = $this->productFactory->create()->load($id);
        $productName = $product->getName();
        $regionIds = $this->validateZipCode($id);
        if (!empty($regionIds)) {
            if (is_numeric($zip)) {
                $zip = (int)$zip;
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
        return [
            $result,
            $productName
        ];
    }

    /**
     * Validate ZipCodes
     *
     * @param [string] $productId
     * @return array
     */
    public function validateZipCode($productId)
    {
        $regionIds = [];
        try {
            $product = $this->productFactory->create()
                ->load($productId);
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
                    $availableregions = [];
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
                "MpZipCodeValidator_validateZipCode Exception : ".$e->getMessage()
            );
        }
        return $regionIds;
    }

    /**
     * Get Apply to all status value
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
     * Get Configuration regions
     *
     * @return return string
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
     * @param [string] $sellerId
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

    /**
     * Get Available region ids
     *
     * @param [array] $availableRegion
     * @param [string] $productId
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
}
