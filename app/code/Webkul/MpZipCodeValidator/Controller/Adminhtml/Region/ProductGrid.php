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
namespace Webkul\MpZipCodeValidator\Controller\Adminhtml\Region;

class ProductGrid extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\Region
     */
    protected $_regionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context                       $context
     * @param \Magento\Framework\View\Result\LayoutFactory              $resultLayoutFactory
     * @param \Magento\Framework\Registry                               $registory
     * @param \Magento\Backend\Model\Session                            $session
     * @param \Webkul\MpZipCodeValidator\Model\RegionFactory            $regionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Registry $registory,
        \Magento\Backend\Model\Session $session,
        \Webkul\MpZipCodeValidator\Model\RegionFactory $regionFactory
    ) {
        parent::__construct($context);
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_registory = $registory;
        $this_session = $session;
        $this->_regionFactory = $regionFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $region = $this->_regionFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $region->load($this->getRequest()->getParam('id'));
        }
        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $region->setData($data);
        }
        $this->_registory->register('mpzipcodevalidator', $region);
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock(
            'webkul_mpzipcodevalidator_region_edit_tab_products'
        );
        return $resultLayout;
    }
}
