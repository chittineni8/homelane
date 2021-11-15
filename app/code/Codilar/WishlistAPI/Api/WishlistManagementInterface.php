<?php

namespace Codilar\WishlistAPI\Api;

/**
 * Interface WishlistManagementInterface
 * @api
 */
interface WishlistManagementInterface
{

    /**
     * Return Wishlist items.
     *
     * @param string $customerEmail
     * @return array
     */
    public function getWishlistForCustomer($customerEmail);

    /**
     * Return response for delete wishlist item.
     *
     * @param string $customerEmail
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function deleteWishlistForCustomer($customerEmail,$productId,$storeId);


    /**
     * Return response after Updating Customer Data
     *
     * @param string $customerEmail
     * @param string $user_id
     * @param string $customerName
     * @param int $phone
     * @return array
     *
     */
    public function changeCustomerInfo();

}
