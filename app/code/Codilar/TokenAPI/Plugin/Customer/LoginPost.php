<?php
/**
 * LoginPost.php
 *
 * @package     Homelane
 * @description To check the registered homelane user and signing in.
 * @author      Abhinav Vinayak
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 */


namespace Codilar\TokenAPI\Plugin\Customer;

use Magedelight\SMSProfile\Controller\Ajax\Login;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Codilar\TokenAPI\Model\Plugin\Controller\Account\RestrictCustomer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Codilar\TokenAPI\Logger\Logger;
use Magento\Framework\Serialize\Serializer\Json;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Store\Model\ScopeInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Controller\Result\JsonFactory;
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
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;


    /**
     * Data constructor.
     *
     * @param Data $helper
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param AccountManagementInterface $customerAccountManagement
     * @param StoreManagerInterface $storeManager
     * @param RestrictCustomer $signupcustomer
     * @param ClientFactory $clientFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param ResponseFactory $responseFactory
     * @param HandlerStack $stack
     * @param Callapi $callapi
     * @param Json $json
     * @param Logger $loggerResponse
     * @param CustomerFactory $customerFactory
     */


    public function __construct(
        Data                       $helper,
        Context                    $context,
        JsonFactory                $resultJsonFactory,
        AccountManagementInterface $customerAccountManagement,
        StoreManagerInterface      $storeManager,
        RestrictCustomer           $signupcustomer,
        ClientFactory              $clientFactory,
        ScopeConfigInterface       $scopeConfig,
        ResponseFactory            $responseFactory,
        HandlerStack               $stack,
        Callapi                    $callapi,
        Json                       $json,
        Logger                     $loggerResponse,
        CustomerFactory            $customerFactory
    )
    {
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
        $this->scopeConfig = $scopeConfig;
        $this->callapi = $callapi;
        $this->loggerResponse = $loggerResponse;
        $this->customerFactory = $customerFactory;
        $this->helper = $helper;
        $this->resultJsonFactory=$resultJsonFactory;

    }//end __construct()

    /**
     * @param Login $subject
     * @param Callable $proceed
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundExecute(Login $subject, callable $proceed)
    {
        try {

            $login = $this->helper->jsonDecode($subject->getRequest()->getContent());
            $email = $login['username'];
            $password = $login['password'];

            $loginParams = [
                'email' => $email,
                'password' => $password,
                'ajax_save' => 1,
                'ajax_mode' => 1
            ];
            list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($loginParams);
            $response = $this->doRequest($apiRequestEndpoint, $requestMethod, $params);
            $status = $response->getStatusCode();
            $responseBody = $response->getBody();
            $responseContent = $responseBody->getContents();
            $responseDecodee = json_decode($responseContent, true);
            if ($status == 200) {
                if ($this->emailExistOrNot($email)):
                    $websiteId = $this->storeManager->getWebsite()->getWebsiteId();
                    $customer = $this->customerFactory->create();
                    $customer->setWebsiteId($websiteId);

                    // Preparing data for existing Homelane customer
                    $customer->setEmail($email);
                    $customer->setFirstname($responseDecodee['first_name']);
                    $customer->setLastname($responseDecodee['last_name']);
                    $customer->setPassword($password);
                    $customer->setHomelaneUserId($responseDecodee['user_id']);
                    $customer->setCustomerMobile($responseDecodee['mobile']);
                    // Save data
                    $customer->save();
                    $this->loggerResponse->addInfo('========================LOGIN SUCCESS========================');
                    $this->loggerResponse->addInfo('Account Created Successfully for email:' . ' ' . $email);
                    $this->loggerResponse->addInfo('============================================================');
                endif;
                return $proceed();
            } else if ($status == 401) {
                $this->loggerResponse->addInfo('========================LOGIN API ERROR======================================');
                $this->loggerResponse->addInfo('Invalid Email or Password' . ' / ' . 'No Auth Header was Present for email:' . ' ' . $email);
                $this->loggerResponse->addInfo('=============================================================================');
                $response = [
                    'errors' => true,
                    'message' => __('Invalid login or password.')
                ];
            } else {
                $this->loggerResponse->addInfo('========================LOGIN API ERROR======================================');
                $this->loggerResponse->addInfo('Error:'. $status . 'Missing Mandatory Params  for email:' . ' ' . $email);
                $this->loggerResponse->addInfo('=============================================================================');
                $response = [
                    'errors' => true,
                    'message' => __('Missing Mandatory Parameters.')
                ];
            }//end if

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);


        } catch (\Exception $e) {
            $this->loggerResponse->critical($e->getMessage() . ' ' . 'VERIFY PASSWORD CODE API EXCEPTION');
        }//end try

    }//end aroundExecute()


    /**
     *
     * @param  $email
     * @return boolean
     */
    public function emailExistOrNot($email): bool
    {
        $websiteId = (int)$this->storeManager->getWebsite()->getId();
        $isEmailNotExists = $this->customerAccountManagement->isEmailAvailable($email, $websiteId);
        return $isEmailNotExists;

    }//end emailExistOrNot()


    /**
     * Get request url
     *
     * @return string
     */
    public function getLoginRequestUri()
    {
        return $this->scopeConfig->getValue(self::LOGIN_REQUEST_URI, ScopeInterface::SCOPE_STORE);

    }//end getLoginRequestUri()


    /**
     * Get API Endpoint
     *
     * @return string
     */
    public function getLoginApiEndpoint()
    {
        return $this->scopeConfig->getValue(self::LOGIN_REQUEST_ENDPOINT, ScopeInterface::SCOPE_STORE);

    }//end getLoginApiEndpoint()


    private function prepareParams($finalBrandData): array
    {
        $apiRequestEndpoint = $this->getLoginApiEndpoint();
        $requestMethod = Request::METHOD_POST;
        $params = $finalBrandData;

        // collect param data
        $bodyJson = $this->json->serialize($finalBrandData);
        $params['form_params'] = json_decode($bodyJson, true);

        $params['debug'] = false;
        $params['headers'] = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer' . ' ' . $this->callapi->getToken(),
        ];
        return [
            $apiRequestEndpoint,
            $requestMethod,
            $params,
        ];

    }//end prepareParams()

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


        return array($stack, $tapMiddleware);
    }


}
