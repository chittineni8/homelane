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
namespace Webkul\MpZipCodeValidator\Controller\Zipcode;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;

class Delete extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var Webkul\MpZipCodeValidator\Model\RegionFactory
     */
    protected $_region;

    /**
     * @var Webkul\MpZipCodeValidator\Model\ZipcodeFactory
     */
    protected $_zipcode;

    /**
     * @var \Webkul\Marketplace\Helper\Data $helper
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Url $url
     */
    protected $url;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Webkul\Marketplace\Helper\Data $helper
     * @param \Magento\Customer\Model\Url $url
     * @param \Webkul\MpZipCodeValidator\Model\ZipcodeFactory $zipcode
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\Marketplace\Helper\Data $helper,
        \Magento\Customer\Model\Url $url,
        \Webkul\MpZipCodeValidator\Model\ZipcodeFactory $zipcode,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_customerSession = $customerSession;
        $this->_zipcode = $zipcode;
        $this->url = $url;
        $this->helper = $helper;
        $this->formKey = $formKey;
        $this->request = $request;
        $this->request->setParam('form_key', $this->formKey->getFormKey());
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

        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Mass Region Delete
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $isPartner = $this->helper->isSeller();
        if ($isPartner == 1) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $data = $this->getRequest()->getParams('form_key');
            foreach ($data['wk-delete'] as $key => $id) {
                $regionId = $this->deleteZipcode($id);
            }
            $this->messageManager->addSuccess(__('Zipcode deleted successfully'));
            return $resultRedirect->setPath('*/zipcode/index/id/'.$regionId);
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/becomeseller',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }

    /**
     * Delete Zipcodes
     *
     * @param Integer $id
     * @return void
     */
    public function deleteZipcode($id)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $regionId = 0;
            $collection = $this->_zipcode->create()->getCollection();
            $collection->addFieldToFilter('id', ['eq' => $id]);
            foreach ($collection as $model) {
                $regionId = $model->getRegionId();
                $model->delete();
            }
            return $regionId;
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "Controller_ZipCode_Delete deleteZipCode : ".$e->getMessage()
            );
            $this->messageManager->addError(__('Something went wrong'));
            return $resultRedirect->setPath('*/region/view');
        }
    }
}
