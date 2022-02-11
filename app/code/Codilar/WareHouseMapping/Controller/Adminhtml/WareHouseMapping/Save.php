<?php

namespace Codilar\WareHouseMapping\Controller\Adminhtml\WareHouseMapping;

use Codilar\WareHouseMapping\Api\WareHouseMappingRepositoryInterface;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;



class Save implements ActionInterface
{
    private $resultFactory;
    private $request;
    private $url;
    private $wareHouseMappingRepository;
    private $manager;

    /**
     * Save constructor.
     * @param ResultFactory $resultFactory
     * @param RequestInterface $request
     * @param WareHouseMappingRepositoryInterface $wareHouseMappingRepository
     * @param ManagerInterface $manager
     * @param UrlInterface $url
     */
    public function __construct(
        ResultFactory $resultFactory,
        RequestInterface $request,
        WareHouseMappingRepositoryInterface $wareHouseMappingRepository,
        ManagerInterface $manager,
        UrlInterface $url
    ) {
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->url = $url;
        $this->wareHouseMappingRepository = $wareHouseMappingRepository;
        $this->manager = $manager;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $redirectResponse = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirectResponse->setUrl($this->url->getUrl('*/*/index'));
        try{
            $model = $this->wareHouseMappingRepository->load($this->request->getParam('id'));
            $model->setData($this->request->getParams());
            $this->wareHouseMappingRepository->save($model);
            $this->manager->addSuccessMessage(
                __(sprintf(
                    'Data %s has been saved Successfully',
                    $this->request->getParam('name')
                    )
                )
            );
        } catch (\Exception $exception) {
            $this->manager->addErrorMessage(
                __(sprintf(
                        'Data %s has not been saved due to Some Technical Reason',
                        $this->request->getParam('name')
                    )
                )
            );
        }
        return $redirectResponse;
    }
}
