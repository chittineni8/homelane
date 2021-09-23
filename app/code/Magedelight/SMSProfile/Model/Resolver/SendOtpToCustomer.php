<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magedelight\SMSProfile\Model\Resolver;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthenticationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magedelight\SMSProfile\Api\SMSProfieApiServicesInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Customers Token resolver, used for GraphQL request processing.
 */
class SendOtpToCustomer implements ResolverInterface
{
    /**
     * @var SMSProfieApiServicesInterface
     */
    private $smsProfieApiServices;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;
    /**
     * @param SMSProfieApiServicesInterface $customerTokenService
     */
    public function __construct(
        SMSProfieApiServicesInterface $smsProfieApiServices,
        JsonHelper $jsonHelper
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->smsProfieApiServices = $smsProfieApiServices;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($args['storeId']) || empty($args['storeId'])) {
            throw new GraphQlInputException(__('Specify the "storeId" value.'));
        }

        if (!isset($args['mobile']) || empty($args['mobile'])) {
            throw new GraphQlInputException(__('Specify the "email" value.'));
        }

        if (!isset($args['eventType']) || empty($args['eventType'])) {
            throw new GraphQlInputException(__('Specify the "eventType" value.'));
        }

        try {
            $response = $this->smsProfieApiServices->SendOtpToCustomer($args['resend'], $args['storeId'], $args['mobile'], $args['eventType']);
            $result = $this->jsonHelper->jsonDecode($response);
            if (isset($result['Success'])) {
                return ['message'=>'OTP Sent Successfully'];
            } else {
                return ['message'=>'Not Able To Sent OTP'];
            }
        } catch (AuthenticationException $e) {
            throw new GraphQlAuthenticationException(__($e->getMessage()), $e);
        }
    }
}
