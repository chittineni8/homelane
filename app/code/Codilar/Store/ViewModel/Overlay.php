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

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Codilar\Store\Model\Session;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\App\Response\Http;
//use Magento\Tests\NamingConvention\true\string;

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
    const SECURE_WEB_URL = 'web/secure/base_url';
    const COOKIE_LIFETIME = 1;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @var Http
     */
    protected $httpResponse;

    /**
     * Overlay constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlInterface
     * @param Session $session
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param SessionManagerInterface $sessionManager
     * @param Http $httpResponse
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        UrlInterface $urlInterface,
        Session $session,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        Http $httpResponse
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->urlInterface = $urlInterface;
        $this->session = $session;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        $this->httpResponse = $httpResponse;
    }

    public function setSessionValue($value)
    {
        $this->sessionManager->start();
        $this->sessionManager->unsetWebsiteCode();
        $this->sessionManager->setWebsiteCode($value);
    }

    public function getSessionValue()
    {
        $this->sessionManager->start();
        return $this->sessionManager->getWebsiteCode();
    }

    public function unSetSessionValue()
    {
        $this->sessionManager->start();
        return $this->sessionManager->unsetData('website_code');
    }

    /**
     * @return Http
     */
    public function getHttpResponse(): Http
    {
        return $this->httpResponse;
    }


    /**
     * @param $websiteCode
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     */
    public function setWebsiteCookie($websiteCode)
    {
        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $publicCookieMetadata->setDurationOneYear();
        $publicCookieMetadata->setPath('/');
        $publicCookieMetadata->setHttpOnly(false);

        return $this->cookieManager->setPublicCookie('website_code', $websiteCode, $publicCookieMetadata);
    }


    /**
     * @return string|null
     */
    public function getWebsiteCookie()
    {
        return $this->cookieManager->getCookie(
            'website_code'
        );
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
    public function getStoreDetail(): array
    {
        $stores = $this->storeManager->getWebsites();

        foreach ($stores as $store) {
            $storeDetails[] = [
                "storeName" => $store->getName(),
                "imageUrl" => $this->getConfigValue(self::IMAGE_URL, $store->getId()),
                "webUrl" => $this->scopeConfig->getValue(
                    self::SECURE_WEB_URL,
                    ScopeInterface::SCOPE_WEBSITE,
                    $store->getId()
                )];
        }
        return $storeDetails;
    }

    /**
     * @return string|void
     */
    public function getWebsiteCode()
    {
        $websites = $this->storeManager->getWebsites();
        foreach ($websites as $website) {
            return $website->getCode();
        }
    }

    /**
     * @return string
     */
    public function getCurrentUrl(): string
    {
        return $this->urlInterface->getCurrentUrl();
    }


    /**
     * @return array|string
     */
    public function getParsedUrl()
    {
        $baseUrl = $this->urlInterface->getBaseUrl();
        $currentUrl = $this->getCurrentUrl();
        return str_replace($baseUrl,'',$currentUrl);
//        return ltrim($currentUrl,$baseUrl);
//        return $this->unparsedUrl(parse_url($currentUrl));
//        return $websiteUrl['scheme'].'/'.$websiteCode.$websiteUrl['path'];
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->urlInterface->getBaseUrl();
    }


    /**
     * @param $webUrl
     * @return array
     */
    public function getParsedWebCode($webUrl): array
    {
        return $this->unparsedUrl(parse_url($webUrl));
    }
    /**
     * @param $parsed_url
     * @return array
     */
    public function unparsedUrl($parsed_url): array
    {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = $parsed_url['host'] ?? '';
        $path     = $parsed_url['path'] ?? '';
        $websiteUrl[] = [
            "scheme" => $scheme.$host,
            "path" => $path
        ];
        return $websiteUrl;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getMediaUrl(): string
    {
        return  $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }
}
