<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Model\Priceversion;
class Options implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value'=>1,
                'label'=>"My Option"
            ],
            [
                'value'=>2,
                'label'=>"My Option 2"
            ],
            [
                'value'=>3,
                'label'=>"My Option 3"
            ],
            [
                'value'=>4,
                'label'=>"My Option 4"
            ],
            [
                'value'=>5,
                'label'=>"My Option 5"
            ]
        ];
    }
}
