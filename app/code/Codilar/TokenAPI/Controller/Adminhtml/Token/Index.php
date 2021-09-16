<?php

namespace Codilar\TokenAPI\Controller\Adminhtml\Token;

use Magento\Backend\App\Action;
use Codilar\TokenAPI\Logger\Logger;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Codilar\TokenAPI\Model\Common\Callapi;

// use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Customer\Block\Form\Register;

class Index extends \Magento\Backend\App\Action
{
    protected $_publicActions = ['index'];
    protected $resultLayoutFactory;
    protected $callapi;
    /**
     * @var LoggerResponse
     */
    private $loggerResponse;
    // protected $json;
    protected $register;

    public function __construct(
        Context       $context,
        Logger        $loggerResponse,
        LayoutFactory $resultLayoutFactory,
        PageFactory   $resultPageFactory,
        Callapi       $callapi,
        // Json          $json,
        Register      $register


    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->callapi = $callapi;
        $this->loggerResponse = $loggerResponse;
        // $this->json = $json;
        $this->register = $register;

    }

    public function execute()
    {

        echo $this->callapi->getToken();


        // $parambody = ['user' => $this->callapi->getUsername(), 'password' => $this->callapi->getPassword()];
        // //    $params=$this->prepareParams($parambody);
        // list($apiRequestEndpoint, $requestMethod, $params) = $this->prepareParams($parambody);

        // $response = $this->callapi->doRequest($apiRequestEndpoint,$requestMethod,$params);
        // //  print_r($response);
        //  $status = $response->getStatusCode();
        // $responseBody = $response->getBody();
        // $responseContent = $responseBody->getContents();
        // $responseDecodee = json_decode($responseContent, true);
        // $token=$responseDecodee['token'];
        // if($status == 200):
        // return $token;
        //   else:
        // $this->loggerResponse->addInfo("========================TOKEN ERROR========================");
        // $this->loggerResponse->addInfo("Error".$status."user or password not matched.");
        // $this->loggerResponse->addInfo("============================================================");
        //   endif;
    }




    /**
     * @param $finalBrandData
     * @return array
     */
//     private function prepareParams($finalBrandData): array
//     {
//         $apiRequestEndpoint = $this->callapi->getApiEndpoint();
//         $requestMethod = Request::METHOD_POST;
//         $params = [];

//         // collect param data
//         $bodyJson = $this->json->serialize($finalBrandData);
// //        $params['form_params'] = json_decode($bodyJson, true);
//         $params['body'] = $bodyJson;
//         // $params['debug'] = true;
// //        $params['http_errors'] = false;
// //        $params['handler'] = $tapMiddleware($stack);
//         $params['headers'] = [
//             'Content-Type' => 'application/json'
//         ];
//         return array($apiRequestEndpoint, $requestMethod, $params);
//     }

}
