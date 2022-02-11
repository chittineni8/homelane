<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Controller\Adminhtml\Priceversion;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    /**
    * @var \Codilar\Priceversion\Model\PriceversionDetailsFactory
    */
    protected $_priceverisondetailsFactory;

    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Codilar\Priceversion\Model\PriceversiondetailsFactory $priceverisondetailsFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_priceverisondetailsFactory = $priceverisondetailsFactory;
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
        //print_r($data);die
        $data['sub_cat'] = is_array($data['sub_cat']) ? array_filter($data['sub_cat'], fn($value) => !is_null($value) && $value !== ''): $data['sub_cat'];
        $data['sub_cat'] = is_array($data['sub_cat']) ? implode(",", $data['sub_cat']): $data['sub_cat'];
        if ($data) {
            $id = $this->getRequest()->getParam('priceversion_id');

            $model = $this->_objectManager->create(\Codilar\Priceversion\Model\Priceversion::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Priceversion no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
              $model->setData($data);

            try {
                $model->save();
                $versions = $this->_priceverisondetailsFactory->create()->getCollection()->getData();
                $details = array();
                $skus = array();
                if(isset($data['copy_from_version_id']) && $data['copy_from_version_id'] !='') {
                  foreach ($versions as $value) {
                      if($id == $value['price_version_id']){
                        $skus[] =  $value['sku'];
                      }
                  }
                  foreach($versions as $version) {
                    if($version['price_version_id'] == $data['copy_from_version_id']) {
                        if(!in_array($version['sku'], $skus)){
                          $version['price_version_id'] = $model->getId();
                          unset($version['priceversiondetails_id']);
                          $details[] = $version;
                        }

                    }
                  }
                }


                //echo "<pre>";print_r($details);die;
                if(!empty($details)){
                  $myModel = $this->_priceverisondetailsFactory->create();

                  // Inserting data using for loop
                  foreach ($details as $detail) {
                    $myModel->addData($detail);
                    $myModel->save();
                    $myModel->unsetData(); // this line is necessary to save multiple records
                  }
                }
                if(isset($data['status'])){
                  $versions = $this->_priceverisondetailsFactory->create()->getCollection()->getData();
                  $details = array();
                    foreach($versions as $version) {
                          $version['status'] = $data['status'];
                          //unset($version['priceversiondetails_id']);
                          $details[] = $version;

                    }

                }
                if(!empty($details)){
                  $myModel = $this->_priceverisondetailsFactory->create();

                  // Inserting data using for loop
                  foreach ($details as $detail) {
                    $myModel->addData($detail);
                    $myModel->save();
                    $myModel->unsetData(); // this line is necessary to save multiple records
                  }
                }
                $this->messageManager->addSuccessMessage(__('You saved the Priceversion.'));
                $this->dataPersistor->clear('codilar_priceversion_priceversion');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['priceversion_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Priceversion.'));
            }

            $this->dataPersistor->set('codilar_priceversion_priceversion', $data);
            return $resultRedirect->setPath('*/*/edit', ['priceversion_id' => $this->getRequest()->getParam('priceversion_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
