<?php

namespace Codilar\TokenAPI\Model\Plugin\Controller\Account;

use Closure;

use Magento\Framework\App\Request\Http;
use PHPCuong\CustomerAccount\Controller\Customer\Ajax\Register;
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
use Magento\Framework\Encryption\EncryptorInterface;


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


    /**
     * API base OTP request URI
     */
    const OTP_REQUEST_URI = 'codilar_customer_api/otp_oauth/otp_request_url';

    /**
     * API base OTP request Endpoint
     */
    const OTP_REQUEST_ENDPOINT = 'codilar_customer_api/otp_oauth/otp_endpoint';

    /**
     * @var UrlInterface
     */
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
     * @var Http
     */
    protected $request;


    /**
     * RestrictCustomerRegister constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlFactory $urlFactory
     * @param Http $request
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     * @param ClientFactory $clientFactory
     * @param ResponseFactory $responseFactory
     * @param EncryptorInterface $encryptor
     * @param HandlerStack $stack
     * @param Callapi $callapi
     * @param StoreManagerInterface $storeManager
     * @param Json $json
     * @param Logger $loggerResponse
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UrlFactory $urlFactory,
        Http $request,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory,
        EncryptorInterface $encryptor,
        HandlerStack $stack,
        Callapi $callapi,
        StoreManagerInterface $storeManager,
        Json $json,
        Logger $loggerResponse
    ) {
        $this->urlModel              = $urlFactory->create();
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager        = $messageManager;
        $this->clientFactory         = $clientFactory;
        $this->request               = $request;
        $this->scopeConfig           = $scopeConfig;
        $this->responseFactory       = $responseFactory;
        $this->stack                 = $stack;
        $this->encryptor             = $encryptor;
        $this->json                  = $json;
        $this->callapi               = $callapi;
        $this->loggerResponse        = $loggerResponse;
        $this->_storeManager         = $storeManager;

    }//end __construct()


    /**
     * @param  Register $subject
     * @param  Closure  $proceed
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundExecute(
        Register $subject,
        Closure $proceed
    ) {
        $post = $this->request->getPostValue();

        $email = $post['email'];

        $signup_source = $this->_storeManager->getStore()->getBaseUrl();

        $parambody = [
            'full_name'     => $post['firstname'],
            'email'         => $email,
            'phone_number'  => $post['customer_mobile'],
            'signup_source' => $signup_source,
            'pincode'       => $post['postcode'],
        ];

        list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($parambody);
        $response        = $this->doRequest($apiRequestEndpoint, $requestMethod, $params);
        $status          = $response->getStatusCode();
        $responseBody    = $response->getBody();
        $responseContent = $responseBody->getContents();
        $responseDecodee = json_decode($responseContent, true);

        if ($status == 200) {
            $this->homelanepassword = $responseDecodee['password'];
            $this->homelaneuserID   = $responseDecodee['user_id'];
            $this->loggerResponse->addInfo('================================LEADGEN API SUCCESS======================');
            $this->loggerResponse->addInfo('Successful Registeration for Email:'.$email);
            $this->loggerResponse->addInfo('======================================================================');

            // OTP-GENERATE//////////////////////////////
            $otpParams = [
                'flag'          => 1,
                'email'         => $email,
                'phone'         => $post['customer_mobile'],
                'signup_source' => $signup_source,
            ];

            list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareOtpParams($otpParams);
            $otpresponse        = $this->doOtpRequest($apiRequestEndpoint, $requestMethod, $params);
            $otpstatus          = $response->getStatusCode();
            $otpresponseBody    = $response->getBody();
            $otpresponseContent = $responseBody->getContents();
            $otpresponseDecodee = json_decode($otpresponseContent, true);
        } else if ($status == 400) {
            $this->loggerResponse->addInfo('================================LEADGEN API ERROR===================');
            $this->loggerResponse->addInfo(
                'Error'.' '.$status.' '.'Missing mandatory params or Lead already
            exists'
            );
            $this->loggerResponse->addInfo('======================================================================');
            // return $resultRedirect->setUrl($defaultUrl);
        } else {
            $this->loggerResponse->addInfo('================================LEADGEN API ERROR===================');
            $this->loggerResponse->addInfo(
                'Error'.' '.$status.' '.'Authorization failed or Token
            not passed. Please refresh the access token'
            );
            $this->loggerResponse->addInfo('================================LEADGEN API ERROR===================');
        }//end if

        return $proceed();

    }//end aroundExecute()


    public function getUserId()
    {
        return $this->homelaneuserID;

    }//end getUserId()


    public function getPassword()
    {
        return $this->homelanepassword;

    }//end getPassword()


    /**
     * Get request url
     *
     * @return string
     */
    public function getSignupRequestUri()
    {
        return $this->scopeConfig->getValue(self::SIGNUP_REQUEST_URI, ScopeInterface::SCOPE_STORE);

    }//end getSignupRequestUri()


    /**
     * Get API Endpoint
     *
     * @return string
     */
    public function getSignupApiEndpoint()
    {
        return $this->scopeConfig->getValue(self::SIGNUP_REQUEST_ENDPOINT, ScopeInterface::SCOPE_STORE);

    }//end getSignupApiEndpoint()


    /**
     * Get otp request url
     *
     * @return string
     */
    public function getOtpRequestUri()
    {
        return $this->scopeConfig->getValue(self::OTP_REQUEST_URI, ScopeInterface::SCOPE_STORE);

    }//end getOtpRequestUri()


    /**
     * Get otp API Endpoint
     *
     * @return string
     */
    public function getOtpApiEndpoint()
    {
        return $this->scopeConfig->getValue(self::OTP_REQUEST_ENDPOINT, ScopeInterface::SCOPE_STORE);

    }//end getOtpApiEndpoint()


    /**
     * @param  $finalBrandData
     * @return array
     */
    private function prepareParams($finalBrandData): array
    {
        $apiRequestEndpoint = $this->getSignupApiEndpoint();
        $requestMethod      = Request::METHOD_POST;
        $params             = $finalBrandData;

        // collect param data
        $bodyJson              = $this->json->serialize($finalBrandData);
        $params['form_params'] = json_decode($bodyJson, true);
        // print_r(json_decode($bodyJson, true));
        // $params['body'] = $bodyJson;
        $params['debug'] = false;
        // //        $params['http_errors'] = false;
        // //        $params['handler'] = $tapMiddleware($stack);
        $params['headers'] = [
            'Content-Type'  => 'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer'.' '.$this->callapi->getToken(),
        ];
        return [
            $apiRequestEndpoint,
            $requestMethod,
            $params,
        ];

    }//end prepareParams()


    /**
     * @param  $paramsOtp
     * @return array
     */
    private function prepareOtpParams($paramsOtp): array
    {
        $apiRequestEndpoint = $this->getOtpApiEndpoint();
        $requestMethod      = Request::METHOD_POST;
        $params             = $paramsOtp;

        // collect param data
        $bodyJson              = $this->json->serialize($paramsOtp);
        $params['form_params'] = json_decode($bodyJson, true);
        // print_r(json_decode($bodyJson, true));
        // $params['body'] = $bodyJson;
        $params['debug'] = false;
        // //        $params['http_errors'] = false;
        // //        $params['handler'] = $tapMiddleware($stack);
        $params['headers'] = [
            'Content-Type'  => 'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer'.' '.$this->callapi->getToken(),
        ];
        return [
            $apiRequestEndpoint,
            $requestMethod,
            $params,
        ];

    }//end prepareOtpParams()


    /**
     * Do API request with provided params
     *
     * @param  $apiRequestEndpoint
     * @param  string             $requestMethod
     * @param  array              $params
     * @return Response
     */
    public function doRequest(
        $apiRequestEndpoint,
        $requestMethod,
        array $params=[]
    ): Response {
        // create middleware to add it in the request
        list($stack, $tapMiddleware) = $this->generateMiddleWare();

        /*
            @var Client $client
        */
        $client = $this->clientFactory->create(
            [
                'config' => [
                    'base_uri'      => $this->getSignupRequestUri(),
                    'handler'       => $tapMiddleware($stack),
                    'Authorization' => 'Bearer'.$this->callapi->getToken(),
                ],
            ]
        );

        try {
            $response = $client->request($requestMethod, $apiRequestEndpoint, $params);
        } catch (GuzzleException $exception) {
            /*
                @var Response $response
            */
            $response = $this->responseFactory->create(
                [
                    'status' => $exception->getCode(),
                    'body'   => $exception->getResponse()->getBody(),
                    'reason' => $exception->getMessage(),
                ]
            );
        }

        return $response;

    }//end doRequest()


    /**
     * Do API request for  OTP with provided params
     *
     * @param  $apiRequestEndpoint
     * @param  string             $requestMethod
     * @param  array              $params
     * @return Response
     */
    public function doOtpRequest(
        $apiRequestEndpoint,
        $requestMethod,
        array $params=[]
    ): Response {
        // create middleware to add it in the request
        list($stack, $tapMiddleware) = $this->generateMiddleWare();

        /*
            @var Client $client
        */
        $client = $this->clientFactory->create(
            [
                'config' => [
                    'base_uri' => $this->getOtpRequestUri(),
                    'handler'  => $tapMiddleware($stack),

                ],
            ]
        );

        try {
            $response = $client->request($requestMethod, $apiRequestEndpoint, $params);
        } catch (GuzzleException $exception) {
            /*
                @var Response $response
            */
            $response = $this->responseFactory->create(
                [
                    'status' => $exception->getCode(),
                    'body'   => $exception->getResponse()->getBody(),
                    'reason' => $exception->getMessage(),
                ]
            );
        }

        return $response;

    }//end doOtpRequest()


    /**
     * create middleware to add it in the request
     *
     * @return array
     */
    public function generateMiddleWare()
    {
        $stack = $this->stack->create();

        // Create a middleware that echoes parts of the request.
        $tapMiddleware = Middleware::tap(
            function ($request) {
                // var_dump($request->getHeaderLine('Content-Type'));
                // application/json
                // echo $request->getBody();
            }
        );

        return [
            $stack,
            $tapMiddleware,
        ];

    }//end generateMiddleWare()


}//end class
