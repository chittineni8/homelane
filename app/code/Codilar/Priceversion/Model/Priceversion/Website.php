<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Model\Priceversion;
class Website implements \Magento\Framework\Option\ArrayInterface
{
      /**
    * @var \Magento\Store\Model\WebsiteFactory
    */
    protected $_websiteFactory;

    public function __construct(
      \Magento\Store\Model\WebsiteFactory $websiteFactory,
      array $data = []
    ) {
      $this->_websiteFactory = $websiteFactory;
    //  parent::__construct($context, $registry, $formFactory, $data);
    }
    public function toOptionArray()
    {
        return   $websites = $this->_websiteFactory->create()->getCollection()->toOptionArray();
    }
}
