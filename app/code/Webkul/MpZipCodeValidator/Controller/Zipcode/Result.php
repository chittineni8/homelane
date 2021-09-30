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
namespace Webkul\MpZipCodeValidator\Controller\Zipcode;

use Magento\Framework\App\Action\Action;
use \Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

/**
 * Result class gettting the valid zipcode.
 */

class Result extends Action
{
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_zipcode;

    /**
     * CookieManager
     *
     * @var CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var \Webkul\ZipCodeValidator\Helper\Data
     */
    protected $_zipcodeHelper;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @var Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var Webkul\MpZipCodeValidator\Model\AssignProduct
     */
    protected $_assignProduct;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_coreSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Webkul\Marketplace\Model\ProductFactory
     */
    protected $mpProductFactory;

    /**
     * @var Webkul\MpZipCodeValidator\Helper\Data
     */
    protected $zipHelper;

    /**
     * @param Context $context
     * @param CookieManagerInterface $cookieManager
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Catalog\Model\Product $product
     * @param \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcode
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Webkul\MpZipCodeValidator\Model\AssignProduct $assignProduct
     * @param \Webkul\MpZipCodeValidator\Model\Region $region
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param \Webkul\Marketplace\Model\ProductFactory $mpProductFactory
     * @param \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
     */
    public function __construct(
        Context $context,
        CookieManagerInterface $cookieManager,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\Model\Session $session,
        \Magento\Catalog\Model\Product $product,
        \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcode,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Webkul\MpZipCodeValidator\Model\AssignProduct $assignProduct,
        \Webkul\MpZipCodeValidator\Model\Region $region,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Webkul\Marketplace\Model\ProductFactory $mpProductFactory,
        \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
    ) {
        $this->_zipcode = $zipcode;
        $this->_customerUrl = $customerUrl;
        $this->_cookieManager = $cookieManager;
        $this->_session = $session;
        $this->_product = $product;
        $this->_assignProduct = $assignProduct;
        $this->_region = $region;
        $this->_coreSession = $coreSession;
        $this->_scopeConfig = $scopeConfig;
        $this->mpProductFactory = $mpProductFactory;
        $this->zipHelper = $zipHelper;
        parent::__construct($context);
    }

    /**
     * @return json
     */
    public function execute()
    {
        try {
            if ($this->getRequest()->getParam('currUrl')) {
                $this->coreSession->setCurrentProUrl(
                    $this->getRequest()->getParam('currUrl')
                );
            }
            if (strpos($this->_redirect->getRefererUrl(), 'customer/account/login')
                !== false && $this->_coreSession->getCurrentProUrl()
            ) {
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setUrl($this->_coreSession->getCurrentProUrl());
                return $resultRedirect;
            }
            $data = [];
            $data['addesses'] = '';
            $data['url'] = '';
            $address = $this->getAllAddressOfCustomer();
            
            if ($address) {
                $data['addesses'] = $address;
            } elseif (!$this->_session->getCustomerId()) {
                $data['url'] = $this->_customerUrl->getLoginUrl();
            }
            $zip = $this->getRequest()->getParam('zip');
            $productId = $this->getRequest()->getParam('productId');
            $value = $this->_scopeConfig->getValue(
                'mpzipcode/general/applyto',
                ScopeInterface::SCOPE_STORE
            );
            $cookie = $this->_cookieManager->getCookie('mpzip');
            if ($value) {
                $regionIds = $this->getConfigRegions($productId);
            } else {
                $regionId = [];
                if ($this->getRequest()->getParam('token')) {
                    $sellerId = $this->getRequest()->getParam('seller');
                    $joinConditions = 'main_table.id = mpzipcodevalidator_zipcode.region_id';
                    $collection = $this->_region->getCollection();
                    $collection->getSelect()->join(
                        ['mpzipcodevalidator_zipcode'],
                        $joinConditions,
                        []
                    )->reset('columns')
                    ->columns("mpzipcodevalidator_zipcode.region_id")
                        ->where("main_table.seller_id=$sellerId")
                        ->distinct();
                    foreach ($collection->getData() as $item) {
                        $regionId[] = $item['region_id'];
                    }
                    $regionIds = implode(',', $regionId);
                    $regionIds = $this->getAssignProductRegion($productId);
                    $regionIds = $this->getActiveRegions($regionIds);
                    if (empty($regionIds)) {
                        $regionIds = 0;
                    }
                } else {
                    $regionIds = explode(
                        ',',
                        $this->_product->load($productId)->getAvailableRegion()
                    );
                    $regionIds =  $this->getActiveRegions($regionIds);
                    if (empty($regionIds)) {
                        $regionIds = 0;
                    }
                }
            }
            if (!empty($regionIds)) {
                if (is_numeric($zip)) {
                    $zip = (int)$zip;
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
                } else {
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
                }
            }
            if ($collection->getSize()) {
                $data['product_zipcode'] = $zip;
                $data['product_id'] = $productId;
            }
            if ($cookie) {
                $data['cookieZip'] = $cookie;
                $cookiezip = trim($zip).','.$data['cookieZip'];
            } else {
                $cookiezip = trim($zip);
            }
            $this->_cookieManager->setPublicCookie('mpzip', $cookiezip);
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "Controller_Zipcode_Result execute : ".$e->getMessage()
            );
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
    }

