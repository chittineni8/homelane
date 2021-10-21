<?php
/**
 *
 *
 */
namespace Codilar\WishlistAPI\Model\Api;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\Customer;
use Magento\Directory\Model\CountryFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\ResourceModel\Item\Collection;
use Codilar\WishlistAPI\Api\WishlistManagementInterface;
use Magento\Wishlist\Controller\WishlistProvider;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Codilar\TokenAPI\Logger\Logger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Api\AccountManagementInterface;
/**
 * Defines the implementaiton class of the WishlistManagementInterface
 */
class WishlistManagement implements WishlistManagementInterface
{

    /**
     * @var CollectionFactory
     */
    protected $_wishlistCollectionFactory;

    /**
     * Wishlist item collection
     *
     * @var Collection
     */
    protected $_itemCollection;

    /**
     * @var WishlistRepository
     */
    protected $_wishlistRepository;

    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * @var WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * @var Item
     */
    protected $_itemFactory;

    /**
     * @var Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $productImageHelper;

    /**
     *
     * @var StoreManagerInterface
     */
    protected $storemanagerinterface;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    /**
     * @var Product
     */
    protected $_productload;

    /**
     * @var CountryFactory
     */
    protected $countryfactory;


     protected $customerRepository;


     protected $jsonFactory;

     /**
     * @var LoggerResponse
     */
    private $loggerResponse;


    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;



    protected $customerCollection;


     /**
* Request instance
*
* @var \Magento\Framework\App\RequestInterface
*/
protected $request;


    /**
     * @param CollectionFactory                  $wishlistCollectionFactory
     * @param ProductFactory                     $productFactory
     * @param \Magento\Framework\Math\Random     $mathRandom
     * @param Customer                           $customer
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param ProductRepositoryInterface         $productRepository
     */
    public function __construct(
        CollectionFactory $wishlistCollectionFactory,
        WishlistFactory $wishlistFactory,
        RequestInterface $request,
        Customer $customer,
        AppEmulation $appEmulation,
        CountryFactory $countryfactory,
        JsonFactory $jsonFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface  $customerAccountManagement,
        StoreManagerInterface $storemanagerinterface,
        ProductImageHelper $productImageHelper,
        CustomerCollection $customerCollection,
        Product $productload,
        Logger                $loggerResponse,
        WishlistFactory $wishlistRepository,
        ProductRepositoryInterface $productRepository,
        ItemFactory $itemFactory
    ) {
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->_wishlistRepository        = $wishlistRepository;
        $this->customerCollection         = $customerCollection;
        $this->_productRepository         = $productRepository;
        $this->customerRepository         = $customerRepository;
        $this->_wishlistFactory           = $wishlistFactory;
        $this->countryfactory             = $countryfactory;
        $this->storemanagerinterface      = $storemanagerinterface;
        $this->_itemFactory               = $itemFactory;
        $this->_customer                  = $customer;
        $this->_productload               = $productload;
        $this->appEmulation               = $appEmulation;
        $this->productImageHelper         = $productImageHelper;
        $this->_customer                  = $customer;
        $this->request                    = $request;
        $this->jsonFactory                = $jsonFactory;
        $this->loggerResponse             = $loggerResponse;
        $this->customerAccountManagement = $customerAccountManagement;

    }//end __construct()


    /**
     * Get wishlist collection
     *
     * @deprecated
     * @param      $customerEmail
     * @return     wishlistResponse
     */
    public function getWishlistForCustomer($customerEmail)
    {
        try{
       if(empty($customerEmail) || $customerEmail == null){
           $response = ['result' => ['status' => 400, 'message' => 'Parameters not found']]; 
            return $response;
            
        }


        if($this->emailExistOrNot($customerEmail)):

           $response = ['result' => ['status' => 400, 'message' => 'This Email Does Not Exist']]; 
            return $response;

        endif;

        $customerId = $this->getCustomerIdByEmail($customerEmail);
        if (empty($customerId) || !isset($customerId) || $customerId == '') {
           $this->loggerResponse->addInfo("========================GET WISHLIST DATA ERROR========================");
        $this->loggerResponse->addInfo('Id required');
       $this->loggerResponse->addInfo("===================================================================");
        } else {
            $collection   = $this->_wishlistCollectionFactory->create()->addCustomerIdFilter($customerId);
            $baseurl      = $this->storemanagerinterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product';
            $wishlistData = [];
            foreach ($collection as $item) {
                $productInfo = $item->getProduct()->toArray();
                 $data           = [
                    'wishlist_item_id' => $item->getWishlistItemId(),
                    'wishlist_id'      => $item->getWishlistId(),
                    'product_id'       => $item->getProductId(),
                    'store_id'         => $item->getStoreId(),
                    'added_at'         => $item->getAddedAt(),
                    'description'      => $item->getDescription(),
                    'qty'              => round($item->getQty()),
                    'product'          => $productInfo,
                ];
                $wishlistData[] = $data;
            }//end foreach
 $wishlistResponse = ['result' => ['status' => 200, 'message' => 'Success', 'Details' => $wishlistData]]; 
            return $wishlistResponse;
        }//end if

    }catch (\Exception $e) {
            $this->loggerResponse->critical($e->getMessage() . ' ' . 'GET WISHLIST DETAILS API  EXCEPTION');
        }//end try

    }//end getWishlistForCustomer()


