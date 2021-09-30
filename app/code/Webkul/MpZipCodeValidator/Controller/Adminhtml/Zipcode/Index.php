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

use Webkul\MpZipCodeValidator\Controller\Adminhtml\Zipcode as ZipcodeController;
use Magento\Framework\Controller\ResultFactory;

class Index extends ZipcodeController
{
    /**
     * @var \Webkul\MpZipCodeValidator\Model\Region
     */
    protected $_region;

    /**
     * @param \Magento\Backend\App\Action\Context     $context
     * @param \Webkul\MpZipCodeValidator\Model\Region $region
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\MpZipCodeValidator\Model\Region $region
    ) {
        $this->_region = $region;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $regionId = $this->getRequest()->getParam('id');
        $regionName = $this->_region->load($regionId)->getRegionName();
        $this->_session->setRegionId('');
        $this->_session->setRegionId($regionId);
        $resultPage->getConfig()->getTitle()->prepend(__($regionName.' Zipcode List'));
        return $resultPage;
    }
}
