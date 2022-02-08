<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Model\Product\Attribute\Source;

class SkuType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
        ['value' => 'traded goods', 'label' => __('Traded Goods')],
        ['value' => 'services', 'label' => __('Services')]
        ];
        return $this->_options;
    }
}

