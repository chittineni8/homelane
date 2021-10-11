<?php
namespace Codilar\TokenAPI\Block;
class Otp extends \Magento\Framework\View\Element\Template
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

/**
     * Retrieve the form posting URL
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('phpcuong/customer_ajax/register');
    }

    
}