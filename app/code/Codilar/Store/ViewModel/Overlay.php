<?php
/**
 * Overlay.php
 *
 * @package     Homelane
 * @description Store Module which contains store switching functionality
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * Store Module which contains store switching functionality
 */

namespace Codilar\Store\ViewModel;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Overlay
 *
 * @package     Homelane
 * @description Overlay class contains websites, stores and configurations functionality
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * Overlay class contains websites, stores and configurations functionality
 */
class Overlay implements ArgumentInterface
{
    const IMAGE_URL = 'general/locale/city_image';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Overlay constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $value
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue(string $value = '', $storeId = null)
    {
        return $this->scopeConfig->getValue($value, ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * @return array
     */
    public function getWebsiteDetail(): array
    {
        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $websiteDetails[] = [
                "storeName" => $store->getName(),
                "imageUrl" => $this->getConfigValue(self::IMAGE_URL, $store->getId()),
            ];
        }
        return $websiteDetails;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getMediaUrl(): string
    {
        return  $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }
}
