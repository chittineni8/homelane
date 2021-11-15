<?php

namespace Codilar\TokenAPI\Plugin\Social;

use Exception;
use Magento\Framework\App\Request\Http;

use Magento\Framework\Exception\LocalizedException;

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
use Magento\Framework\View\Result\PageFactory;

// use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Codilar\TokenAPI\Logger\Logger;
use Mageplaza\SocialLogin\Model\Social;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Login
 *
 * @package Mageplaza\SocialLogin\Controller\Social
 */
class Login
{


    /**
     * API base request URI
     */
    const USEREXISTS_REQUEST_URI = 'codilar_customer_api/user_exists_oauth/userexist_request_url';

    /**
     * API base request Endpoint
     */
    const USEREXISTS_REQUEST_ENDPOINT = 'codilar_customer_api/user_exists_oauth/userexist_endpoint';

    /**
     * @type Social
     */
    protected $apiObject;

    /**
     * @var Page
     */
    protected $resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Customer
     */
    protected $_customerModel;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

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


    /**
     * @type \Mageplaza\SocialLogin\Helper\Social
     */
    protected $apiHelper;


    /**
     * @param \Mageplaza\SocialLogin\Helper\Social $apiHelper
     * @param Http $request
     * @param Social $apiObject
     * @param ResponseFactory $responseFactory
     * @param HandlerStack $stack
     * @param Callapi $callapi
     * @param StoreManagerInterface $storeManager
     * @param ClientFactory $clientFactory
     * @param Json $json
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $loggerResponse
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Mageplaza\SocialLogin\Helper\Social $apiHelper,
        Http                                 $request,
        Social                               $apiObject,
        ResponseFactory                      $responseFactory,
        HandlerStack                         $stack,
        Callapi                              $callapi,
        StoreManagerInterface                $storeManager,
        ClientFactory                        $clientFactory,
        Json                                 $json,
        ScopeConfigInterface                 $scopeConfig,
        Logger                               $loggerResponse,
        JsonFactory                          $resultJsonFactory,
        PageFactory                          $resultPageFactory

    )
    {
        $this->apiHelper = $apiHelper;
        $this->responseFactory = $responseFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->stack = $stack;
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->json = $json;
        $this->_storeManager = $storeManager;
        $this->callapi = $callapi;
        $this->clientFactory = $clientFactory;
        $this->loggerResponse = $loggerResponse;
        $this->request = $request;
        $this->apiObject = $apiObject;

    }


    /**
     * @param \Mageplaza\SocialLogin\Controller\Social\Login $subject
     * @param callable $proceed
     * @return \Magento\Framework\Controller\Result\Json|void
     * @throws LocalizedException
     */
    public function aroundExecute(\Mageplaza\SocialLogin\Controller\Social\Login $subject, callable $proceed)
    {


        $type = $this->apiHelper->setType($subject->getRequest()->getParam('type'));
        $userProfile = $this->apiObject->getUserProfile($type);

        $email = $userProfile->email ?: $userProfile->identifier . '@' . strtolower($type) . '.com';
        $firstname = $userProfile->firstName ?: (array_shift($name) ?: $userProfile->identifier);
        $lastname = $userProfile->lastName ?: (array_shift($name) ?: $userProfile->identifier);

        if ($email) :
            $emailbody = ['email' => $email];

            list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($emailbody);
            $emailData = $this->doRequest($apiRequestEndpoint, $requestMethod, $params);
            $status = $emailData->getStatusCode();
            $responseBody = $emailData->getBody();
            $responseContent = $responseBody->getContents();
            $responseEmail = json_decode($responseContent, true);


            if ($status == 200 && $responseEmail['user_exists'] == 1):

                return $proceed();

            elseif ($status == 200 && $responseEmail['user_exists'] != 1):

                $resultJson = $this->resultJsonFactory->create();

                $resultJson->setData('User not registered with homelane.com. Please use a different email');
                 // $resultPage = $this->resultPageFactory->create();
 //  $resultPage->getLayout()->getBlock('customer-popup-register')->setSocialData($email);
 // return $resultPage;

                return $resultJson;

            elseif ($status == 401):

                $this->loggerResponse->addInfo("========================SOCIAL LOGIN  API ERROR========================");
                $this->loggerResponse->addInfo("STATUS" . ' ' . $status . ' ' . "NO AUTHORIZATION HEADER PRESENT for email:" . $email);
                $this->loggerResponse->addInfo("===================================================================");


            else:

                $this->loggerResponse->addInfo("========================SOCIAL LOGIN  API ERROR========================");
                $this->loggerResponse->addInfo("STATUS" . ' ' . $status . ' ' . "ERROR");
                $this->loggerResponse->addInfo("===================================================================");


            endif;
        endif;


    }


    /**
     * Get request url
     *
     * @return string
     */
    public function getUserexistRequestUri()
    {
        return $this->scopeConfig->getValue(self::USEREXISTS_REQUEST_URI, ScopeInterface::SCOPE_STORE);

    }//end getUserexistRequestUri()


    /**
     * Get API Endpoint
     *
     * @return string
     */
    public function getUserexistApiEndpoint()
    {
        return $this->scopeConfig->getValue(self::USEREXISTS_REQUEST_ENDPOINT, ScopeInterface::SCOPE_STORE);

    }//end getUserexistApiEndpoint()


    /**
     * @param  $finalBrandData
     * @return array
     */
    private function prepareParams($finalBrandData): array
    {
        $apiRequestEndpoint = $this->getUserexistApiEndpoint();
        $requestMethod = Request::METHOD_POST;
        $params = $finalBrandData;
        // collect param data
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
                    'base_uri' => $this->getUserexistRequestUri(),
                    'handler' => $tapMiddleware($stack),

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
                    'reason' => $exception->getMessage(),
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
            }
        );

        return [
            $stack,
            $tapMiddleware,
        ];

    }//end generateMiddleWare()


}
