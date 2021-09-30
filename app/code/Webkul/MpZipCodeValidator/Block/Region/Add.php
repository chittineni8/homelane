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
namespace Webkul\MpZipCodeValidator\Block\Region;

class Add extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory
     */
    protected $_region;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $_mpHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context                                $context
     * @param Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory $region
     * @param \Webkul\Marketplace\Helper\Data                                        $mpHelper
     * @param array                                                                  $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Webkul\MpZipCodeValidator\Model\RegionFactory $region,
        \Webkul\Marketplace\Helper\Data $mpHelper,
        array $data = []
    ) {
        $this->_region = $region;
        $this->_mpHelper = $mpHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get region
     *
     * @return Webkul\MpZipCodeValidator\Model\RegionFactory
     */
    public function getRegion()
    {
        $data = $this->getRequest()->getParams();
        if (isset($data['id'])) {
            return $this->_region
                ->create()
                ->load($data['id']);
        } else {
            return '';
        }
    }

    /**
     * Get Helper Object
     *
     * @return object
     */
    public function getMpHelper() : \Webkul\Marketplace\Helper\Data
    {
        return $this->_mpHelper;
    }
}
