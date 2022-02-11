<?php
declare(strict_types=1);

namespace Codilar\CategoryInfoAPI\Model;

use Codilar\CategoryInfoAPI\Api\CategoryInfoManagementInterface;
use Codilar\MiscAPI\Logger\Logger;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Layer\FilterListFactory;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\CatalogGraphQl\Model\Resolver\Layer\FilterableAttributesListFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Store\Model\StoreManagerInterface;

class CategoryInfoManagement implements CategoryInfoManagementInterface
{
    protected $_request;
    protected $_filterableAttributeList;
    protected $_layerResolver;
    protected $_filterList;
    protected $_storeManagerInterface;
    protected $_response;
    protected $_redirFactory;
    protected $_categoryFactory;
    protected $logger;

    /**
     * @param CategoryFactory $categoryFactory
     * @param CollectionFactory $productAttributeCollectionFactory
     * @param FilterableAttributesListFactory $filterableAttributeList
     * @param FilterListFactory $filterList
     * @param StoreManagerInterface $storeManagerInterface
     * @param Resolver $layerResolver
     * @param Request $request
     * @param Logger $logger
     */
    public function __construct(
        CategoryFactory                 $categoryFactory,
        CollectionFactory               $productAttributeCollectionFactory,
        FilterableAttributesListFactory $filterableAttributeList,
        FilterListFactory               $filterList,
        StoreManagerInterface           $storeManagerInterface,
        Resolver                        $layerResolver,
        Request                         $request,
        Logger                          $logger


    )
    {

        $this->_categoryFactory = $categoryFactory;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->_filterList = $filterList;
        $this->_filterableAttributeList = $filterableAttributeList;
        $this->_layerResolver = $layerResolver;
        $this->_request = $request;
        $this->logger = $logger;
        $this->_storeManagerInterface = $storeManagerInterface;


    }

    /**
     * @param int $id
     * @return mixed|void
     */
    public function getCategoryInfo($id)
    {
        try {
            $categoryData = $this->_categoryFactory->create()->load($id);
            $categoryId = $id;
            $layer = $this->_layerResolver->get();


            $layerType = "search";
            if ($categoryId) {
                $layer->setCurrentCategory($categoryId);
                $layerType = "category";
            }

//        $filterArray['store_id'] = $this->_storeManagerInterface->getStore()->getId();
            $filterArray['cat_custom_attribute'] = array();
            $filterArray['room_type'] = array();
            if($categoryData->getData('cat_custom_attribute') !=null){
                $filterArray['cat_custom_attribute'] = explode(",",$categoryData->getData('cat_custom_attribute'));
            }
            if($categoryData->getData('room_type') != null){
                $filterArray['room_type'] = explode(",",$categoryData->getData('room_type'));
            }


            $filterableAttributesList = $this->_filterableAttributeList->create($layerType);

            $filterList = $this->_filterList->create(['filterableAttributes' => $filterableAttributesList]);
            $filters = $filterList->getFilters($layer);
            $i = 0;
            $data =array();
            foreach ($filters as $filter) {
                // Don't show options with no items
                if (!$filter->getItemsCount()) {
                    continue;
                }

                $filters = (string)$filter->getName();
                $filteraa = "Visual-swatch";

                $items = $filter->getItems();
                $filterValues = array();

                $j = 0;
                foreach ($items as $item) {
                    $filterValues[$j]['value'] = $item->getLabel();
                    $filterValues[$j]['id'] = $item->getValue();
//                $filterValues[$j]['count']   = $item->getCount(); //Gives no. of products in each filter options

                    $j++;
                }
              //  print_r($filter->getAttributeModel()->getData());
                $data[] = array('id'=>$filter->getAttributeModel()->getAttributeId(),'code'=>$filter->getAttributeModel()->getAttributeCode(),'label'=>$filter->getName(),'type'=>$filter->getAttributeModel()->getFrontendInput(),'values'=>$filterValues);

                if (!empty($filterValues) && count($filterValues) > 1) {
                  //  $filterArray['filters'][$filters][$filteraa] = $filterValues;
                }
                $i++;

            }
              $filterArray['filters'] = $data;
            if (!isset($filterArray["filters"])) {
                $filterArray['filters'] = "No filters to show.";
            }

            header("Content-Type: application/json; charset=utf-8");
            $this->response = json_encode($filterArray);
            print_r($this->response, false);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage() . ' ' . 'CATEGORY INFO ERP API EXCEPTION');
            return ($e->getMessage());
        }//end try


    }

}
