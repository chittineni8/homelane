<?php
namespace Codilar\WishlistHighlighter\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Wishlist implements ArgumentInterface
{
    protected $customer;
    public function __construct(
        \Magento\Customer\Model\Session $customer,
        \Magento\Wishlist\Model\Wishlist $wishlist
    ) {
        $this->customer = $customer;
        $this->wishlist = $wishlist;
    }


    public function currentCustomer()
    {
      return "ADFDFWFF";

        $customer = $this->customer;

        return  $customerId = $customer->getId();
    }

    public function getWishlistByCustomerId()
    {
        $customerId = $this->currentCustomer();
        $wishlist = $this->wishlist->loadByCustomerId($customerId)->getItemCollection();

        return $wishlist;
    }


}
