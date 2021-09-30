<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpZipCodeValidator
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpZipCodeValidator\Controller\Region;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\App\RequestInterface;

class Massdelete extends Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\Region
     */
    protected $region;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $zipcodeCollection;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $url;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Webkul\MpZipCodeValidator\Helper\Data
     */
    protected $zipHelper;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param \Webkul\MpZipCodeValidator\Model\Region $region
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $url
     * @param \Webkul\Marketplace\Helper\Data $helper
     * @param \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \Webkul\MpZipCodeValidator\Model\Region $region,
        CollectionFactory $collectionFactory,
        \Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $url,
        \Webkul\Marketplace\Helper\Data $helper,
        \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
    ) {
        $this->_filter = $filter;
        $this->region = $region;
        $this->_collectionFactory = $collectionFactory;
        $this->zipcodeCollection = $zipcodeCollection;
        $this->formKey = $formKey;
        $this->request = $request;
        $this->request->setParam('form_key', $this->formKey->getFormKey());
        $this->customerSession = $customerSession;
        $this->url = $url;
        $this->helper = $helper;
        $this->zipHelper = $zipHelper;
        parent::__construct($context);
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->url->getLoginUrl();

        if (!$this->customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $isPartner = $this->helper->isSeller();
            if ($isPartner == 1) {
                $collection = $this->_filter->getCollection($this->_collectionFactory->create());
                foreach ($collection as $region) {
                    $zipcodeCollection = $this->zipcodeCollection->create()
                        ->addFieldToFilter('region_id', ['eq' => $region->getId()]);
                    if ($zipcodeCollection->getSize()) {
                        $zipcodeCollection->walk('delete');
                    }
                    $collection->walk('delete');
                }
                $this->messageManager->addSuccess(__('Region(s) deleted succesfully'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/view');
            } else {
                return $this->resultRedirectFactory->create()->setPath(
                    'marketplace/account/becomeseller',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "Controller_Region_Delete execute : ".$e->getMessage()
            );
        }
    }
}
