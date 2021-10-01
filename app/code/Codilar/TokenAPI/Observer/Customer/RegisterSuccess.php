<?php
namespace Codilar\TokenAPI\Observer\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Encryption\EncryptorInterface;
class RegisterSuccess implements ObserverInterface
{

protected $customer;

protected $customerFactory;

 /**
     * @var EncryptorInterface
     */
    protected $_encryptor;


/**
 * @var \Codilar\TokenAPI\Model\Plugin\Controller\Account\RestrictCustomer
 */
private $restrictcustomer;

protected $customerRegistry;

   public function __construct(\Codilar\TokenAPI\Model\Plugin\Controller\Account\RestrictCustomer $restrictcustomer,
    \Magento\Customer\Model\Customer $customer,
    \Magento\Customer\Model\ResourceModel\CustomerFactory $customerFactory,
     \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
      CustomerRegistry  $customerRegistry,
      EncryptorInterface  $encryptor
)
{
    $this->customer = $customer;
    $this->customerFactory = $customerFactory;
     $this->_customerRepositoryInterface = $customerRepositoryInterface;
      $this->restrictcustomer =$restrictcustomer;
      $this->customerRegistry = $customerRegistry;
      $this->_encryptor = $encryptor;
}
   /**
    * Below is the method that will fire whenever the event runs!
    *
    * @param Observer $observer
    */
   public function execute(Observer $observer)
   {
        $customers = $observer->getEvent()->getData('customer');
  
    $customerId= $customers->getId();
      $customerNew = $this->customer->load($customerId);
            $customerData = $customerNew->getDataModel();
          $idvalue= $this->restrictcustomer->getUserId();
            
            $customerData->setCustomAttribute('homelane_user_id',$idvalue);
            $customerNew->updateData($customerData);
            // \Magento\Customer\Model\ResourceModel\CustomerFactory $customerFactory
            $customerResource = $this->customerFactory->create();
            $customerResource->saveAttribute($customerNew, 'homelane_user_id');


                    $password = $this->restrictcustomer->getPassword();
                    $customer = $this->customerRepositoryInterface->getById($customerId);
                    // $this->customerRepository->save($customer, $this->_encryptor->getHash($password, true));
                    $customerSecure = $this->customerRegistry->retrieveSecureData($customerId);
                    $customerSecure->setRpToken(null);
                    $customerSecure->setRpTokenCreatedAt(null);
                    $customerSecure->setPasswordHash($this->_encryptor->getHash($password, true));
                $this->customerRepositoryInterface->save($customer);
  
 




   }
}