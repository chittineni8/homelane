<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */
 
namespace Magedelight\SMSProfile\Model\Config\Source;

class SMSProfileListAddressOptions implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'shipping_add', 'label' => __('Default Shipping Address')],
            ['value' => 'billing_add', 'label' => __('Default Billing Address')],
            ['value' => 'first_add', 'label' => __('First Address')],
            ['value' => 'last_add', 'label' => __('Last Address')],
        ];
    }
}
