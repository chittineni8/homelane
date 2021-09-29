<?php
/**
 * Index.php
 *
 * @package     Homelane
 * @description Verifies the password code api
 * @author      Abhinav Vinayak
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 *  Verifies the password code api
 */
declare(strict_types=1);

namespace Codilar\TokenAPI\Controller\Resetpassword;

use Codilar\TokenAPI\Logger\Logger;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\Serializer\Json;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Store\Model\ScopeInterface;
use Codilar\TokenAPI\Model\Common\Callapi;

class Index extends Action
{

    /**
     * @var Page
     */
    protected $resultPageFactory;

    /**
     * API base request URI
     */
    const VERIFY_CODE_REQUEST_URI = 'codilar_customer_api/forgot_pass_oauth/verify_password_code';

    /**
     * @var EncryptorInterface
     */
    protected $_encryptor;

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
     * @var Logger
     */
    private $loggerResponse;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var Callapi
     */
    protected $callapi;

    /**
     * TokenApiService constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param ClientFactory $clientFactory
     * @param ResponseFactory $responseFactory
     * @param HandlerStack $stack
     * @param Json $json
     * @param LoggerResponse $loggerResponse
     */


    /**
     * * @param Context $context
     */
    public function __construct(
        Context              $context,
        PageFactory          $resultPageFactory,
        Http                 $request,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface   $encryptor,
        ClientFactory        $clientFactory,
        ResponseFactory      $responseFactory,
        HandlerStack         $stack,
        Json                 $json,
        Callapi              $callapi,
        Logger               $loggerResponse
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->clientFactory = $clientFactory;
        $this->scopeConfig = $scopeConfig;
        $this->responseFactory = $responseFactory;
        $this->_encryptor = $encryptor;
        $this->stack = $stack;
        $this->json = $json;
        $this->callapi = $callapi;
        $this->loggerResponse = $loggerResponse;
        parent::__construct($context);

    }//end __construct()


    /**
     * Reset password form
     *
     * @return PageFactory
     */
    public function execute()
    {
        try {

            $GetValue = $this->getRequest()->getParams();
            if (!empty($GetValue) && array_key_exists('code', $GetValue)) {
                $this->code = $GetValue['code'];
                $finalData = [];
                list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($finalData);

                $response = $this->doRequest($apiRequestEndpoint, $requestMethod, $params);

                $status = $response->getStatusCode();
                $responseBody = $response->getBody();
                $responseContent = $responseBody->getContents();
                $responseDecodee = json_decode($responseContent, true);
                $msg = $responseDecodee['msg'];

                if ($status == 200) {

                    $email = $responseDecodee['email'];
                    $data = ['email' => $email, 'code' => $this->code];
                    $resultPage = $this->resultPageFactory->create();
                    $resultPage->getConfig()->getTitle()->set(__('Reset Password'));
                    $resultPage->getLayout()->getBlock('homelane_resetpassword')->setFormData($data);
                    return $resultPage;


                } elseif ($status == 400) {
                    $resultPage = $this->resultPageFactory->create();
                    $this->loggerResponse->addInfo("========================VERIFY PASSWORD CODE API ERROR========================");
                    $this->loggerResponse->addInfo("STATUS" . ' ' . $status . ' ' . "This link is not valid any more.");
                    $this->loggerResponse->addInfo("===================================================================");
                    $resultPage->getConfig()->getTitle()->set(__($msg));
                    return $resultPage;
                } else {
                    $this->loggerResponse->addInfo("========================VERIFY PASSWORD CODE API ERROR========================");
                    $this->loggerResponse->addInfo("STATUS" . ' ' . $status . ' ' . "Authorization failed or Token not passed. Please refresh the access token");
                    $this->loggerResponse->addInfo("===================================================================");

                }
            }//end if
        } catch (\Exception $e) {
            $this->loggerResponse->critical($e->getMessage() . ' ' . 'VERIFY PASSWORD CODE API EXCEPTION');
        }//end try
    }//end execute()


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
                    'base_uri' => $this->getVerifyCodeUri(),
                    'handler' => $tapMiddleware($stack),
                ],
            ]
        );

        try {
            $response = $client->request('GET', 'verifyPasswordCode?code=' . $apiRequestEndpoint, $params);
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
     * Get request url
     *
     * @return string
     */
    public function getVerifyCodeUri()
    {
        return $this->scopeConfig->getValue(self::VERIFY_CODE_REQUEST_URI, ScopeInterface::SCOPE_STORE);

    }//end getVerifyCodeUri()


    /**
     * @param  $finalBrandData
     * @return array
     */
    public function prepareParams($finalBrandData): array
    {
        $apiRequestEndpoint = $this->code;
        $requestMethod = Request::METHOD_GET;
        $params = [];

        // collect param data

        $params['headers'] = [
            'Authorization' => 'Bearer' . ' ' . $this->callapi->getToken()
        ];
        return array($apiRequestEndpoint, $requestMethod, $params);
    }//end prepareParams()


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
        // $stack->push($middleware);
        return [
            $stack,
            $tapMiddleware,
        ];

    }//end generateMiddleWare()


}//end class
