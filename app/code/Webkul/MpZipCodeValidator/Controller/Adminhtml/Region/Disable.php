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
namespace Webkul\MpZipCodeValidator\Controller\Adminhtml\Region;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory;

class Disable extends \Magento\Backend\App\Action
{
    const DISABLE_REGION = 0;

    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\Region
     */
    protected $region;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param \Webkul\MpZipCodeValidator\Model\Region $region
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Webkul\MpZipCodeValidator\Model\Region $region,
        \Webkul\MpZipCodeValidator\Model\Zipcode $zipcode,
        CollectionFactory $collectionFactory
    ) {
        $this->_filter = $filter;
        $this->region = $region;
        $this->zipcode = $zipcode;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_MpZipCodeValidator::region');
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        foreach ($collection as $region) {
            $region->setStatus(self::DISABLE_REGION);
            $region->save();
        }
        $this->messageManager->addSuccess(__('Regions disabled succesfully'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
