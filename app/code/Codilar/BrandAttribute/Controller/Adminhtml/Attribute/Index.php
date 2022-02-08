<?php

namespace Codilar\BrandAttribute\Controller\Adminhtml\Attribute;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * Simplify Import Classes,  Remove extra line spaces
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultResponse = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultResponse->getConfig()->getTitle()->set(__(" Brand Mapping "));
        return $resultResponse;
    }
}

