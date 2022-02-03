<?php
namespace Codilar\AttributeSet\Model\ResourceModel\Attributeset;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
             \Codilar\AttributeSet\Model\Attributeset::class,
            \Codilar\AttributeSet\Model\ResourceModel\Attributeset::class
        );

    }
}
