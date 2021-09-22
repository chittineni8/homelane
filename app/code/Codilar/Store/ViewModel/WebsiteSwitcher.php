<?php
/**
 * WebsiteSwitcher.php
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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\TranslatedLists;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\ResourceModel\Website\Collection as WebsiteCollection;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\ViewModel\SwitcherUrlProvider;

/**
 * Class WebsiteSwitcher
 *
 * @package     Homelane
 * @description Overlay class contains websites, stores and configurations functionality
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * WebsiteSwitcher class contains websites, stores and configurations functionality
 */
class WebsiteSwitcher extends SwitcherUrlProvider
{
    const LOCALE_CONFIG_PATH = 'general/locale/code';
    const DEFAULT_COUNTRY_CONFIG_PATH = 'general/country/default';

    /**
     * @var WebsiteCollectionFactory
     */
    private $websiteCollectionFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var TranslatedLists
     */
    private $translatedLists;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        EncoderInterface $encoder,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        WebsiteCollectionFactory $websiteCollectionFactory,
        ScopeConfigInterface $scopeConfig,
        TranslatedLists $translatedLists
    ) {
        parent::__construct($encoder, $storeManager, $urlBuilder);
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->translatedLists = $translatedLists;
        $this->storeManager = $storeManager;
    }

    /**
     * @throws LocalizedException
     */
    public function getWebsite(): WebsiteInterface
    {
        return $this->storeManager->getWebsite();
    }

    public function getWebsites(): WebsiteCollection
    {
        return $this->websiteCollectionFactory->create();
    }

    public function getStoreLocale(StoreInterface $store): string
    {
        return $this->scopeConfig->getValue(
            self::LOCALE_CONFIG_PATH,
            ScopeInterface::SCOPE_STORE,
            $store->getId()
        );
    }

    public function getStoreCountryCode(StoreInterface $store): string
    {
        return $this->scopeConfig->getValue(
            self::DEFAULT_COUNTRY_CONFIG_PATH,
            ScopeInterface::SCOPE_STORE,
            $store->getId()
        );
    }
}
