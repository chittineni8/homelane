<?php

namespace Codilar\WareHouseMapping\Controller\Adminhtml\WareHouseMapping;

use Codilar\WareHouseMapping\Api\WareHouseMappingRepositoryInterface;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ActionInterface;


class Delete implements ActionInterface
{
    private $resultFactory;
    private $request;
    private $url;
    private $manager;

    /**
     * Index constructor.
     * @param ResultFactory $resultFactory
     * @param RequestInterface $request
     * @param WareHouseMappingRepositoryInterface $wareHouseMappingRepository
     * @param ManagerInterface $manager
     * @param UrlInterface $url
     */
    public function __construct(
        ResultFactory                   $resultFactory,
        RequestInterface                $request,
        WareHouseMappingRepositoryInterface $wareHouseMappingRepository,
        ManagerInterface                $manager,
        UrlInterface                    $url
    )
    {
        $this->resultFactory = $resultFactory;
        $this->wareHouseMappingRepository = $wareHouseMappingRepository;
        $this->request = $request;
        $this->url = $url;
        $this->manager = $manager;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $redirectResponse = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirectResponse->setUrl($this->url->getUrl('warehousemapping/warehousemapping/index'));
        $result = $this->wareHouseMappingRepository->deleteById($this->request->getParam('id'));
        if ($result) {
            $this->manager->addSuccessMessage(
                __(sprintf(
                        'The  Data with id %s has been deleted Successfully',
                        $this->request->getParam('id')
                    )
                )
            );
        } else {
            $this->manager->addErrorMessage(
                __(sprintf(
                        'The Attribute with id %s has not been deleted, Due to some technical reasons',
                        $this->request->getParam('id')
                    )
                )
            );
        }
        return $redirectResponse;
    }
}
