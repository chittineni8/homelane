<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Controller\Adminhtml\Priceversiondetails;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    protected $_productRepository;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            //echo "<prer>";print_r($data);die;
            if(isset($data['sku']) && $data['sku']!=""){
              try {
                  $product = $this->_productRepository->get($data['sku']);
                  $skutype = $product->getSkuTypes();

                  if($skutype != 2){
                    $skutype = false;
                  }
              } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                  $skutype = false;
              }
            }else{
                $skutype = false;
            }
            if($skutype == false){
              $this->messageManager->addErrorMessage(__('This SKU is not found or it is not the Servicable Product.'));
              return $resultRedirect->setPath('*/*/');
            }
            $id = $this->getRequest()->getParam('priceversiondetails_id');

            $model = $this->_objectManager->create(\Codilar\Priceversion\Model\Priceversiondetails::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Priceversiondetails no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Priceversiondetails.'));
                $this->dataPersistor->clear('codilar_priceversion_priceversiondetails');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['priceversiondetails_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Priceversiondetails.'));
            }

            $this->dataPersistor->set('codilar_priceversion_priceversiondetails', $data);
            return $resultRedirect->setPath('*/*/edit', ['priceversiondetails_id' => $this->getRequest()->getParam('priceversiondetails_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
