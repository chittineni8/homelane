<?php

/**
 *
 * @package
 * @author
 *

 */

namespace Codilar\WishlistHighlighter\Block\Product;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;

class ListProduct extends Template

{
    protected $customer;
    /**
     *
     * @var StoreManagerInterface
     */
    protected $storemanagerinterface;


    /**
     * @var CollectionFactory
     */
    protected $_wishlistCollectionFactory;

    public function __construct(
        CollectionFactory                    $wishlistCollectionFactory,
        StoreManagerInterface                $storemanagerinterface,
        \Magento\Customer\Model\Session      $customer,
        \Codilar\Customer\Block\CustomerData $customerdata,
        \Magento\Framework\Registry $registry,
        \Magento\Wishlist\Model\Wishlist     $wishlist
    )
    {
        $this->customer = $customer;
        $this->customerdata = $customerdata;
         $this->registry = $registry;
        $this->wishlist = $wishlist;
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->storemanagerinterface = $storemanagerinterface;

    }

    public function currentCustomer()
    {

        return $customer = $this->customerdata->getCustomerId();


    }

    public function getWishlistByCustomerId()
    {
        $customerId = $this->currentCustomer();

        $collection = $this->_wishlistCollectionFactory->create()->addCustomerIdFilter($customerId);
        $baseurl = $this->storemanagerinterface->getStore()->getBaseUrl();

        $wishlistData = [];
        foreach ($collection as $item) {
            $productInfo = $item->getProduct()->toArray();
            $data = [

                'product_id' => $item->getProductId(),

            ];
            $wishlistData[] = $data;
        }//end foreach
        return $wishlistData;
    }



}
