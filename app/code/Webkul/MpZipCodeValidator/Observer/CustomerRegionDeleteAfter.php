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

namespace Webkul\MpZipCodeValidator\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Webkul MpZipCodeValidator CustomerRegionDeleteAfter Observer
 */
class CustomerRegionDeleteAfter implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\ZipcodeFactory
     */
    protected $_zipcodeFactory;

    /**
     * @var \Webkul\MpZipCodeValidator\Helper\Data
     */
    protected $zipHelper;

    /**
     * Initialized Dependencies
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Webkul\MpZipCodeValidator\Model\RegionFactory $regionFactory
     * @param \Webkul\MpZipCodeValidator\Model\ZipcodeFactory $zipcodeFactory
     * @param \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Webkul\MpZipCodeValidator\Model\RegionFactory $regionFactory,
        \Webkul\MpZipCodeValidator\Model\ZipcodeFactory $zipcodeFactory,
        \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_regionFactory = $regionFactory;
        $this->_zipcodeFactory = $zipcodeFactory;
        $this->zipHelper = $zipHelper;
    }

    /**
     * Customer Region Delete After event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $customerId =  $observer->getCustomer()->getId();
            
            $regionPost = $this->_regionFactory->create();
            $regionCollection = $regionPost->getCollection();
    
            $zipcodePost = $this->_zipcodeFactory->create();
            $zipcodeCollection = $zipcodePost->getCollection();
    
            $regionCollection->addFieldToFilter("seller_id", ["eq" => $customerId]);
            foreach ($regionCollection as $regionModel) {
                $zipcodeCollection->addFieldToFilter("region_id", ["eq" => $regionModel->getId()]);
                if ($zipcodeCollection->getSize()) {
                    $zipcodeCollection->walk('delete');
                }
                $regionCollection->walk('delete');
            }
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "Observer_CustomerRegionDeleteAfter execute : ".$e->getMessage()
            );
        }
    }
}
