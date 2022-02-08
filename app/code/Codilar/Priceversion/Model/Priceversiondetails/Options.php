<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Model\Priceversiondetails;
class Options implements \Magento\Framework\Option\ArrayInterface
{
  /**
* @var \Magento\Store\Model\WebsiteFactory
*/
protected $_priceverisonFactory;

public function __construct(
  \Codilar\Priceversion\Model\PriceversionFactory $priceverisonFactory,
  array $data = []
) {
  $this->_priceverisonFactory = $priceverisonFactory;
//  parent::__construct($context, $registry, $formFactory, $data);
}
public function toOptionArray()
{

    $versions = $this->_priceverisonFactory->create()->getCollection()->getData();
    $data = array();
    foreach($versions as $version){
      $data[] = array('label'=> $version['version_label'], 'value' => $version['priceversion_id']);
    }
    return $data;
    //print_r($websites);
}
}
