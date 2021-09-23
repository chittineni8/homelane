<?php

namespace Codilar\QueryForm\Controller\Query;

use Codilar\QueryForm\Model\Query;
use Codilar\QueryForm\Model\ResourceModel\Query as QueryResourceModel;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Save extends Action
{

    private $query;

    private $queryResourceModel;


    public function __construct(
        Context $context,
        Query $query,
        QueryResourceModel $queryResourceModel
    ) {
        $this->query = $query;
        $this->queryResourceModel = $queryResourceModel;
        parent::__construct($context);
    }
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $query = $this->query->setData($params);
        try {
            $this->queryResourceModel->save($query);
            // created custom event:- form_submit_event
            $this->_eventManager->dispatch('form_submit_event');
            $this->messageManager->addSuccessMessage(__("Your Query added Successfully,We will reach You Soon !!!"));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("Something went wrong"));
        }

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setUrl($this->_redirect->getRefererUrl());
        return $redirect;
    }
}
