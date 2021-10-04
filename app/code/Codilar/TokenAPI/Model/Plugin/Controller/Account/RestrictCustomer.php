<?php

namespace Codilar\TokenAPI\Model\Plugin\Controller\Account;

use Closure;

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
     * @param EncryptorInterface $encryptor
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
        EncryptorInterface    $encryptor,
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
        $this->encryptor = $encryptor;
        $this->json = $json;
        $this->callapi = $callapi;
        $this->loggerResponse = $loggerResponse;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param Register $subject
     * @param Closure $proceed
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundExecute(
        Register $subject,
        Closure  $proceed
    )
    {
        /** @var RequestInterface $request */
        $firstname = $subject->getRequest()->getParam('firstname');
        $email = $subject->getRequest()->getParam('email');
        $phone_number = $subject->getRequest()->getParam('customer_mobile');
        $signup_source = $this->_storeManager->getStore()->getBaseUrl();
        $postcode = $subject->getRequest()->getParam('postcode');


        $parambody = [
            'full_name' => $firstname, 'email' => $email, 'phone_number' => $phone_number,
            'signup_source' => $signup_source, 'pincode' => $postcode
        ];


        list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($parambody);
        $response = $this->doRequest($apiRequestEndpoint, $requestMethod, $params);
        $status = $response->getStatusCode();
        $responseBody = $response->getBody();
        $responseContent = $responseBody->getContents();
        $responseDecodee = json_decode($responseContent, true);
        // print_r($status);
        // print_r($responseContent);
        // print_r($responseDecodee);


        if ($status == 200) {

            $this->homelanepassword = $responseDecodee['password'];
            $this->homelaneuserID = $responseDecodee['user_id'];
            $this->loggerResponse->addInfo("================================LEADGEN API SUCCESS======================");
            $this->loggerResponse->addInfo("Successful Registeration for Email:" . $email);
            $this->loggerResponse->addInfo("======================================================================");

        } elseif ($status == 400) {
            // $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
            // /** @var Redirect $resultRedirect */
            // $resultRedirect = $this->resultRedirectFactory->create();
            $this->loggerResponse->addInfo("================================LEADGEN API ERROR===================");
            $this->loggerResponse->addInfo("Error" . ' ' . $status . ' ' . "Missing mandatory params or Lead already
            exists");
            $this->loggerResponse->addInfo("======================================================================");
            // return $resultRedirect->setUrl($defaultUrl);

        } else {
            $this->loggerResponse->addInfo("================================LEADGEN API ERROR===================");
            $this->loggerResponse->addInfo("Error" . ' ' . $status . ' ' . "Authorization failed or Token
            not passed. Please refresh the access token");
            $this->loggerResponse->addInfo("================================LEADGEN API ERROR===================");
        }

        return $proceed();

    }


    public function getUserId()
    {
        return $this->homelaneuserID;
    }

    public function getPassword()
    {
        return $this->homelanepassword;
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
        $bodyJson = $this->json->serialize($finalBrandData);
        $params['form_params'] = json_decode($bodyJson, true);
        //    print_r(json_decode($bodyJson, true));
        // $params['body'] = $bodyJson;
        $params['debug'] = false;
// //        $params['http_errors'] = false;
// //        $params['handler'] = $tapMiddleware($stack);
        $params['headers'] = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer' . ' ' . $this->callapi->getToken()
        ];
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
            'Authorization' => "Bearer" . $this->callapi->getToken()
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


        return array($stack, $tapMiddleware);
    }


}
