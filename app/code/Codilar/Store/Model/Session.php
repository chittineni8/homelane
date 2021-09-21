<?php
/**
 * @package     homelane
 * @author      Codilar Technologies
 * @link        https://www.codilar.com/
 * @copyright © 2021 Codilar Technologies Pvt. Ltd.. All rights reserved.
 */

namespace Codilar\Store\Model;

use Magento\Framework\App\Http\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\State;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Session\Generic;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Session\StorageInterface;
use Magento\Framework\Session\ValidatorInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class Session extends SessionManager
{
    protected $_session;
    protected $_coreUrl = null;
    protected $_configShare;
    protected $_urlFactory;
    protected $_eventManager;
    protected $response;
    protected $_sessionManager;

    public function __construct(
        Http                   $request,
        SidResolverInterface   $sidResolver,
        ConfigInterface        $sessionConfig,
        SaveHandlerInterface   $saveHandler,
        ValidatorInterface     $validator,
        StorageInterface       $storage,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory  $cookieMetadataFactory,
        Context                $httpContext,
        State                  $appState,
        Generic                $session,
        ManagerInterface       $eventManager,
        HttpResponse           $response
    ) {

        $this->_session = $session;
        $this->_eventManager = $eventManager;

        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState
        );
        $this->response = $response;
        $this->_eventManager->dispatch('websitecode_session_init', ['websitecode_session' => $this]);
    }
}
