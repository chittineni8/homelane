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

class Massdelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var zipcodeCollection
     */
    private $zipcodeCollection;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->zipcodeCollection = $zipcodeCollection;
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
            $zipcodeCollection = $this->zipcodeCollection->create()
                ->addFieldToFilter('region_id', $region->getId());
            if ($zipcodeCollection->getSize()) {
                $zipcodeCollection->walk('delete');
            }
            $collection->walk('delete');
        }
        $this->messageManager->addSuccess(__('Region(s) deleted succesfully'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
