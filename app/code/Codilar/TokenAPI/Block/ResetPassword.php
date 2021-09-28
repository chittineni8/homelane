<?php
namespace Codilar\TokenAPI\Block;
class ResetPassword extends \Magento\Framework\View\Element\Template
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

public function getFormAction()
    {
        return $this->getUrl('user/resetpassword/submit', ['_secure' => true]);
    }

    
}