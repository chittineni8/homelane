<?php

namespace Codilar\TokenAPI\Model\Plugin\Controller\Account;

use Closure;
use Magento\Customer\Controller\Account\CreatePost;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Codilar\TokenAPI\Logger\Logger;
use Magento\Framework\Serialize\Serializer\Json;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Magento\Framework\Webapi\Rest\Request;
use Codilar\TokenAPI\Model\Common\Callapi;


class RestrictCustomer
{

    /**
     * API base request URI
     */
    const SIGNUP_REQUEST_URI = 'codilar_customer_api/signup_oauth/signup_request_url';

    /**
     * API base request Endpoint
     */
    const SIGNUP_REQUEST_ENDPOINT = 'codilar_customer_api/signup_oauth/signup_endpoint';


    /** @var UrlInterface */
    protected $urlModel;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;


    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var HandlerStack
     */
    private $stack;
    /**
     * @var LoggerResponse
     */
    private $loggerResponse;
    /**
     * @var Json
     */

    protected $json;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var Callapi
     */
    protected $callapi;


    /**
     * RestrictCustomerRegister constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlFactory $urlFactory
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     * @param ClientFactory $clientFactory
     * @param ResponseFactory $responseFactory
     * @param HandlerStack $stack
     * @param Callapi $callapi
     * @param StoreManagerInterface $storeManager
     * @param Json $json
     * @param Logger $loggerResponse
     */
    public function __construct(
        ScopeConfigInterface  $scopeConfig,
        UrlFactory            $urlFactory,
        RedirectFactory       $redirectFactory,
        ManagerInterface      $messageManager,
        ClientFactory         $clientFactory,
        ResponseFactory       $responseFactory,
        HandlerStack          $stack,
        Callapi               $callapi,
        StoreManagerInterface $storeManager,
        Json                  $json,
        Logger                $loggerResponse

    )
    {
        $this->urlModel = $urlFactory->create();
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->clientFactory = $clientFactory;
        $this->scopeConfig = $scopeConfig;
        $this->responseFactory = $responseFactory;
        $this->stack = $stack;
        $this->json = $json;
        $this->callapi = $callapi;
        $this->loggerResponse = $loggerResponse;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param CreatePost $subject
     * @param Closure $proceed
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundExecute(
        CreatePost $subject,
        Closure    $proceed
    )
    {
        /** @var RequestInterface $request */
        $firstname = $subject->getRequest()->getParam('firstname');
        $lastname = $subject->getRequest()->getParam('lastname');
        $email = $subject->getRequest()->getParam('email');
        $phone_number = $subject->getRequest()->getParam('telephone');
        $city = $subject->getRequest()->getParam('city');
        $signup_source = $this->_storeManager->getStore()->getBaseUrl();
        $postcode = $subject->getRequest()->getParam('postcode');
        $apiRequestEndpoint = $this->getSignupApiEndpoint();
        $requestMethod = Request::METHOD_POST;
        $parambody = [
            'full_name' => $firstname . ' ' . $lastname, 'email' => $email, 'phone_number' => $phone_number,
            'city' => $city, 'signup_source' => $signup_source, 'postcode' => $postcode
        ];
     
        list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($parambody);
        $response = $this->doRequest($apiRequestEndpoint,$requestMethod,$params);
        echo "<pre>";
         print_r($response);
        die;
        // if (in_array($domain, ['163.com', 'mail.ru'], true)) {

        //     $this->messageManager->addErrorMessage(
        //         'Registration is disabled for you domain'
        //     );
        //     $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
        //     /** @var Redirect $resultRedirect */
        //     $resultRedirect = $this->resultRedirectFactory->create();

        //     return $resultRedirect->setUrl($defaultUrl);

        // }
        return $proceed();
    }


    /**
     * Get request url
     *
     * @return string
     */
    public function getSignupRequestUri()
    {
        return $this->scopeConfig->getValue(self::SIGNUP_REQUEST_URI, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get API Endpoint
     *
     * @return string
     */
    public function getSignupApiEndpoint()
    {
        return $this->scopeConfig->getValue(self::SIGNUP_REQUEST_ENDPOINT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $finalBrandData
     * @return array
     */
    private function prepareParams($finalBrandData): array
    {
        $apiRequestEndpoint = $this->getSignupApiEndpoint();
        $requestMethod = Request::METHOD_POST;
        $params = $finalBrandData;

//         // collect param data
//         $bodyJson = $this->json->serialize($finalBrandData);
// //        $params['form_params'] = json_decode($bodyJson, true);
//         $params['body'] = $bodyJson;
//         // $params['debug'] = true;
// //        $params['http_errors'] = false;
// //        $params['handler'] = $tapMiddleware($stack);
//         $params['headers'] = [
//             'Content-Type' => 'application/json'
//         ];
        return array($apiRequestEndpoint, $requestMethod, $params);
    }

    /**
     * Do API request with provided params
     *
     * @param $apiRequestEndpoint
     * @param string $requestMethod
     * @param array $params
     * @return Response
     */
    public function doRequest(
        $apiRequestEndpoint,
        $requestMethod,
        array $params = []
    ): Response
    {
        // create middleware to add it in the request
        list($stack, $tapMiddleware) = $this->generateMiddleWare();

        /** @var Client $client */
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => $this->getSignupRequestUri(),
            'handler' => $tapMiddleware($stack),
            'Authorization' => "Bearer" .$this->callapi->getToken()
        ]]);

        try {
            $response = $client->request($requestMethod, $apiRequestEndpoint, $params);
        } catch (GuzzleException $exception) {
            /** @var Response $response */
            $response = $this->responseFactory->create([
                'status' => $exception->getCode(),
                'body' => $exception->getResponse()->getBody(),
                'reason' => $exception->getMessage()
            ]);
        }

        return $response;
    }

    /**
     * create middleware to add it in the request
     *
     * @return array
     */
    public function generateMiddleWare()
    {

        $stack = $this->stack->create();

        // Create a middleware that echoes parts of the request.
        $tapMiddleware = Middleware::tap(function ($request) {
            //    var_dump($request->getHeaderLine('Content-Type'));
            // application/json
            //    echo $request->getBody();
        });

        // $middleware = new Oauth1([
        //     'consumer_key' => $this->getConsumerKey(),
        //     'consumer_secret' => $this->getConsumerSecret(),
        //     'token' => $this->getTokenKey(),
        //     'token_secret' => $this->getTokenSecret(),
        //     'realm' => $this->getRealm(),
        //     'signature_method' => $this->getSignatureMethod()
        // ]);
        // $stack->push($middleware);
        return array($stack, $tapMiddleware);
    }


}
