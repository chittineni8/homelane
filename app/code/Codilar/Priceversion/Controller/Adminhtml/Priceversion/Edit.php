<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Controller\Adminhtml\Priceversion;

class Edit extends \Codilar\Priceversion\Controller\Adminhtml\Priceversion
{

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('priceversion_id');
        $model = $this->_objectManager->create(\Codilar\Priceversion\Model\Priceversion::class);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Priceversion no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        //print_r($model->getData());
        //$model->getData()['sub_cat'] = explode(',',$model->getData()['sub_cat']);
        $this->_coreRegistry->register('codilar_priceversion_priceversion', $model);

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Priceversion') : __('New Priceversion'),
            $id ? __('Edit Priceversion') : __('New Priceversion')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Priceversions'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Priceversion %1', $model->getId()) : __('New Priceversion'));
        return $resultPage;
    }
}
