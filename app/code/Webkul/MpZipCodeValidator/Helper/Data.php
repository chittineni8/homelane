<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpZipCodeValidator
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\MpZipCodeValidator\Helper;

use Magento\Framework\App\Http\Context as HttpContext;

/**
 * Webkul MpZipCodeValidator Helper Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Webkul\MpZipCodeValidator\Logger\Logger
     */
    protected $logger;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Webkul\MpZipCodeValidator\Logger\Logger $logger
     * @param HttpContext $httpContext
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Webkul\MpZipCodeValidator\Logger\Logger $logger,
        HttpContext $httpContext
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->httpContext = $httpContext;
    }

    /**
     * [logDataInLogger  is used to log the data in the mpzipcodevalidator.log file]
     * @param  string $data
     * @return
     */
    public function logDataInLogger($data)
    {
        $this->logger->info($data);
    }

    /**
     * Return Customer id.
     *
     * @return bool|0|1
     */
    public function getCustomerId()
    {
        return $this->httpContext->getValue('customer_id');
    }

    /**
     * Get zipcode validation options
     *
     * @return array
     */
    public function getZipCodeValidationOptions()
    {
        return $options = [
            "1" => __('Disable'),
            "0" => __('Enable')
        ];
    }
}
