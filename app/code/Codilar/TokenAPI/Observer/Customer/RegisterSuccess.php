<?php
namespace Codilar\TokenAPI\Observer\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
class RegisterSuccess implements ObserverInterface
{

protected $customer;

protected $customerFactory;


   public function __construct(
    \Magento\Customer\Model\Customer $customer,
    \Magento\Customer\Model\ResourceModel\CustomerFactory $customerFactory,
     \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
)
{
    $this->customer = $customer;
    $this->customerFactory = $customerFactory;
     $this->_customerRepositoryInterface = $customerRepositoryInterface;
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
          $idvalue= $customerId * 10;
            
            $customerData->setCustomAttribute('homelane_user_id',$idvalue);
            $customerNew->updateData($customerData);
            // \Magento\Customer\Model\ResourceModel\CustomerFactory $customerFactory
            $customerResource = $this->customerFactory->create();
            $customerResource->saveAttribute($customerNew, 'homelane_user_id');
  
 




   }
}