<?php
namespace Codilar\AttributeSet\Model\ResourceModel;
class Attributeset extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'codilar_erp_attributeset';
    const ID_FIELD_NAME = 'id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init("codilar_erp_attributeset","id");   //here id is the primary key of custom table
    }
}
