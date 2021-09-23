<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\TokenAPI\Model\Common;

use Codilar\TokenAPI\Logger\Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\Serializer\Json;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Store\Model\ScopeInterface;

class Callapi
{

    /**
     * API base request URI
     */
    const REQUEST_URI = 'codilar_customer_api/oauth/request_url';


    /**
     * API base request URI Endpoint
     */
    const API_ENDPOINT = 'codilar_customer_api/oauth/endpoint';

    /**
     *
     */
    const USERNAME = 'codilar_customer_api/token/username';

    /**
     * API base request URI
     */
    const PASSWORD = 'codilar_customer_api/token/password';

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
     * TokenApiService constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param ClientFactory $clientFactory
     * @param ResponseFactory $responseFactory
     * @param HandlerStack $stack
     * @param Json $json
     * @param LoggerResponse $loggerResponse
     *
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface   $encryptor,
        ClientFactory        $clientFactory,
        ResponseFactory      $responseFactory,
        HandlerStack         $stack,
        Json                 $json,
        Logger               $loggerResponse
    )
    {
        $this->clientFactory = $clientFactory;
        $this->scopeConfig = $scopeConfig;
        $this->responseFactory = $responseFactory;
        $this->_encryptor = $encryptor;
        $this->stack = $stack;
        $this->json = $json;
        $this->loggerResponse = $loggerResponse;
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
            'base_uri' => $this->getRequestUri(),
            'handler' => $tapMiddleware($stack)
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
     * Get request url
     *
     * @return string
     */
    public function getRequestUri()
    {
        return $this->scopeConfig->getValue(self::REQUEST_URI, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get API Endpoint
     *
     * @return string
     */
    public function getApiEndpoint()
    {
        return $this->scopeConfig->getValue(self::API_ENDPOINT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->scopeConfig->getValue(self::USERNAME, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Password
     *
     * @return string
     */
    public function getPassword()
    {
        $encryptedValue = $this->scopeConfig->getValue(self::PASSWORD, ScopeInterface::SCOPE_STORE);
        return $this->_encryptor->decrypt($encryptedValue);

    }

    /**
     * Do Token request
     *
     * @return Token
     */
    public function getToken()
    {
        $parambody = ['user' => $this->getUsername(), 'password' => $this->getPassword()];

        list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($parambody);

        $response = $this->doRequest($apiRequestEndpoint, $requestMethod, $params);

        $status = $response->getStatusCode();
        $responseBody = $response->getBody();
        $responseContent = $responseBody->getContents();
        $responseDecodee = json_decode($responseContent, true);


        if ($status == 200) {
            $token = $responseDecodee['token'];
            // $this->loggerResponse->addInfo("========================TOKEN SUCCESS========================");
            // $this->loggerResponse->addInfo("Token:" . ' ' . $token);
            // $this->loggerResponse->addInfo("============================================================");
            return $token;
        } else {

            $this->loggerResponse->addInfo("========================TOKEN ERROR========================");
            $this->loggerResponse->addInfo("Error" . ' ' . $status . ' ' . "user or password not matched.");
            $this->loggerResponse->addInfo("============================================================");

        }

    }


    /**
     * @param $finalBrandData
     * @return array
     */
    public function prepareParams($finalBrandData): array
    {
        $apiRequestEndpoint = $this->getApiEndpoint();
        $requestMethod = Request::METHOD_POST;
        $params = [];

        // collect param data
        $bodyJson = $this->json->serialize($finalBrandData);
//        $params['form_params'] = json_decode($bodyJson, true);
        $params['body'] = $bodyJson;
        // $params['debug'] = true;
//        $params['http_errors'] = false;
//        $params['handler'] = $tapMiddleware($stack);
        $params['headers'] = [
            'Content-Type' => 'application/json'
        ];
        return array($apiRequestEndpoint, $requestMethod, $params);
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


