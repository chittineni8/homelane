<?php
namespace Codilar\MiscAPI\Logger;

use Monolog\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
   /**
    * Logging level
    * @var int
    */
   protected $loggerType = Logger::NOTICE;

   /**
    * File name
    * @var string
    */
   protected $fileName = '/var/log/codilar_erp_api.log';
}
