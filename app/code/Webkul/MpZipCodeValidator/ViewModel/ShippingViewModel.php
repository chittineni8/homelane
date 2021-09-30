<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpZipCodeValidator
 * @author    Webkul <support@webkul.com>
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\MpZipCodeValidator\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class ShippingViewModel implements ArgumentInterface
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Get json helper object
     *
     * @return \Magento\Framework\Json\Helper\Data
     */
    public function getJsonHelper() : \Magento\Framework\Json\Helper\Data
    {
        return $this->jsonHelper;
    }
}
