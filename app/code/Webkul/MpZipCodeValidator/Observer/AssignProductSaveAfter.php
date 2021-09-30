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
 * Webkul MpZipCodeValidator AssignProductSaveAfter Observer.
 */
class AssignProductSaveAfter implements ObserverInterface
{
    /**
     * @var \Webkul\MpZipCodeValidator\Model\AssignProduct
     */
    protected $_assignProduct;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Webkul\MpZipCodeValidator\Helper\Data
     */
    protected $zipHelper;

    /**
     * Initialized Dependencies
     *
     * @param \Webkul\MpZipCodeValidator\Model\AssignProduct $assignProduct
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
     */
    public function __construct(
        \Webkul\MpZipCodeValidator\Model\AssignProduct $assignProduct,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\RequestInterface $request,
        \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
    ) {
        $this->_assignProduct = $assignProduct;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->date = $date;
        $this->zipHelper = $zipHelper;
    }

    /**
     * Assign Product Save After event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $currentModel = $observer->getEvent()->getObject();
            $assignId = $currentModel->getId();
            $data = $this->request->getPostValue();
            $currentDate = $this->date->gmtDate();
            if (isset($data['region_assign_id'])) {
                $regionIds = implode(",", $data['product']['available_region']);
                $this->_assignProduct
                    ->load($data['region_assign_id'])
                    ->setRegionIds($regionIds)
                    ->setUpdatedAt($currentDate)
                    ->save();
            } else {
                if (isset($data['data']['available_region'])) {
                    $regionIds = implode(",", $data['product']['available_region']);
                    $productId = $data['product_id'];
                    $this->_assignProduct
                        ->setProductId($productId)
                        ->setRegionIds($regionIds)
                        ->setAssignId($assignId)
                        ->setUpdatedAt($currentDate)
                        ->setCreatedAt($currentDate)
                        ->save();
                }
            }
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "Observer_AssignProductSaveAfter execute : ".$e->getMessage()
            );
            $this->messageManager->addError($e->getMessage());
        }
    }
}
