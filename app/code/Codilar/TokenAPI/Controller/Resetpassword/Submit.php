<?php
/**
 * Submit.php
 *
 * @package     Homelane
 * @description To reset the password.
 * @author      Abhinav Vinayak
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 */

namespace Codilar\TokenAPI\Controller\Resetpassword;

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
use Magento\Framework\HTTP\Client\Curl;
use Magento\Customer\Model\CustomerRegistry;

class Submit extends Action
{


    /**
     * API base request URI
     */
    const RECOVERY_REQUEST_URI = 'codilar_customer_api/regenerate_password_oauth/regenerate_password_request_url';

    /**
     * API base request Endpoint
     */
    const RECOVERY_REQUEST_ENDPOINT = 'codilar_customer_api/regenerate_password_oauth/regenerate_password_endpoint';

    /**
     * @var Curl
     */
    protected $curl;


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
     * @param Curl $curl
     * @param JsonFactory $resultJsonFactory
     * @param ResponseFactory $responseFactory
     * @param HandlerStack $stack
     * @param Callapi $callapi
     * @param StoreManagerInterface $storeManager
     * @param ClientFactory $clientFactory
     * @param Json $json
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $loggerResponse
     * @param Customer $customerModel
     */

    public function __construct(
        Context                     $context,
        Curl                        $curl,
        JsonFactory                 $resultJsonFactory,
        AccountManagementInterface  $customerAccountManagement,
        ResponseFactory             $responseFactory,
        ManagerInterface            $messageManager,
        EncryptorInterface          $encryptor,
        HandlerStack                $stack,
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
        $this->curl = $curl;
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
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $PostValue = $this->getRequest()->getParams();


            $parambody = [
                'password' => $PostValue['password'], 'code' => $PostValue['code']
            ];


            list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($parambody);
            $response = $this->doRequest($apiRequestEndpoint, $requestMethod, $params);
            $status = $response->getStatusCode();
            $responseBody = $response->getBody();
            $responseContent = $responseBody->getContents();
            $responseDecodee = json_decode($responseContent, true);


            if ($status == 200) {
                try{
                    $email = $PostValue['email'];
                if ($this->emailExistOrNot($email) == 0) {
                    $custId = $this->getCustomerIdByEmail($email);
                    $password = $PostValue['password'];
                    $customer = $this->customerRepositoryInterface->getById($custId);
                    $customerSecure = $this->customerRegistry->retrieveSecureData($custId);
                    $customerSecure->setRpToken(null);
                    $customerSecure->setRpTokenCreatedAt(null);
                    $customerSecure->setPasswordHash($this->_encryptor->getHash($password, true));
                $this->customerRepositoryInterface->save($customer);
                $this->loggerResponse->addInfo("========================PASSWORD RECOVERY API SUCCESS========================");
                $this->loggerResponse->addInfo("Password Reset Successfully for email:".' '.$email);
                $this->loggerResponse->addInfo("============================================================");
                $this->messageManager->addSuccessMessage(
                        'Password Reset Successfully'
                    );
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
              }catch (\Exception $e) {
        $this->loggerResponse->critical($e->getMessage() . ' ' . 'Homelane Store Password Reset Error for emailID:' .$email);
        }//end try
            } elseif ($status == 400) {

                $this->messageManager->addErrorMessage(
                    'This link is not valid any more'
                );
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
                $this->loggerResponse->addInfo("========================PASSWORD RECOVERY API ERROR========================");
                $this->loggerResponse->addInfo("Error" . ' ' . $status . ' ' . "This link is not valid any more/Missing mandatory params");
                $this->loggerResponse->addInfo("============================================================");


            } else {
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
                $this->loggerResponse->addInfo("========================PASSWORD RECOVERY API ERROR========================");
                $this->loggerResponse->addInfo("Error" . ' ' . $status . ' ' . "No Authorization Header Present");
                $this->loggerResponse->addInfo("============================================================");
            }
        } catch (\Exception $e) {
            $this->loggerResponse->critical($e->getMessage() . ' ' . 'RECOVERY PASSWORD API EXCEPTION');
        }//end try
    }//end execute()


    /**
     * Get request url
     *
     * @return string
     */
    public function getRecoveryRequestUri()
    {
        return $this->scopeConfig->getValue(self::RECOVERY_REQUEST_URI, ScopeInterface::SCOPE_STORE);

    }//end getUserexistRequestUri()


    /**
     * Get API Endpoint
     *
     * @return string
     */
    public function getRecoveryApiEndpoint()
    {
        return $this->scopeConfig->getValue(self::RECOVERY_REQUEST_ENDPOINT, ScopeInterface::SCOPE_STORE);

    }//end getUserexistApiEndpoint()


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

        $apiRequestEndpoint = $this->getRecoveryApiEndpoint();
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
                    'base_uri' => $this->getRecoveryRequestUri(),
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
            $customerData = $this->customerRepositoryInterface->get($email);
            $customerId = (int)$customerData->getId();
        } catch (NoSuchEntityException $noSuchEntityException) {
        }
        return $customerId;

    }
}//end class
