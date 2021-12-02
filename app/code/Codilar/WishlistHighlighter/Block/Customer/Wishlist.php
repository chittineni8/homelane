<?php
/**
 *
 * @package   Codilar\WishlistUrl
 * @author    Abhinav Vinayak <abhinav.v@codilar.com>
 * @copyright © 2021 Codilar
 * @license   See LICENSE file for license details.
 */

namespace Codilar\WishlistHighlighter\Block\Customer;


class Wishlist extends \Magento\Wishlist\Block\Customer\Wishlist
{

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('My Wish List'));
        $this->getChildBlock('wishlist_item_pager')
            ->setUseContainer(
                true
            )->setShowAmounts(
                true
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                10000
            )
            ->setCollection($this->getWishlistItems());
        return $this;
    }

}






