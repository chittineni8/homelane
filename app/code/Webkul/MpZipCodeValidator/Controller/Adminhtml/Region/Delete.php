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

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var region
     */
    private $region;

    /**
     * @var zipcodeCollection
     */
    private $zipcodeCollection;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\Zipcode
     */
    protected $zipcode;

    /**
     * @var \Webkul\MpZipCodeValidator\Helepr\Data
     */
    protected $zipHelper;

   /**
    * @param Context $context
    * @param \Webkul\MpZipCodeValidator\Model\Region $region
    * @param \Webkul\MpZipCodeValidator\Model\Zipcode $zipcode
    * @param \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection
    * @param \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
    */
    public function __construct(
        Context $context,
        \Webkul\MpZipCodeValidator\Model\Region $region,
        \Webkul\MpZipCodeValidator\Model\Zipcode $zipcode,
        \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection,
        \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
    ) {
        $this->region = $region;
        $this->zipcode = $zipcode;
        $this->zipcodeCollection = $zipcodeCollection;
        $this->zipHelper = $zipHelper;
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
        try {
            $data=$this->getRequest()->getParams();
            if (isset($data['id'])) {
                $id=$data['id'];
                $region = $this->region->load($id);
                if ($region) {
                    $this->removeItem($region);
                    $zipcodeCollection = $this->zipcodeCollection->create()
                        ->addFieldToFilter('region_id', $id);
                    if ($zipcodeCollection->getSize()) {
                        foreach ($zipcodeCollection as $zipcode) {
                            $this->zipcode->removeItem($zipcode);
                        }
                    }
                    $this->messageManager->addSuccess(__('Region deleted successfully'));
                }
            } else {
                $this->messageManager->addError(__('Region Id is Invalid'));
            }
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "Controller_Admin_Region_Delete execute : ".$e->getMessage()
            );
            $this->messageManager->addError(__('Something went wrong !!!'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
