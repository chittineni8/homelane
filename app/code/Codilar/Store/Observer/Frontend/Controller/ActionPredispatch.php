<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Store\Observer\Frontend\Controller;

use Codilar\Store\ViewModel\Overlay;
use Magento\Framework\App\Request\Http;

class ActionPredispatch implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var Http
     */
    private $request;
    /**
     * @var Overlay
     */
    private $overlay;

    /**
     * ActionPredispatch constructor.
     * @param Http $request
     * @param Overlay $overlay
     */
    public function __construct(
        Http $request,
        Overlay $overlay
    ) {
        $this->request = $request;
        $this->overlay = $overlay;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        try {
            // Remove specific parameter from query string
            $key = 'city_change';
            $filteredURL = preg_replace('~(\?|&)' . $key . '=[^&]*~', '$1', $this->overlay->getParsedUrl());
            $redirectSessionValue = $this->overlay->getSessionValue();
            $redirectCookieValue = $this->overlay->getWebsiteCookie();
            if ($this->isCityChange() == 'true') {

                $this->overlay->setSessionValue($this->overlay->getBaseUrl());
                $this->overlay->setWebsiteCookie($this->overlay->getBaseUrl());
//                $this->overlay->getHttpResponse()->setRedirect($this->overlay->getBaseUrl() . '' . $filteredURL);
            } else {
                if (isset($redirectSessionValue) && $redirectSessionValue != null && $redirectSessionValue != $this->overlay->getBaseUrl()) {
                    $this->overlay->getHttpResponse()->setRedirect($this->overlay->getSessionValue() . '' . $filteredURL);
                    $this->overlay->setWebsiteCookie($redirectSessionValue);
                } elseif (isset($redirectCookieValue) && $redirectCookieValue != null && $redirectCookieValue != $this->overlay->getBaseUrl()) {
                    $this->overlay->setSessionValue($redirectCookieValue);
                    $this->overlay->getHttpResponse()->setRedirect($this->overlay->getWebsiteCookie() . '' . $filteredURL);
                }
            }
        } catch (\Exception $exception) {
        }
    }

    public function isCityChange()
    {
        $this->request->getParams();
        return $this->request->getParam('city_change');
    }
}
