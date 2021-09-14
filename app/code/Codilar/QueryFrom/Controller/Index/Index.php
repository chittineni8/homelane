<?php

namespace Codilar\QueryFrom\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public function execute()
    {
        $this->_view->getPage()->getConfig()->getTitle()->set(__("Queries Form"));
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}

