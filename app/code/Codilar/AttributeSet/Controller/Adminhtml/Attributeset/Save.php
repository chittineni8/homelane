<?php

namespace Codilar\AttributeSet\Controller\Adminhtml\Attributeset;

use Codilar\AttributeSet\Api\AttributesetRepositoryInterface;
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
    private $attributesetRepository;
    private $manager;

    /**
     * Save constructor.
     * @param ResultFactory $resultFactory
     * @param RequestInterface $request
     * @param AttributesetRepositoryInterface $attributesetRepository
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
        $this->request = $request;
        $this->url = $url;
        $this->attributesetRepository = $attributesetRepository;
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
            $model = $this->attributesetRepository->load($this->request->getParam('id'));
            $model->setData($this->request->getParams());
            $this->attributesetRepository->save($model);
            $this->manager->addSuccessMessage(
                __(sprintf(
                        'The attributeset %s Information has been saved Successfully',
                        $this->request->getParam('attribute_set_name')
                    )
                )
            );
        } catch (\Exception $exception) {
            $this->manager->addErrorMessage(
                __(sprintf(
                        'The attributeset %s Information has not been saved due to Some Technical Reason',
                        $this->request->getParam('attribute_set_name')
                    )
                )
            );
        }
        return $redirectResponse;
    }
}
