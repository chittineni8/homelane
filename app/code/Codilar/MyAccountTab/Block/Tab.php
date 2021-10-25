<?php

namespace Codilar\MyAccountTab\Block;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Tab extends Template
{

    protected $customer;


    /**
     * @param Context $context
     * @param Session $customer
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customer,
        array   $data = []
    )
    {
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


}//end class
