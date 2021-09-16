<?php
/**
 * View.php
 *
 * @package     Homelane
 * @description Store Module which contains store switching functionality
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * Store Module which contains store switching functionality
 */
namespace Codilar\Store\Block;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class View
 *
 * @package     Homelane
 * @description Block class for checking customer's session
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * Block class for checking customer's session
 */
class View extends Template
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * ModalOverlay constructor.
     * @param Session $customerSession
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Session $customerSession,
        Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
    }

    /**
     * @return Customer|void
     */
    public function getCustomerDetails()
    {
        $customer = $this->customerSession;
        if ($customer->isLoggedIn()) {
            return $customer->getCustomer();
        }
    }
}
