<?php

namespace Codilar\TokenAPI\Plugin\Customer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Codilar\TokenAPI\Model\Plugin\Controller\Account\RestrictCustomer;
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
use Magento\Customer\Model\CustomerFactory;

class LoginPost
{


    /**
     * API base request URI
     */
    const LOGIN_REQUEST_URI = 'codilar_customer_api/login_oauth/login_request_url';

    /**
     * API base request Endpoint
     */
    const LOGIN_REQUEST_ENDPOINT = 'codilar_customer_api/login_oauth/login_endpoint';
     /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;
 
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

 /**
     * @var RestrictCustomer
     */
    protected $signupcustomer;
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
    /**
     * @var Callapi
     */
    protected $callapi;
      /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;




    /**
     * Data constructor.
     *
     * @param AccountManagementInterface $customerAccountManagement
     * @param StoreManagerInterface $storeManager
     * @param RestrictCustomer  $signupcustomer
     */

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        AccountManagementInterface $customerAccountManagement,
        StoreManagerInterface $storeManager,
        RestrictCustomer $signupcustomer,
        ClientFactory         $clientFactory,
        ResponseFactory       $responseFactory,
        HandlerStack          $stack,
        Callapi               $callapi,
        Json                  $json,
        Logger                $loggerResponse,
        CustomerFactory       $customerFactory
    ) {
        $this->_request = $context->getRequest();
        $this->_response = $context->getResponse();
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->resultFactory = $context->getResultFactory();
        $this->customerAccountManagement = $customerAccountManagement;
        $this->storeManager = $storeManager;
        $this->signupcustomer = $signupcustomer;
        $this->responseFactory = $responseFactory;
        $this->stack = $stack;
        $this->json = $json;
        $this->clientFactory = $clientFactory;
        $this->callapi = $callapi;
        $this->loggerResponse = $loggerResponse;
        $this->customerFactory  = $customerFactory;

    }

    public function aroundExecute(\Magento\Customer\Controller\Account\LoginPost $subject, $proceed)
    {           
        $login =  $this->_request->getPost('login'); 
      
        $email = $login['username'];
        $password = $login['password'];
        $check = $this->emailExistOrNot($email);

        // $custom_redirect= false;

        $returnValue = $proceed();
                    

        if ($check) {

$loginParams=['email' => $email , 'password' => $password];
            list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($loginParams);
        $response = $this->doRequest($apiRequestEndpoint,$requestMethod,$params);
        echo "<pre>";
         print_r($response);
        die;
           
        }
        // if (isset($login['press_room_page']) && $custom_redirect) {
        //     $resultRedirect = $this->resultRedirectFactory->create();
        //     $resultRedirect->setPath('mycustomlogin/index');
        //     return $resultRedirect; 
        // }
        return $returnValue;
    }



    /**
     *
     * @param $email
     * @return bool
     */
    public function emailExistOrNot($email): bool
    {
        
        $websiteId = (int)$this->storeManager->getWebsite()->getId();
        $isEmailNotExists = $this->customerAccountManagement->isEmailAvailable($email, $websiteId);
        return $isEmailNotExists;
    }

     /**
     * Get request url
     *
     * @return string
     */
    public function getLoginRequestUri()
    {
        return $this->scopeConfig->getValue(self::LOGIN_REQUEST_URI, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get API Endpoint
     *
     * @return string
     */
    public function getLoginApiEndpoint()
    {
        return $this->scopeConfig->getValue(self::LOGIN_REQUEST_ENDPOINT, ScopeInterface::SCOPE_STORE);
    }

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
            'base_uri' => $this->getLoginRequestUri(),
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