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
namespace Webkul\MpZipCodeValidator\Controller\Adminhtml\Zipcode;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory;

class Delete extends \Magento\Backend\App\Action
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
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_MpZipCodeValidator::zipcode');
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $regionId = $this->_session->getRegionId();
        $allZipCodeIds = $this->_collectionFactory->create()
        ->addFieldToFilter('region_id', $regionId)->getColumnValues('id');
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        foreach ($collection as $zipcode) {
            if (in_array($zipcode->getId(), $allZipCodeIds)) {
                $this->deleteZipCode($zipcode);
            }
        }
        $this->messageManager->addSuccess(__('Zipcode deleted succesfully'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index', ['region_id' => $regionId]);
    }

    /**
     * DeleteZipCode
     *
     * @param  object $item
     * @return object
     */
    public function deleteZipCode($item)
    {
        $item->delete();
    }
}
