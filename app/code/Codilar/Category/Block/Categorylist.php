<?php

namespace Codilar\Category\Block;

class Categorylist extends \Magento\Framework\View\Element\Template {

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_categoryHelper;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $categoryFlatConfig;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param Template\Context                                        $context
     * @param \Magento\Catalog\Helper\Category                        $categoryHelper
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State      $categoryFlatState
     * @param \Magento\Catalog\Model\CategoryFactory                  $categoryFactory
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        $data = []
    )
    {
        $this->_categoryHelper           = $categoryHelper;
        $this->categoryFlatConfig        = $categoryFlatState;
        $this->_categoryFactory          = $categoryFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get all categories
     *
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     *
     * @return array|\Magento\Catalog\Model\ResourceModel\Category\Collection|\Magento\Framework\Data\Tree\Node\Collection
     */
    public function getCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        $cacheKey = sprintf('%d-%d-%d-%d', $this->getSelectedRootCategory(), $sorted, $asCollection, $toLoad);
        if ( isset($this->_storeCategories[ $cacheKey ]) )
        {
            return $this->_storeCategories[ $cacheKey ];
        }
        /**
         * Check if parent node of the store still exists
         */
        $category = $this->_categoryFactory->create();

        $storeCategories = $category->getCategories($this->getSelectedRootCategory(), $recursionLevel = 0, $sorted, $asCollection, $toLoad);
        $this->_storeCategories[ $cacheKey ] = $storeCategories;
        return $storeCategories;
    }

    /**
     * Get current store root category id
     *
     * @return int|mixed
     */
    public function getSelectedRootCategory()
    {
        return $this->_storeManager->getStore()->getRootCategoryId();
    }

    /**
     * @param        $category
     * @param string $html
     * @param int    $level
     *
     * @return string
     */
    public function getChildCategoryView($category, $html = '', $level = 1)
    {
        // Check if category has children
        if ( $category->hasChildren() )
        {
            $childCategories = $this->getSubcategories($category);
            $childCount = (int)$category->getChildrenCount();
            if ( $childCount > 0 )
            {
                $html .= '<ul class="o-list o-list--unstyled" style="display:none">';
                // Loop through children categories
                foreach ( $childCategories as $childCategory )
                {
                    $html .= '<li class="level' . $level . '">';
                    $html .= '<a href="' . $this->getCategoryUrl($childCategory) . '" title="' . $childCategory->getName() . '">' . $childCategory->getName() . '</a>';
                    if ($childCategory->hasChildren())
                    {
                        $html .= '<span class="expand"><i class="fa fa-plus">+/-</i></span>';
                        $html .= $this->getChildCategoryView($childCategory, '', ($level + 1));
                    }

                    $html .= '</li>';
                }
                $html .= '</ul>';
            }
        }
        return $html;
    }

    /**
     * Retrieve subcategories
     *
     * @param $category
     *
     * @return array
     */
    public function getSubcategories($category)
    {
        if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
            $subcategories = (array)$category->getChildrenNodes();
        } else {
            $subcategories = $category->getChildren();
        }
        return $subcategories;
    }

    /**
     * Return Category URL
     *
     * @param $category
     *
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->_categoryHelper->getCategoryUrl($category);
    }
}
