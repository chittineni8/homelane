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
use Magento\Framework\App\RequestInterface;

class Massaction extends Action
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
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Url
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
     * @var \Webkul\MpZipCodeValidator\Helper\Data
     */
    protected $zipHelper;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Webkul\MpZipCodeValidator\Model\RegionFactory $region
     * @param \Webkul\MpZipCodeValidator\Model\ZipcodeFactory $zipcode
     * @param \Webkul\Marketplace\Helper\Data $helper
     * @param \Magento\Customer\Model\Url $url
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\MpZipCodeValidator\Model\RegionFactory $region,
        \Webkul\MpZipCodeValidator\Model\ZipcodeFactory $zipcode,
        \Webkul\Marketplace\Helper\Data $helper,
        \Magento\Customer\Model\Url $url,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Request\Http $request,
        \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
    ) {
        $this->_customerSession = $customerSession;
        $this->_region = $region;
        $this->_zipcode = $zipcode;
        $this->helper = $helper;
        $this->url = $url;
        $this->request = $request;
        $this->formKey = $formKey;
        $this->request->setParam('form_key', $this->formKey->getFormKey());
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
        try {
            if ($isPartner == 1) {
                $resultRedirect = $this->resultRedirectFactory->create();
                $data = $this->getRequest()->getParams();
                $type = $data['action'];
                foreach ($data['wk-delete'] as $id) {
                    if ($type == 2) {
                        $this->enableRegion($id);
                    } elseif ($type == 3) {
                        $this->disableRegion($id);
                    } elseif ($type == 4) {
                        $this->deleteRegion($id);
                    } else {
                        $this->messageManager->addSuccess(__('Something went wrong.'));
                        return $resultRedirect->setPath('*/*/view');
                    }
                }
                if ($type == 2) {
                    $this->messageManager->addSuccess(__('Regions enabled successfully'));
                } elseif ($type == 3) {
                    $this->messageManager->addSuccess(__('Regions disabled successfully'));
                } elseif ($type == 4) {
                    $this->messageManager->addSuccess(__('Regions deleted successfully'));
                }
                return $resultRedirect->setPath('*/*/view');
            } else {
                return $this->resultRedirectFactory->create()->setPath(
                    'marketplace/account/becomeseller',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "Controller_Region_MassAction execute : ".$e->getMessage()
            );
        }
    }

    /**
     * Delete Region
     *
     * @param int $id
     * @return void
     */
    public function deleteRegion($id)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->_region->create()->load($id)->delete();
            $zipCollections = $this->_zipcode->create()->getCollection()
                ->addFieldToFilter('region_id', $id);
            foreach ($zipCollections as $zipcode) {
                $this->deleteRegionZipcode($zipcode->getId());
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Something went wrong.'));
            return $resultRedirect->setPath('*/*/view');
        }
    }

    /**
     * Delete Region Zipcodes
     *
     * @param Integer $id
     * @return void
     */
    public function deleteRegionZipcode($id)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->_zipcode->create()->load($id)->delete();
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Something went wrong'));
            return $resultRedirect->setPath('*/*/view');
        }
    }

    /**
     * Enable Region
     *
     * @param  int $id
     * @return object
     */
    public function enableRegion($id)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->_region->create()->load($id)->setStatus(1)->save();
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Something went wrong.'));
            return $resultRedirect->setPath('*/*/view');
        }
    }

    /**
     * Disable Region
     *
     * @param  int $id
     * @return object
     */
    public function disableRegion($id)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->_region->create()->load($id)->setStatus(0)->save();
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Something went wrong.'));
            return $resultRedirect->setPath('*/*/view');
        }
    }
}
