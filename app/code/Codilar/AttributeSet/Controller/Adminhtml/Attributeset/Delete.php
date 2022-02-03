<?php

namespace Codilar\AttributeSet\Controller\Adminhtml\Attributeset;

use Codilar\AttributeSet\Api\AttributesetRepositoryInterface;
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
     * @param AttributesetRepositoryInterface $AttributesetRepository
     * @param ManagerInterface $manager
     * @param UrlInterface $url
     */
    public function __construct(
        ResultFactory                   $resultFactory,
        RequestInterface                $request,
        AttributesetRepositoryInterface $attributesetRepository,
        ManagerInterface                $manager,
        UrlInterface                    $url
    ) {
        $this->resultFactory = $resultFactory;
        $this->attributesetRepository = $attributesetRepository;
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
        $redirectResponse->setUrl($this->url->getUrl('codilar_attributeset/attributeset/index'));
        $result = $this->attributesetRepository->deleteById($this->request->getParam('id'));
        if($result) {
            $this->manager->addSuccessMessage(
                __(sprintf(
                        'The  AttributeSet with id %s has been deleted Successfully',
                        $this->request->getParam('id')
                    )
                )
            );
        } else {
            $this->manager->addErrorMessage(
                __(sprintf(
                        'The AttributeSet with id %s has not been deleted, Due to some technical reasons',
                        $this->request->getParam('id')
                    )
                )
            );
        }
        return $redirectResponse;
    }
}
