<?php

namespace Codilar\TokenAPI\Observer\Customer;

use Codilar\TokenAPI\Model\Plugin\Controller\Account\RestrictCustomer;
use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Encryption\EncryptorInterface;
use Codilar\TokenAPI\Logger\Logger;
use Magento\Framework\Exception\LocalizedException;

class RegisterSuccess implements ObserverInterface
{

    protected $customer;

    protected $customerFactory;

    /**
     * @var EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var LoggerResponse
     */
    private $loggerResponse;

    /**
     * @var RestrictCustomer
     */
    private $restrictcustomer;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;


    public function __construct(
        RestrictCustomer $restrictcustomer,
        Customer $customer,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        CustomerRegistry $customerRegistry,
        Logger $loggerResponse,
        EncryptorInterface $encryptor
    ) {
        $this->customer        = $customer;
        $this->customerFactory = $customerFactory;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->restrictcustomer             = $restrictcustomer;
        $this->customerRegistry             = $customerRegistry;
        $this->_encryptor                   = $encryptor;
        $this->loggerResponse               = $loggerResponse;

    }//end __construct()


    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $customers = $observer->getEvent()->getData('customer');

            $customerId   = $customers->getId();
            $customerNew  = $this->customer->load($customerId);
            $customerData = $customerNew->getDataModel();
            $idvalue      = $this->restrictcustomer->getUserId();

            $customerData->setCustomAttribute('homelane_user_id', $idvalue);
            $customerNew->updateData($customerData);
            // \Magento\Customer\Model\ResourceModel\CustomerFactory $customerFactory
            $customerResource = $this->customerFactory->create();

            $password = $this->restrictcustomer->getPassword();
            $customer = $this->_customerRepositoryInterface->getById($customerId);

            $customerSecure = $this->customerRegistry->retrieveSecureData($customerId);
            $customerSecure->setRpToken(null);
            $customerSecure->setRpTokenCreatedAt(null);
            $customerSecure->setPasswordHash($this->_encryptor->getHash($password, true));
            $this->_customerRepositoryInterface->save($customer);
            $customerResource->saveAttribute($customerNew, 'homelane_user_id');
        } catch (Exception $e) {
            $this->loggerResponse->critical($e->getMessage().' '.'Register Event Exception');
        }//end try

    }//end execute()


}//end class
