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

namespace Magedelight\SMSProfile\Controller\Account;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Encryption\Encryptor;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Store\Model\StoreManagerInterface;

class ForgotPasswordPost extends \Magento\Customer\Controller\Account\ForgotPasswordPost
{
     /**
      * Encryptor.
      *
      * @var Encryptor
      */
    private $encryptor;

     /**
      * CustomerRepository.
      *
      * @var CustomerRepository
      */
    private $customerRepository;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CollectionFactory $customerCollection
     * @param Escaper $escaper
     * @param Encryptor $encryptor
     * @param CustomerRepository $customerRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CollectionFactory $customerCollection,
        Escaper $escaper,
        Encryptor $encryptor,
        CustomerRepository $customerRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->customerCollection = $customerCollection;
        $this->encryptor = $encryptor;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        parent::__construct($context, $customerSession, $customerAccountManagement, $escaper);
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $email = (string)$this->getRequest()->getPost('email');
        if (is_numeric($email) && $this->getRequest()->getPost('forgetOtpValidation') !=1) {
            $message = __(
                'Please verify OTP.'
            );
                $this->session->setForgottenEmail($email);
                $this->messageManager->addErrorMessage($message);
                return $resultRedirect->setPath('*/*/forgotpassword');
        }

        if (is_numeric($email)) {
            $customerCollections = $this->getCustomerByPhone($email);
            foreach ($customerCollections as $customer) {
                $email = $customer->getEmail();
            }
            if ($email == '') {
                $message = __(
                    'Account with this number doesn\'t exist'
                );
                $this->session->setForgottenEmail($email);
                $this->messageManager->addErrorMessage($message);
                return $resultRedirect->setPath('*/*/forgotpassword');
            }
        }
        if ($this->getRequest()->getPost('forgetOtpValidation') ==1 && $this->getRequest()->getPost('password') != null) {
            $password = $this->getRequest()->getPost('password');
            $passwordConfirmation = $this->getRequest()->getPost('password_confirmation');
            $passwordHash =$this->encryptor->getHash($password, true);
            $websiteId = $this->storeManager->getWebsite()->getId();
            try {
                $customer = $this->customerRepository->get($email, $websiteId);
                $this->customerRepository->save($customer, $passwordHash);
                $this->messageManager->addSuccessMessage(
                    __('You have successfully reset password.')
                );
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $exception) {
                    $this->messageManager->addExceptionMessage(
                        $exception,
                        __('We\'re unable to send the password reset email.')
                    );
                    return $resultRedirect->setPath('*/*/forgotpassword');
            }

        } else {
            if ($email) {
                if (!\Zend_Validate::is($email, \Magento\Framework\Validator\EmailAddress::class)) {
                    $this->session->setForgottenEmail($email);
                    $this->messageManager->addErrorMessage(__('Please correct the email address.'));
                    return $resultRedirect->setPath('*/*/forgotpassword');
                }

                try {
                    $this->customerAccountManagement->initiatePasswordReset(
                        $email,
                        AccountManagement::EMAIL_RESET
                    );
                } catch (NoSuchEntityException $exception) {
                    // Do nothing, we don't want anyone to use this action to determine which email accounts are registered.
                } catch (SecurityViolationException $exception) {
                    $this->messageManager->addErrorMessage($exception->getMessage());
                    return $resultRedirect->setPath('*/*/forgotpassword');
                } catch (\Exception $exception) {
                    $this->messageManager->addExceptionMessage(
                        $exception,
                        __('We\'re unable to send the password reset email.')
                    );
                    return $resultRedirect->setPath('*/*/forgotpassword');
                }
                $this->messageManager->addSuccessMessage($this->getSuccessMessage($email));
                return $resultRedirect->setPath('*/*/');
            } else {
                $this->messageManager->addErrorMessage(__('Please enter your email.'));
                return $resultRedirect->setPath('*/*/forgotpassword');
            }
        }
    }

    public function getCustomerByPhone($phone)
    {
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('customer_mobile', $phone)
                           ->load();
        return $customerCollection;
    }
}
