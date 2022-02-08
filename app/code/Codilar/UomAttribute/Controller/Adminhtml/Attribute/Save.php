<?php

namespace Codilar\UomAttribute\Controller\Adminhtml\Attribute;

use Codilar\UomAttribute\Api\UomAttributeRepositoryInterface;
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
    private $uomAttributeRepository;
    private $manager;

    /**
     * Save constructor.
     * @param ResultFactory $resultFactory
     * @param RequestInterface $request
     * @param UomAttributeRepositoryInterface $uomAttributeRepository
     * @param ManagerInterface $manager
     * @param UrlInterface $url
     */
    public function __construct(
        ResultFactory $resultFactory,
        RequestInterface $request,
        UomAttributeRepositoryInterface $uomAttributeRepository,
        ManagerInterface $manager,
        UrlInterface $url
    ) {
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->url = $url;
        $this->uomAttributeRepository = $uomAttributeRepository;
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
            $model = $this->uomAttributeRepository->load($this->request->getParam('id'));
            $model->setData($this->request->getParams());
            $this->uomAttributeRepository->save($model);
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
