<?php
namespace Codilar\TokenAPI\Block;

use Magento\Store\Model\StoreManagerInterface;

class ResetPassword extends \Magento\Framework\View\Element\Template
{
     /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

 public function __construct(
        StoreManagerInterface $storeManager
  )
    
    {   $this->storeManager = $storeManager;


    }//end __construct()
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

public function getFormAction()
    {
        return $this->getUrl('user/resetpassword/submit', ['_secure' => true]);
    }

 public function getBaseUrl()
    {
        return  $storeUrl = $this->storeManager->getStore()->getBaseUrl();
    }

    
}