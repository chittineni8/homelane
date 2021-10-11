<?php

namespace Codilar\Catalog\Model\Config\Source;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class CategoryList implements OptionSourceInterface
{

    /**
     * @var CategoryFactory
     */
    protected CategoryFactory $categoryFactory;
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $categoryCollectionFactory;
    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    public function __construct(
        CollectionFactory $categoryCollectionFactory,
        CategoryFactory $categoryFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param bool $isActive
     * @param false $level
     * @param false $sortBy
     * @param false $pageSize
     * @return Collection
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCategoryCollection(bool $isActive = true, bool $level = false, bool $sortBy = false, bool $pageSize = false): Collection
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $store = $this->storeManager->getStore();
        $collection->setStore($store);


        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }

        // select categories of certain level
        if ($level) {
            $collection->addLevelFilter($level);
        }

        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }

        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize);
        }

        return $collection;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function toOptionArray(): array
    {
        $arr = $this->toArray();
        $ret = [];
        foreach ($arr as $key => $value){
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }
        return $ret;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    private function toArray(): array
    {
        $categories = $this->getCategoryCollection(true, false, false, false);
        $categoryList = array();
        foreach ($categories as $category){
            $categoryList[$category->getEntityId()] = __($this->getParentName($category->getPath()) . $category->getName());
        }
        return $categoryList;
    }

    private function getParentName($path = ''): string
    {
        $parentName = '';
        $rootCats = array(1,2);
        $catTree = explode("/", $path);
        array_pop($catTree);
        if($catTree && (count($catTree) > count($rootCats))){
            foreach ($catTree as $catId){
                if(!in_array($catId, $rootCats)){
                    $category = $this->categoryFactory->create()->load($catId);
                    $categoryName = $category->getName();
                    $parentName .= $categoryName . ' -> ';
                }
            }
        }
        return $parentName;
    }
}
