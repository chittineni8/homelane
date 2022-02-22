<?php
/**
 * Copyright © 2017 BORN . All rights reserved.
 */
namespace Codilar\Vendor\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class SerializedVendorMapping implements ObserverInterface
{
    const ATTR_ATTRACTION_HIGHLIGHTS_CODE = 'vendor_mapping';

    /**
     * @var  \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Constructor
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->request = $request;
    }

    public function execute(Observer $observer)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getDataObject();
        $post = $this->request->getPost();
        $post = $post['product'];
        $highlights = isset($post[self::ATTR_ATTRACTION_HIGHLIGHTS_CODE]) ? $post[self::ATTR_ATTRACTION_HIGHLIGHTS_CODE] : '';
        $product->setVendorMapping($highlights);
        $requiredParams = ['automat_vendor_id'];
        if (is_array($highlights)) {
            $highlights = $this->removeEmptyArray($highlights, $requiredParams);
            $product->setVendorMapping(json_encode($highlights));
            //print_r($highlights);
        }

    }

    /**
    * Function to remove empty array from the multi dimensional array
    *
    * @return Array
    */
    private function removeEmptyArray($attractionData, $requiredParams){

        $requiredParams = array_combine($requiredParams, $requiredParams);
        $reqCount = count($requiredParams);

        foreach ($attractionData as $key => $values) {
            $values = array_filter($values);
            $inersectCount = count(array_intersect_key($values, $requiredParams));
            if ($reqCount != $inersectCount) {
                unset($attractionData[$key]);
            }
        }
        return $attractionData;
    }
}
