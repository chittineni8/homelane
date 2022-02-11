<?php

namespace   Codilar\WareHouseMapping\Controller\Adminhtml\WareHouseMapping;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class Add extends Action
{
    /**
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */

    public function execute()
    {
        $resultResponse = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultResponse->getConfig()->getTitle()->set(__(" Add Data"));
        return $resultResponse;
    }
}

