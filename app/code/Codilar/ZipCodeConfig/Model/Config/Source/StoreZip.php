<?php

namespace Codilar\ZipCodeConfig\Model\Config\Source;

//use Magento\Eav\Api\AttributeRepositoryInterface;
use Webkul\MpZipCodeValidator\Model\Config\Source\RegionOptions;
use Magento\Framework\Data\OptionSourceInterface;

class StoreZip
{
    protected $regionoptions;


    public function __construct(
        RegionOptions $regionoptions
    ) {

        $this->regionoptions = $regionoptions;
    }


    public function toOptionArray()
    {

        $abc = $this->regionoptions->getAllOptions();
//echo "<pre>";
//        print_r($abc);
//        die('dddd');
    }

}
