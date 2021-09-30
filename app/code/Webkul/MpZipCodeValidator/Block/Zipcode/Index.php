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
namespace Webkul\MpZipCodeValidator\Block\Zipcode;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var Webkul\MpZipCodeValidator\Model\ZipcodeFactory
     */
    protected $_zipcode;

    /**
     * Webkul\MpZipCodeValidator\Model\ZipcodeFactory
     *
     * @var boolean
     */
    protected $_zipcodeCollection = false;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $_mpHelper;

    /**
     * @var \Webkul\MpZipCodeValidator\Helper\Data
     */
    protected $zipHelper;

    /**
     * @param Magento\Backend\Block\Template\Context         $context
     * @param Magento\Customer\Model\Session                 $customerSession
     * @param Webkul\MpZipCodeValidator\Model\ZipcodeFactory $zipcode
     * @param Webkul\Marketplace\Helper\Data                 $mpHelper
     * @param Webkul\MpZipCodeValidator\Helper\Data          $zipHelper
     * @param array                                          $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\MpZipCodeValidator\Model\ZipcodeFactory $zipcode,
        \Webkul\Marketplace\Helper\Data $mpHelper,
        \Webkul\MpZipCodeValidator\Helper\Data $zipHelper,
        array $data = []
    ) {
        $this->_zipcode = $zipcode;
        $this->_customerSession = $customerSession;
        $this->_mpHelper = $mpHelper;
        $this->zipHelper = $zipHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getAllZipcodes()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'wkzipcodevalidator.pager'
            )->setCollection(
                $this->getAllZipcodes()
            );
            $this->setChild('pager', $pager);
            $this->getAllZipcodes()->load();
        }

        return $this;
    }

    /**
     * Get pagerHtml
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get All region zipcodes
     *
     * @return Collection
     */
    public function getAllZipcodes()
    {
        $regionId = $this->getRequest()->getParam('id');
        try {
            if (!$this->_zipcodeCollection) {
                $data = $this->getRequest()->getParams();
                $filterid = '';
                $filterzipcodeFrom = '';
                $filterzipcodeTo = '';
                if (isset($data['fid'])) {
                    $filterid = $data['fid'] != "" ? $data['fid'] : '';
                }
                if (isset($data['fzipFrom'])) {
                    $filterzipcodeFrom = $data['fzipFrom'] != "" ? $data['fzipFrom'] : '';
                }
                if (isset($data['fzipTo'])) {
                    $filterzipcodeTo = $data['fzipTo'] != "" ? $data['fzipTo'] : '';
                }
                $collection = $this->_zipcode->create()
                    ->getCollection()
                    ->addFieldToFilter('region_id', $regionId);
                if ($filterid!= '') {
                    $collection->addFieldToFilter('id', ['like' => '%'.$filterid.'%']);
                }
                if ($filterzipcodeFrom != '') {
                    $collection->addFieldToFilter('region_zipcode_from', ['like' => '%'.$filterzipcodeFrom.'%']);
                }
                if ($filterzipcodeTo != '') {
                    $collection->addFieldToFilter('region_zipcode_to', ['like' => '%'.$filterzipcodeTo.'%']);
                }
                if ($filterzipcodeFrom != '' && $filterzipcodeTo != '') {
                    $collection->addFieldToFilter(
                        'region_zipcode_from',
                        ['like' => '%'.$filterzipcodeFrom.'%']
                    )
                    ->addFieldToFilter(
                        'region_zipcode_to',
                        ['like' => '%'.$filterzipcodeTo.'%']
                    );
                }
                $this->_zipcodeCollection = $collection;
            }
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "BLock_ZipCode_Index getAllZipCodes : ".$e->getMessage()
            );
        }
        return $this->_zipcodeCollection;
    }

    /**
     * Get Helper Object
     *
     * @return object
     */
    public function getMpHelper() : \Webkul\Marketplace\Helper\Data
    {
        return $this->_mpHelper;
    }
}
