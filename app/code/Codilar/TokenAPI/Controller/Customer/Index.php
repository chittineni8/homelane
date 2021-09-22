<?php
/**
 * Image.php
 *
 * @package     Homelane
 * @description TokenAPI module which checks user exists or not in Register Form
 * @author      Abhinav Vinayak
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * TokenAPI module which checks user exists or not in Register Form
 */

namespace Codilar\TokenAPI\Controller\Customer;

use Magento\Customer\Model\Customer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Codilar\TokenAPI\Logger\Logger;
use Magento\Framework\Exception\LocalizedException;
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

class Index extends Action
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
     * @var Curl
     */
    protected $curl;

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
        Context               $context,
        Curl                  $curl,
        JsonFactory           $resultJsonFactory,
        ResponseFactory       $responseFactory,
        HandlerStack          $stack,
        Callapi               $callapi,
        StoreManagerInterface $storeManager,
        ClientFactory         $clientFactory,
        Json                  $json,
        ScopeConfigInterface  $scopeConfig,
        Logger                $loggerResponse,
        Customer              $customerModel
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_customerModel = $customerModel;
        $this->responseFactory = $responseFactory;
        $this->stack = $stack;
        $this->scopeConfig = $scopeConfig;
        $this->json = $json;
        $this->_storeManager = $storeManager;
        $this->callapi = $callapi;
        $this->clientFactory = $clientFactory;
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
            $mobileResult = $this->resultJsonFactory->create();
            $mobile = $this->getRequest()->getParam('mobile');
            if ($mobile) :
                $mobilebody = ['mobile' => $mobile];

                list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($mobilebody);
                $mobileData = $this->doRequest($apiRequestEndpoint, $requestMethod, $params);
                $status = $mobileData->getStatusCode();
                $responseBody = $mobileData->getBody();
                $responseContent = $responseBody->getContents();
                $responseMobile = json_decode($responseContent, true);

                $userExist = $responseMobile['user_exists'];
                if ($userExist) :
                    $userEmail = $responseMobile['email'];

                    $hiddenEmail = $this->hideEmailAddress($userEmail);

                    $mobileResult->setData(
                    'This phone number is associated with the
                     email id' . ' ' . $hiddenEmail . ' ' . 'Please log in u
   that email id. If this is not your email id, <b>click here to report the error.</b>'
                    );
                    return $mobileResult;
                else :
                    return $mobileResult;
                endif;
            endif;

            $resultJson = $this->resultJsonFactory->create();
            $email = $this->getRequest()->getParam('email');
           if ($email) :
                $emailbody = ['email' => $email];

                list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($emailbody);
                $emailData = $this->doRequest($apiRequestEndpoint, $requestMethod, $params);
                $status = $emailData->getStatusCode();
                $responseBody = $emailData->getBody();
                $responseContent = $responseBody->getContents();
                $responseEmail = json_decode($responseContent, true);

                $emailuserExist = $responseEmail['user_exists'];
                if ($emailuserExist) :
                    $resultJson->setData('User Exists. Please login or use a different email');
                    return $resultJson;
                else :
                    return $resultJson;
                endif;
            endif;
        } catch (\Exception $e) {
            $this->loggerResponse->critical($e->getMessage() . ' ' . 'UserExist API Exception');
        }//end try

    }//end execute()


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


    public function hideEmailAddress($email)
    {
        list($first, $last) = explode('@', $email);
        $first = str_replace(substr($first, '3'), str_repeat('*', (strlen($first) - 3)), $first);
        $last = explode('.', $last);
        $last_domain = str_replace(substr($last['0'], '1'), str_repeat('*', (strlen($last['0']) - 1)), $last['0']);
        $hideEmailAddress = $first . '@' . $last_domain . '.' . $last['1'];
        return $hideEmailAddress;

    }//end hideEmailAddress()


}//end class