    /**
     * Delete wishlist item for the customer
     *
     * @return array|boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteWishlistForCustomer($customerEmail, $productId)
    {
        try{

        if(empty($customerEmail) || $customerEmail == null || empty($productId) || $productId == null){
           $response = ['result' => ['status' => 400, 'message' => 'Parameters not found']]; 
            return $response;
            
        }
        if($this->emailExistOrNot($customerEmail)):

            $response = ['result' => ['status' => 400, 'message' => 'This Email Does Not Exist']]; 
            return $response;

        endif;


        if($this->productExistById($productId)):

       $customerId = $this->getCustomerIdByEmail($customerEmail);
            $collection   = $this->_wishlistCollectionFactory->create()->addCustomerIdFilter($customerId);
            
            foreach ($collection as $item) {
                if ($item->getProductId() == $productId) {
            $item->delete();
            $collection->save();
             
        }
       
        $wishlistDeleteResponse = ['result' => ['status' => 200, 'message' => 'Product Deleted Successfully']]; 
            return $wishlistDeleteResponse;
             
    }
else:
    $wishlistDeleteResponse = ['result' => ['status' => 400, 'message' => 'Id not found']]; 
            return $wishlistDeleteResponse;
       endif;
    }catch (\Exception $e) {
            $this->loggerResponse->critical($e->getMessage() . ' ' . 'WISHLIST DELETE ITEMS API EXCEPTION');
        }//end try

    }//end deleteWishlistForCustomer()


/**
     * Update customer data by homelane user id 
     *
     * @param  string  $customerEmail
     * @param  integer $productIdId
     * @return array|boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
 public function changeCustomerInfo()
 {
    try{
    $params =$this->request->getParams();
   if($params['customerEmail'] == null || $params['customerName'] == null || $params['phone'] == null):

   $UserResponse = ['result' => ['status' => 400, 'message' => 'Parameters Missing']]; 
   return $UserResponse;
   endif;

if($params['user_id']):

 $collection= $this->customerCollection->addAttributeToSelect('*')
                  ->addAttributeToFilter('homelane_user_id',$params['user_id'])
                  ->load();
               $c_data=$collection->getData();
   $c_data[0]['entity_id'];
   $customerID =  $c_data[0]['entity_id'];
  
    $customer = $this->customerRepository->getById($customerID);
    $customer->setEmail($params['customerEmail']); 
    $customer->setFirstname($params['customerName']); 
    $customer->setCustomAttribute('customer_mobile',$params['phone']); 
    $this->customerRepository->save($customer);
    $UserResponse = ['result' => ['status' => 200, 'message' => 'Customer Data Updated Successfully']]; 
            return $UserResponse;
else:

      $UserResponse = ['result' => ['status' => 400, 'message' => 'User ID Missing']]; 
            return $UserResponse;
endif;

}catch (\Exception $e) {
            $this->loggerResponse->critical($e->getMessage() . ' ' . 'Change Customer Info  EXCEPTION');
        }//end try
 }

    /**
     * Helper function that provides full cache image url
     *
     * @param  Product
     * @return string
     */
    public function getImageUrl($product, string $imageType='')
    {
        $storeId = $this->storemanagerinterface->getStore()->getId();
        $this->appEmulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
        $imageUrl = $this->productImageHelper->create()->init($product, $imageType)->getUrl();
        $this->appEmulation->stopEnvironmentEmulation();

        return $imageUrl;

    }//end getImageUrl()


    public function getCustomerIdByEmail(string $email)
    {
        $customerId = null;
        try {
            $customerData = $this->customerRepository->get($email);
            $customerId = (int)$customerData->getId();
        }catch (NoSuchEntityException $noSuchEntityException){
        }
        return $customerId;
    }

    /**
     *
     * @param $email
     * @return bool
     */
    public function emailExistOrNot($email): bool
    {

        $websiteId = (int)$this->storemanagerinterface->getWebsite()->getId();
        $isEmailNotExists = $this->customerAccountManagement->isEmailAvailable($email, $websiteId);
        return $isEmailNotExists;
    }


     public function productExistById($productId)
    {
        if ($this->_productRepository->getById($productId)) 
        {
           return true;
        }
    }


}//end class
