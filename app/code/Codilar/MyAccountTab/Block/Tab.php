<?php

namespace Codilar\MyAccountTab\Block;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class Tab extends Template
{

    protected $customer;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @param Context $context
     * @param Session $customer
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        UrlInterface $urlInterface,
        Context $context,
        Session $customer,
        array   $data = []
    )
    {   $this->storeManager = $storeManager;
        $this->urlInterface = $urlInterface;
        $this->customer = $customer;
        parent::__construct($context, $data);

    }//end __construct()


    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomer()
    {
        $customer = $this->customer;
        $customerData[] = [
            'customerName' => $customer->getCustomer()->getName(),
            'customerId' => $customer->getId(),
            'customerEmail' => $customer->getCustomer()->getEmail()
        ];
        return $customerData;

    }//end getCustomer()
    public function getCurrentUrl()
    {

        return   $currentUrl = $this->urlInterface->getCurrentUrl();
    }
    public function getBaseUrl()
    {
        return  $storeUrl = $this->storeManager->getStore()->getBaseUrl();
    }


}//end class
