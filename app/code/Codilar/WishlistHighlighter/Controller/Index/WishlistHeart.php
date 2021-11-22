<?php

/**
 *
 * @package   Codilar\WishlistHighlighter
 * @author    Abhinav Vinayak <abhinav.v@codilar.com>
 * @copyright © 2021 Codilar
 * @license   See LICENSE file for license details.
 */


namespace Codilar\WishlistHighlighter\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Helper\Data;

class WishlistHeart extends Action
{

    /**
     * @param Context $context
     * @param Data $wishlistHelper
     * @param StoreManagerInterface $storeManager
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context            $context,
        Data                    $wishlistHelper,
        StoreManagerInterface       $storeManager,
        JsonFactory $jsonFactory
    )
    {

        $this->wishlistHelper = $wishlistHelper;
        $this->jsonFactory = $jsonFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $result = $this->jsonFactory->create();
        $data = $this->wishlistHelper->getWishlistItemCollection()->addFieldToFilter('store_id', $this->getStoreId())->getData();

        return $result->setData(['status' => 200, 'items' => $data]);
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}
