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
        Template\Context $context,
        Context $httpContext,
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
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
}
