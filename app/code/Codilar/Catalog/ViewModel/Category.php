<?php
/**
 * Category.php
 *
 * @package     Homelane
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 */

namespace Codilar\Catalog\ViewModel;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Category
 *
 * @package     Homelane
 * @description Category class to get new category link
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * Category class to get new category link
 */
class Category implements ArgumentInterface
{
    const CATALOG_FRONTEND_LATEST_CATEGORY = 'catalog/frontend/latest_category';

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $categoryRepository;


    /**
     * Category constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CategoryRepository $categoryRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getNewCategory(): string
    {
        $categoryId = $this->scopeConfig->getValue(
            self::CATALOG_FRONTEND_LATEST_CATEGORY,
            ScopeInterface::SCOPE_STORE
        );
        if($categoryId == null || empty($categoryId))
        {
            $categoryId = 3;
        }
        $category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
        return $category->getUrl();
    }
}
