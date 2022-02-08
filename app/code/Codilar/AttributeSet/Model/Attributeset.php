<?php
namespace Codilar\AttributeSet\Model;

use Magento\Framework\Model\AbstractModel;
use Codilar\AttributeSet\Model\ResourceModel\Attributeset as ResourceModel;


class Attributeset extends AbstractModel
{

    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
