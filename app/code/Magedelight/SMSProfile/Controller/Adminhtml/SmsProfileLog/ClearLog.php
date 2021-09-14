<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */
 
namespace Magedelight\SMSProfile\Controller\Adminhtml\SmsProfileLog;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magedelight\SMSProfile\Model\SMSProfileLogFactory;

class ClearLog extends Action
{
    /**
    * @var string
    */
    const ACTION_RESOURCE = 'Magedelight_SMSProfile::smsprofilelog';

    /**
     * RedirectFactory
     *
     * @var resultRedirect
     */
    private $resultRedirect;

    /** @var SMSProfileLogFactory */
    private $smsprofilelog;

    /**
     * @param Context $context
     * @param SMSProfileLogFactory $smsprofilelog
     * @param RedirectFactory $resultRedirect
     */
    public function __construct(
        Context $context,
        SMSProfileLogFactory $smsprofilelog,
        RedirectFactory $resultRedirect
    ) {
        parent::__construct($context);
        $this->resultRedirect = $resultRedirect;
        $this->smsprofilelog = $smsprofilelog;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ACTION_RESOURCE);
    }

     /**
      * SmsLog clear for AJAX request
      *
      * @return RedirectFactory
      */
    public function execute()
    {
        $resultRedirect = $this->resultRedirect->create();
        $sms  = $this->smsprofilelog->create();
        try {
            $sms->SmsProfileClearelog();
            $this->messageManager->addSuccess(__('The Sms Log has been cleared.'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }
}
