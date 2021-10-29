<?php
/**
 * OtpGenerate.php
 *
 * @package     Homelane
 * @description To regenerate  the otp.
 * @author      Abhinav Vinayak
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 */

namespace Codilar\TokenAPI\Controller\Otp;

use Magento\Customer\Model\Customer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Codilar\TokenAPI\Logger\Logger;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Magento\Framework\Webapi\Rest\Request;
use Codilar\TokenAPI\Model\Common\Callapi;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\CustomerRegistry;

class OtpGenerate extends Action
{


    /**
     * API base OTP request URI
     */
    const OTP_REQUEST_URI = 'codilar_customer_api/otp_oauth/otp_request_url';

    /**
     * API base OTP request Endpoint
     */
    const OTP_REQUEST_ENDPOINT = 'codilar_customer_api/otp_oauth/otp_endpoint';

    /**
     * @var Page
     */
    protected $resultPageFactory;


    /**
     * @var EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;


    /**
     * @var ManagerInterface
     */
    protected $messageManager;


    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var Customer
     */
    protected $_customerModel;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;


    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

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
     * @var Callapi
     */
    protected $callapi;


    protected $customerRegistry;


    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param AccountManagementInterface $customerAccountManagement
     * @param ResponseFactory $responseFactory
     * @param ManagerInterface $messageManager
     * @param EncryptorInterface $encryptor
     * @param HandlerStack $stack
     * @param PageFactory $resultPageFactory
     * @param Callapi $callapi
     * @param CustomerRegistry $customerRegistry
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param StoreManagerInterface $storeManager
     * @param ClientFactory $clientFactory
     * @param Json $json
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $loggerResponse
     * @param Customer $customerModel
     */

    public function __construct(
        Context                     $context,
        JsonFactory                 $resultJsonFactory,
        AccountManagementInterface  $customerAccountManagement,
        ResponseFactory             $responseFactory,
        ManagerInterface            $messageManager,
        EncryptorInterface          $encryptor,
        HandlerStack                $stack,
        PageFactory                 $resultPageFactory,
        Callapi                     $callapi,
        CustomerRegistry            $customerRegistry,
        CustomerRepositoryInterface $customerRepositoryInterface,
        StoreManagerInterface       $storeManager,
        ClientFactory               $clientFactory,
        Json                        $json,
        ScopeConfigInterface        $scopeConfig,
        Logger                      $loggerResponse,
        Customer                    $customerModel
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_customerModel = $customerModel;
        $this->responseFactory = $responseFactory;
        $this->stack = $stack;
        $this->scopeConfig = $scopeConfig;
        $this->customerRegistry = $customerRegistry;
        $this->_encryptor = $encryptor;
        $this->messageManager = $messageManager;
        $this->json = $json;
        $this->_storeManager = $storeManager;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->callapi = $callapi;
        $this->clientFactory = $clientFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->loggerResponse = $loggerResponse;
        parent::__construct($context);

    }

    /**
     *
     * @return string
     * @throws LocalizedException
     */
    public function execute()
    {
        try {


            $postOtp = $this->getRequest()->getPostValue();
            $signup_source = $this->_storeManager->getStore()->getBaseUrl();

            if ($postOtp['flag'] == 3):

                $parambody = [
                    'flag' => $postOtp['flag'], 'email' => $postOtp['email'], 'phone' => $postOtp['phone'], 'signup_source' => $signup_source, 'type' => $postOtp['type'], 'otp' => $postOtp['otp']
                ];
            else:


                $parambody = [
                    'flag' => $postOtp['flag'], 'email' => $postOtp['email'], 'phone' => $postOtp['phone'], 'signup_source' => $signup_source, 'type' => $postOtp['type'], 'isEditNum' => $postOtp['isEditNum'], 'isResendOtpClick' => $postOtp['isResendOtpClick']
                ];

            endif;

            list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($parambody);
            $response = $this->doRequest($apiRequestEndpoint, $requestMethod, $params);
            $status = $response->getStatusCode();
            $responseBody = $response->getBody();
            $responseContent = $responseBody->getContents();
            $responseDecodee = json_decode($responseContent, true);
            $resultJson = $this->resultJsonFactory->create();

            if ($status == 200):

                if (array_key_exists('otpVerified', $responseDecodee)):

                    $verified = $responseDecodee['otpVerified'];

                    if ($verified == 'true'):

                        $resultJson->setData('Verified');
                        return $resultJson;

                    else:

                        $resultJson->setData('Not Verified');
                        return $resultJson;

                    endif;
                endif;

            elseif ($status == 400):


                $this->loggerResponse->addInfo('================================OTP POPUP API ERROR===================');
                $this->loggerResponse->addInfo(
                    'Error' . ' ' . $status . ' ' . 'Missing mandatory params or Lead already exists');
                $this->loggerResponse->addInfo('======================================================================');

            else:


                $this->loggerResponse->addInfo('=================OTP POPUP API ERROR===================');
                $this->loggerResponse->addInfo(
                    'Error' . ' ' . $status . ' ' . 'Authorization failed or Token
            not passed. Please refresh the access token'
                );
                $this->loggerResponse->addInfo('================================LEADGEN API ERROR===================');
            endif;

        } catch (\Exception $e) {
            $this->loggerResponse->critical($e->getMessage() . ' ' . 'VERIFY OTP API EXCEPTION');
        }//end try

    }//end execute()


    /**
     * Get request url
     *
     * @return string
     */
    public function getOtpRequestUri()
    {
        return $this->scopeConfig->getValue(self::OTP_REQUEST_URI, ScopeInterface::SCOPE_STORE);

    }//end getOtpRequestUri()


    /**
     * Get API Endpoint
     *
     * @return string
     */
    public function getOtpApiEndpoint()
    {
        return $this->scopeConfig->getValue(self::OTP_REQUEST_ENDPOINT, ScopeInterface::SCOPE_STORE);

    }//end getOtpApiEndpoint()


    /**
     *
     * @param $email
     * @return bool
     */
    public function emailExistOrNot($email): bool
    {

        $websiteId = (int)$this->_storeManager->getWebsite()->getId();
        $isEmailNotExists = $this->customerAccountManagement->isEmailAvailable($email, $websiteId);
        return $isEmailNotExists;
    }


    /**
     * @param  $finalBrandData
     * @return array
     */
    private function prepareParams($finalBrandData): array
    {

        $apiRequestEndpoint = $this->getOtpApiEndpoint();
        $requestMethod = Request::METHOD_POST;
        $params = $finalBrandData;
        // collect param data
        $params['debug'] = false;
        $bodyJson = $this->json->serialize($finalBrandData);
        $params['form_params'] = json_decode($bodyJson, true);
        $params['headers'] = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer' . ' ' . $this->callapi->getToken()
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
     * @param  $apiRequestEndpoint
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

        /*
            @var Client $client
        */
        $client = $this->clientFactory->create(
            [
                'config' => [
                    'base_uri' => $this->getOtpRequestUri(),
                    'handler' => $tapMiddleware($stack)


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
                    'body' => $exception->getResponse()->getBody(),
                    'reason' => $exception->getMessage()
                ]
            );
        }

        return $response;

    }//end doRequest()


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
            });

        return [
            $stack,
            $tapMiddleware,
        ];

    }//end generateMiddleWare()


    /**
     * @param string $email
     * @return int|null
     */
    public function getCustomerIdByEmail($email)
    {
        $customerId = null;
        try {
            $customerData = $this->customerRepository->get($email);
            $customerId = (int)$customerData->getId();
        } catch (NoSuchEntityException $noSuchEntityException) {
        }
        return $customerId;

    }
}//end class
