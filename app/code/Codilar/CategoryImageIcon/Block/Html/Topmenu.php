<?php
namespace Codilar\CategoryImageIcon\Block\Html;

use Magento\Framework\Data\Tree\Node;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    protected $_categoryFactory;
    protected $_storeManager;

    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
        $this->_categoryFactory = $collectionFactory;
        $this->_storeManager = $storeManager;

    }

    public function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
                                          $childrenWrapClass,
                                          $limit,
        array $colBrakes = []
    ) {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        /** @var \Magento\Framework\Data\Tree\Node $child */
        foreach ($children as $child) {
            if ($childLevel === 0 && $child->getData('is_parent_active') === false) {
                continue;
            }
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $currentClass = $child->getClass();

                if (empty($currentClass)) {
                    $child->setClass($outermostClass);
                } else {
                    $child->setClass($currentClass . ' ' . $outermostClass);
                }
            }

            if (is_array($colBrakes) && count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span class="image-container-block"> <span class="child-element">' . $this->escapeHtml(
                    $child->getName()
                ) . '</span>' . $this->getCustomImage($child) . '</span></a>' . $this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                ) . '</li>';
            $itemPosition++;
            $counter++;
        }

        if (is_array($colBrakes) && count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }

        return $html;
    }

    public function getCustomImage($childObj)
    {
        if (!($childObj->getIsCategory() && $childObj->getLevel() == 1)) {
            return false;
        }

        $store = $this->_storeManager->getStore();
        //$BaseUrl = $store->getBaseUrl();
	$BaseUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $catNodeArr = explode('-', $childObj->getId());
        $catId = end($catNodeArr);

        $collection = $this->_categoryFactory
            ->create()
            ->addAttributeToSelect('image_icon')
            ->addAttributeToFilter('entity_id',['eq'=>$catId])
            ->setPageSize(1);

        if ($collection->getSize() && $collection->getFirstItem()->getImageIcon()) {
            $catImageIconUrl = $BaseUrl . ltrim($collection->getFirstItem()->getImageIcon(),'/media');
            return '<span class="cat-imageicon"><img src="'.$catImageIconUrl.'"></span>';
        }
    }

}
