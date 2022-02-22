<?php

namespace Codilar\PriceSorter\Plugin\Catalog\Model;

class Config
{
    public function afterGetAttributeUsedForSortByArray(
        \Magento\Catalog\Model\Config $catalogConfig,
                                      $options
    )
    {
        unset($options['price']);
        $options['position'] = __('Popularity');
        $options['low_to_high'] = __('Price - Low To High');
        $options['high_to_low'] = __('Price - High To Low');
        return $options;

    }

}
