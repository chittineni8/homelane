<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Model\Priceversion;
class Category implements \Magento\Framework\Option\ArrayInterface
{
    protected $_categories;

    public function __construct(\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collection)
    {
        $this->_categories = $collection;
    }

    public function toOptionArray()
    {

        $collection = $this->_categories->create();
        $collection->addAttributeToSelect('*');
        $itemArray = array('value' => '', 'label' => '--Please Select--');
        $options = [];
        $options = $itemArray;
        foreach ($collection as $category) {
            $options[] = ['value' => $category->getId(), 'label' => $category->getName()];
        }
        return $options;
    }
}
