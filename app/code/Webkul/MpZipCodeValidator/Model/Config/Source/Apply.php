<?php
/**
 * @category  Webkul
 * @package   Webkul_MpZipCodeValidator
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpZipCodeValidator\Model\Config\Source;

class Apply implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * ToOptionArray
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('Apply to Individual Products')],
            ['value' => '1', 'label' => __('Apply to all Products')]
        ];
    }
}
