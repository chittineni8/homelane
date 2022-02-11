<?php

namespace Codilar\AttributeSet\Model\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
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
use Magento\Framework\Webapi\Rest\Request;
use Codilar\MiscAPI\Logger\Logger;
use Magento\Store\Model\ScopeInterface;

class SapHierarchy implements OptionSourceInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;


    const SAP_URI = 'codilar_erp_attributeset/sap_erp_oauth/sap_hierarchy_request_url';
    const SAP_ENDPOINT = 'codilar_erp_attributeset/sap_erp_oauth/sap_hierarchy_endpoint';
    const SAP_TOKEN = 'codilar_erp_attributeset/sap_erp_oauth/sap_hierarchy_token';

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Http $request
     * @param EncryptorInterface $encryptor
     * @param ClientFactory $clientFactory
     * @param ResponseFactory $responseFactory
     * @param HandlerStack $stack
     * @param Json $json
     * @param Logger $loggerResponse
     */
    public function __construct
    (
        ScopeConfigInterface $scopeConfig,
        Http                 $request,
        EncryptorInterface   $encryptor,
        ClientFactory        $clientFactory,
        ResponseFactory      $responseFactory,
        HandlerStack         $stack,
        Json                 $json,
        Logger               $loggerResponse
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->_encryptor = $encryptor;
        $this->stack = $stack;
        $this->json = $json;
        $this->loggerResponse = $loggerResponse;

    }

    /**
     * @return mixed
     */
    public function getSapRequestUri()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue(self::SAP_URI, ScopeInterface::SCOPE_STORE);


    }

    public function getSapEndpoint()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue(self::SAP_ENDPOINT, ScopeInterface::SCOPE_STORE);


    }

    public function getSapToken()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue(self::SAP_TOKEN, ScopeInterface::SCOPE_STORE);


    }


    /**
     * @return array|void
     */
    public function toOptionArray()
    {
        try {
            $finalData = [];

            list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($finalData);

            $response = $this->doRequest($apiRequestEndpoint, $requestMethod, $params);

            $status = $response->getStatusCode();
            $responseBody = $response->getBody();
            $responseContent = $responseBody->getContents();
            $responseDecodee = json_decode($responseContent, true);

            $result = [];

            if (is_array($responseDecodee) || is_object($responseDecodee)) {
                $items = $responseDecodee['ZapiGetProductHierarchyResponse'][0]['EtProducthierarchycodelist'][0]['item'];
                foreach ($items as $item) {
                    $result[] = ['value' => $item['Producthierarchy'][0], 'label' => $item['Description'][0]];

                }
            }


            return $result;
        } catch (\Exception $e) {
            $this->loggerResponse->critical($e->getMessage() . ' ' . 'SAP HIERARCHY API EXCEPTION');
        }//end try
    }


    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::toOptionArray as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * @param $finalBrandData
     * @return array
     */
    private function prepareParams($finalBrandData): array
    {
        $apiRequestEndpoint = $this->getSapEndpoint();
        $requestMethod = Request::METHOD_GET;
        $params = $finalBrandData;


        // collect param data
        $bodyJson = $this->json->serialize($finalBrandData);

        $params['form_params'] = json_decode($bodyJson, true);
        $params['headers'] = [
//            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => $this->getSapToken()
        ];
        return [
            $apiRequestEndpoint,
            $requestMethod,
            $params,
        ];

    }//end prepareParams()

    /**
     * @param $apiRequestEndpoint
     * @param $requestMethod
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
                    'base_uri' => $this->getSapRequestUri(),
                    'handler' => $tapMiddleware($stack),
                ],
            ]
        );

        try {
            $response = $client->request('GET', $apiRequestEndpoint, $params);
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


}
