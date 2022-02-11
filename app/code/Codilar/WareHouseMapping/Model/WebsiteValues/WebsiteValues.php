<?php

namespace Codilar\WareHouseMapping\Model\WebsiteValues;

use \Magento\Store\Model\ResourceModel\Website\CollectionFactory;

class WebsiteValues implements \Magento\Framework\Option\ArrayInterface

{
    /** @var CollectionFactory */

    protected CollectionFactory $websiteCollectionFactory;

    /** @var array */
    protected $items;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory  $websiteCollectionFactory)
    {
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
    }

    public function toOptionArray(): array
    {
        if (is_null($this->items)) {
            $this->items = $this->getWebsites();
        }
        return $this->items;
    }

    public function getWebsites(): array
    {
        $collection = $this->_websiteCollectionFactory->create();
        $options =[];
        foreach($collection as $website){
            $options[] = ['label' =>$website->getName(),
                          'value' =>$website->getCode()
            ];
        }
        return $options;
    }
}
