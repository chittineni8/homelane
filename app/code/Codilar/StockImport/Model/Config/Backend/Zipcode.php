<?php
 
namespace Codilar\StockImport\Model\Config\Backend;
 
class Zipcode extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * @return string[]
     */
    public function getAllowedExtensions() {
        return ['csv', 'xls'];
    }
}