    /**
     * Get all address of logged Customer
     *
     * @return array $customerAddress
     */
    public function getAllAddressOfCustomer()
    {
        $customerAddress = [];
        if ($this->_session->getCustomerId()) {
            $customer = $this->_session->getCustomer();
            foreach ($customer->getAddresses() as $address) {
                $addr = $address->toArray();
                $postcode = $addr['postcode'];
                $street = $addr['street'];
                $city = $addr['city'];
                if ($street && $city && $postcode) {
                    $custAddr = $postcode.' '.$street.' '.$city;
                }
                $customerAddress[] = substr($custAddr, 0, 20).'...';
            }
        }
        return $customerAddress;
    }
    
    /**
     * Get Assign product region ids
     *
     * @param array $regionArray
     * @return aaray $regionArray
     */
    public function getAssignProductRegion($productId)
    {
        $collection = $this->_assignProduct->getCollection();
        $collection->addFieldToFilter(
            "assign_id",
            ["eq" => $productId]
        );
        $regionIdArray = [];
        foreach ($collection as $model) {
            $regionIdArray[] = $model->getRegionIds();
            $regionArray = explode(',', implode(',', $regionIdArray));
        }
        return $regionArray;
    }

    /**
     * Get active region ids
     *
     * @param array $regionIds
     * @return void $regionIdArray
     */
    public function getActiveRegions($regionIds)
    {
        $collection = $this->_region->getCollection();
        $collection->addFieldToFilter(
            "status",
            ["eq" => "1"]
        )->addFieldToFilter(
            "id",
            ["in" => $regionIds]
        );
        $regionIdArray = [];
        if ($collection->getSize()) {
            foreach ($collection as $model) {
                $regionIdArray[] = $model->getId();
            }
        }
        return $regionIdArray;
    }

    /**
     * Get Apply to All product region ids
     *
     * @param string $productId
     * @return array $regionIds
     */
    public function getConfigRegions($productId)
    {
        $zipCodeValidation = $this->_product->load($productId)
                            ->getZipCodeValidation();
        $sellerId = 0;
        $regionIds = 0;
        if ($productId) {
            $collection = $this->mpProductFactory->create()->getCollection();
            $collection->addFieldToFilter(
                'mageproduct_id',
                ['eq' => $productId]
            );
            foreach ($collection as $model) {
                $sellerId =$model->getSellerId();
            }
        }
        if ($sellerId == '0') {
            if ($zipCodeValidation == '2') {
                $regionIds = $this->getApplyToAllRegionIds();
            } elseif ($zipCodeValidation == '0') {
                $regionIds = $this->_product->load($productId)->getAvailableRegion();
            } elseif ($zipCodeValidation == '3') {
                $collection = $this->_region->getCollection();
                $collection->addFieldToFilter(
                    'seller_id',
                    ['eq' => $sellerId]
                );
                $regionIds = [];
                foreach ($collection as $model) {
                    $regionIds[] = $model->getId();
                }
            } else {
                $regionIds = $this->getApplyToAllRegionIds();
            }
        } else {
            $collection = $this->_region->getCollection();
            $collection->addFieldToFilter(
                'seller_id',
                ['eq' => $sellerId]
            );
            $regionIds = [];
            foreach ($collection as $model) {
                $regionIds[] = $model->getId();
            }
            $availableRegion = $this->_product->load($productId)
                                ->getAvailableRegion();
            if ($zipCodeValidation == '2') {
                if (empty($regionIds)) {
                    $regionIds = $this->getApplyToAllRegionIds();
                } else {
                    $regionIds = $this->getAvailableRegionIds(
                        $availableRegion,
                        $productId
                    );
                }
            } elseif ($zipCodeValidation == '0') {
                if (empty($regionIds)) {
                    $regionIds = $this->getApplyToAllRegionIds();
                } else {
                    $regionIds = $this->getAvailableRegionIds(
                        $availableRegion,
                        $productId
                    );
                }
            } elseif ($zipCodeValidation == '3') {
                if (empty($regionIds)) {
                    $regionIds = $this->getApplyToAllRegionIds();
                } else {
                        $regionIds;
                }
            } else {
                if (empty($regionIds)) {
                    $regionIds = $this->getApplyToAllRegionIds();
                } elseif ($availableRegion) {
                    $regionIds = $this->getAvailableRegionIds($availableRegion, $productId);
                } else {
                    $regionIds = $this->getApplyToAllRegionIds();
                }
            }
        }
        $regionIds =  $this->getActiveRegions($regionIds);
        if (empty($regionIds)) {
            $regionIds = 0;
        }
        return $regionIds;
    }

    /**
     * Get Apply To All Region Ids
     *
     * @return array $regionids
     */
    public function getApplyToAllRegionIds()
    {
        $regionIds = $this->_scopeConfig->getValue(
            'mpzipcode/general/regionids',
            ScopeInterface::SCOPE_STORE
        );
        return $regionIds;
    }

    /**
     * Get Product Available Region Ids
     *
     * @param array $availableRegion
     * @param array $productId
     * @return array $regionIds
     */
    public function getAvailableRegionIds($availableRegion, $productId)
    {
        if (empty($availableRegion)) {
            $regionIds = $this->getApplyToAllRegionIds();
        } else {
            $regionIds = $this->_product->load($productId)->getAvailableRegion();
        }
        return $regionIds;
    }
}
