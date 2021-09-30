<?php
/**
 * Index.php
 *
 * @package     Homelane
 * @description TokenAPI module which sends instruction mail to reset password.
 * @author      Abhinav Vinayak
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * TokenAPI module which sends instruction mail to reset password.
 */

namespace Codilar\TokenAPI\Controller\ForgetPassword;

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


class Index extends Action
{


    /**
     * API base request URI
     */
    const FORGET_PASSWORD_REQUEST_URI = 'codilar_customer_api/forgot_pass_oauth/forgot_password_request_url';

    /**
     * API base request Endpoint
     */
    const FORGET_PASSWORD_REQUEST_ENDPOINT = 'codilar_customer_api/forgot_pass_oauth/forgot_password_endpoint';


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
            $result = $this->resultJsonFactory->create();
            $email = $this->getRequest()->getParam('email');

            if ($email) :
                $emailbody = ['email' => $email, 'hlStore' => 1];

                list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($emailbody);
                $emailData = $this->doRequest($apiRequestEndpoint, $requestMethod, $params);
                $status = $emailData->getStatusCode();
                $responseBody = $emailData->getBody();
                $responseContent = $responseBody->getContents();
                $responsedecoded = json_decode($responseContent, true);
                if ($status == 200) :
                    $message = $responsedecoded['msg'];
                    $result->setData($message);
                    return $result;

                elseif ($status == 401):
                    if (array_key_exists('error', $responsedecoded)):
                        $error = $responsedecoded['error'];
                        $result->setData($error);
                        return $result;
                    else:
                        $this->loggerResponse->addInfo("========================FORGOT PASSWORD ERROR========================");
                        $this->loggerResponse->addInfo("STATUS" . ' ' . $status . ' ' . "NO AUTHORIZATION HEADER PRESENT for email:" . $email);
                        $this->loggerResponse->addInfo("===================================================================");
                    endif;
                else:
                    $error = $responsedecoded['error'];
                    $this->loggerResponse->addInfo("========================FORGOT PASSWORD ERROR========================");
                    $this->loggerResponse->addInfo("STATUS" . ' ' . $status . ' ' . $error . "for email:" . $email);
                    $this->loggerResponse->addInfo("===================================================================");

                endif;
            endif;

        } catch (\Exception $e) {
            $this->loggerResponse->critical($e->getMessage() . ' ' . 'ForgetPassword API Exception');
        }//end try

    }//end execute()


    /**
     * Get request url
     *
     * @return string
     */
    public function getForgetPasswordRequestUri()
    {
        return $this->scopeConfig->getValue(self::FORGET_PASSWORD_REQUEST_URI, ScopeInterface::SCOPE_STORE);

    }//end getUserexistRequestUri()


    /**
     * Get API Endpoint
     *
     * @return string
     */
    public function getForgetPasswordApiEndpoint()
    {
        return $this->scopeConfig->getValue(self::FORGET_PASSWORD_REQUEST_ENDPOINT, ScopeInterface::SCOPE_STORE);

    }//end getUserexistApiEndpoint()


    /**
     * @param  $finalBrandData
     * @return array
     */
    private function prepareParams($finalBrandData): array
    {
        $apiRequestEndpoint = $this->getForgetPasswordApiEndpoint();
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
                    'base_uri' => $this->getForgetPasswordRequestUri(),
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

}//end class
