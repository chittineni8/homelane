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
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;

class CustomerData extends Template
{
    /**
     * @var Context
     */
    protected Context $httpContext;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $url;

    /**
     * @param Template\Context $context
     * @param Context $httpContext
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Context $httpContext,
        Session $customerSession,
        UrlInterface $url,
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->url = $url;
    }

    public function getCustomerDetails()
    {
        $customer = $this->customerSession;
        if ($customer->isLoggedIn()) {
            return $customer->getCustomer();
        }
    }

    public function getCustomerIsLoggedIn(): bool
    {
        return (bool)$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

}
