<?php
/**
 * @package     homelane
 * @author      Codilar Technologies
 * @link        https://www.codilar.com/
 * @copyright © 2021 Codilar Technologies Pvt. Ltd.. All rights reserved.
 */

namespace Codilar\Customer\Block;

use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context;
use Magento\Framework\View\Element\Template;

class CustomerData extends Template
{
    protected $_customerSession;
    /**
     * @var Context
     */
    protected Context $httpContext;

    /**
     * @param Template\Context $context
     * @param Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Request\Http               $request,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\SessionFactory            $customerSession,
        Template\Context                                  $context,
        Context                                           $httpContext,
        array                                             $data = []
    )
    {
        $this->request = $request;
        $this->customerRepository = $customerRepository;
        $this->_customerSession = $customerSession;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    public function getCustomerId()
    {
        $customer = $this->_customerSession->create();
        return $customer->getCustomer()->getId();
    }

    public function getCustomerIsLoggedIn(): bool
    {
        return (bool)$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    public function getCustomerName()
    {
        return $this->httpContext->getValue('customer_name');
    }

    public function getCustomerEmail()
    {
        return $this->httpContext->getValue('customer_email');
    }

     public function getCustomerId()
    {
        return $this->httpContext->getValue('customer_id');
    }



    public function customerName()
    {
        $customer = $this->_customerSession->create();
        return $customer->getCustomer()->getName();
    }

    public function customerEmail()
    {
        $customer = $this->_customerSession->create();
        return $customer->getCustomer()->getEmail();
    }

}
