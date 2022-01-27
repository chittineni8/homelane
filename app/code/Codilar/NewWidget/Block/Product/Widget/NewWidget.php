<?php

namespace Codilar\NewWidget\Block\Product\Widget;

class NewWidget extends \Magento\Catalog\Block\Product\Widget\NewWidget
{

    public function __construct(
        \Magento\Catalog\Block\Product\Context                         $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility                      $catalogProductVisibility,
        \Magento\Framework\App\Http\Context                            $httpContext,
        array                                                          $data = [],
        \Magento\Framework\Serialize\Serializer\Json                   $serializer = null
    )
    {
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $data
        );
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    public function getCacheKeyInfo()
    {
        $id = $this->getData('category');
        $catid = str_replace("category/", "", $id);
        return
            [
                $this->getDisplayType(),
                $this->getProductsPerPage(),
                (int)$this->getRequest()->getParam($this->getData('page_var_name'), 1),
                $this->getProductsPerPage(),
                $catid,
                $this->serializer->serialize($this->getRequest()->getParams())
            ];

    }
}

?>
