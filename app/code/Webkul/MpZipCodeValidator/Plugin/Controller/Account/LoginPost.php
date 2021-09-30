<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpZipCodeValidator
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpZipCodeValidator\Plugin\Controller\Account;

use Magento\Customer\Controller\Account\LoginPost as Login;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;

class LoginPost
{
    /**
     * @param \Magento\Framework\App\RequestInterface $httpRequest
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param AccountRedirect $accountRedirect
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $httpRequest,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        AccountRedirect $accountRedirect
    ) {
        $this->_httpRequest = $httpRequest;
        $this->accountRedirect = $accountRedirect;
        $this->resultRedirect = $resultFactory;
    }

    /**
     * After Execute
     *
     * @param \Magento\Customer\Controller\Account\LoginPost $subject
     * @param mixed $result
     * @return void
     */
    public function afterExecute(\Magento\Customer\Controller\Account\LoginPost $subject, $result)
    {
        $zipCodeValidatorRequest = $subject->getRequest()->getParam('mpZipCodeValidator');
        if ($zipCodeValidatorRequest == 1) {
            $currentUrl = $this->_httpRequest->getServer('HTTP_REFERER');
            $resultRedirect = $this->resultRedirect->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($currentUrl);
            return $resultRedirect;
        } else {
            return $result;
        }
    }
}
