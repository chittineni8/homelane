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
namespace Webkul\MpZipCodeValidator\Block\Region;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory
     */
    protected $_region;

    /**
     * $_regionCollection
     *
     * @var boolean
     */
    protected $_regionCollection = false;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $mpHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Webkul\MpZipCodeValidator\Helper\Data
     */
    protected $zipHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @param \Magento\Backend\Block\Template\Context                                $context
     * @param Magento\Customer\Model\Session                                         $customerSession
     * @param Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory $region
     * @param \Webkul\Marketplace\Helper\Data                                        $mpHelper
     * @param \Magento\Framework\Json\Helper\Data                                    $jsonHelper
     * @param \Webkul\MpZipCodeValidator\Helper\Data                                 $zipHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                            $date
     * @param array                                                                  $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory $region,
        \Webkul\Marketplace\Helper\Data $mpHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\MpZipCodeValidator\Helper\Data $zipHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    ) {
        $this->_region = $region;
        $this->_customerSession = $customerSession;
        $this->mpHelper = $mpHelper;
        $this->jsonHelper = $jsonHelper;
        $this->zipHelper = $zipHelper;
        $this->date = $date;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getAllRegions()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'wkzipcodevalidator.pager'
            )->setCollection(
                $this->getAllRegions()
            );
            $this->setChild('pager', $pager);
            $this->getAllRegions()->load();
        }

        return $this;
    }

    /**
     * Get pagerhtml
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get Store Collection
     *
     * @return collection
     */
    public function getAllRegions()
    {
        $sellerId = $this->_customerSession->getCustomerId();
        try {

            if (!$this->_regionCollection) {
                $paramData = $this->getRequest()->getParams();
                $filter = '';
                $filterStatus = '';
                $filterDateFrom = '';
                $filterDateTo = '';
                $from = null;
                $to = null;
                $filter = $this->getParamData($paramData, 's');
                $filterStatus = $this->getParamData($paramData, 'status');
                $filterDateFrom = $this->getParamData($paramData, 'from_date');
                $filterDateTo = $this->getParamData($paramData, 'to_date');
                if ($filter == "" && $filterDateFrom == "" && $filterDateTo == "") {
                    $this->_regionCollection = $this->_region->create()->addFieldToFilter('seller_id', $sellerId);
                }
                $collection = $this->_region->create()
                    ->addFieldToFilter('seller_id', $sellerId)
                    ->addFieldToFilter('region_name', ['like' => '%'.$filter.'%']);
                if ($filterStatus != '') {
                    $collection->addFieldToFilter('status', $filterStatus);
                }
                if ($filterDateTo && preg_match("/^[0-9- ]+$/D", $filterDateTo)) {
                    $todate = date_create($filterDateTo);
                    $to = date_format($todate, 'Y-m-d 23:59:59');
                }
                if (!$to) {
                    $to = $this->date->gmtDate();
                }
                if ($filterDateFrom && preg_match("/^[0-9- ]+$/D", $filterDateFrom)) {
                    $fromdate = date_create($filterDateFrom);
                    $from = date_format($fromdate, 'Y-m-d H:i:s');
                }
                if ($from && $to) {
                    $collection->addFieldToFilter('created_at', ["from" => $from, "to" => $to]);
                } elseif ($filter == "" && $filterStatus == "") {
                    return $this->_regionCollection;
                }
                $this->_regionCollection = $collection;
            }
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "BLock_Region_View getAllRegions : ".$e->getMessage()
            );
        }
        return $this->_regionCollection;
    }

    /**
     * Get param data
     *
     * @param array $paramData
     * @param string $string
     * @return void
     */
    public function getParamData($paramData, $string)
    {
        if (isset($paramData[$string])) {
            return $paramData[$string] != '' ? $paramData[$string] : '';
        } else {
            return '';
        }
    }

    /**
     * Get Helper Object
     *
     * @return object
     */
    public function getMpHelper() : \Webkul\Marketplace\Helper\Data
    {
        return $this->mpHelper;
    }

    /**
     * Get Json Helper Object
     *
     * @return \Magento\Framework\Json\Helper\Data
     */
    public function getJsonHelper() : \Magento\Framework\Json\Helper\Data
    {
        return $this->jsonHelper;
    }
}
