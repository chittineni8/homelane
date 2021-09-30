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
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;

class Index extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $url;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\RegionFactory
     */
    protected $_region;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Webkul\Marketplace\Helper\Data $helper
     * @param \Magento\Customer\Model\Url $url
     * @param \Webkul\MpZipCodeValidator\Model\RegionFactory $region
     * @param \Magento\Backend\Model\Session $session
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\Marketplace\Helper\Data $helper,
        \Magento\Customer\Model\Url $url,
        \Webkul\MpZipCodeValidator\Model\RegionFactory $region,
        \Magento\Backend\Model\Session $session
    ) {
        $this->_region = $region;
        $this->helper  = $helper;
        $this->url = $url;
        $this->_customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $session;
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
     * Add Store Page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $isPartner = $this->helper->isSeller();
        if ($isPartner == 1) {
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            if ($this->helper->getIsSeparatePanel()) {
                $resultPage->addHandle('mpzipcodevalidator_layout2_zipcode_index');
            }
            $regionId = $this->getRequest()->getParam('id');
            $this->session->setRegionIds('');
            $this->session->setRegionIds($regionId);
            if ($regionName = $this->_region->create()->load($regionId)->getRegionName()) {
                $resultPage->getConfig()->getTitle()->set(__('%1 Zipcode List', $regionName));
            } else {
                $this->messageManager->addError(__('Region does not exists.'));
                return $this->resultRedirectFactory->create()->setPath(
                    'mpzipcodevalidator/region/view',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
            return $resultPage;
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/becomeseller',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
