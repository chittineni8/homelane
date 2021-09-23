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
class CreateCustomerTokenWithOtp implements ResolverInterface
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
        if (!isset($args['mobile']) || empty($args['mobile'])) {
            throw new GraphQlInputException(__('Specify the "mobile" value.'));
        }

        if (!isset($args['otp']) || empty($args['otp'])) {
            throw new GraphQlInputException(__('Specify the "otp" value.'));
        }

        if (!isset($args['websiteId']) || empty($args['websiteId'])) {
            throw new GraphQlInputException(__('Specify the "websiteId" value.'));
        }

        try {
            $response = $this->smsProfieApiServices->createCustomerTokenWithOtp($args['mobile'], $args['otp'], $args['websiteId']);
            return ['message'=> "Successfully Customer Token Generated", 'token' => $response];
        } catch (AuthenticationException $e) {
            throw new GraphQlAuthenticationException(__($e->getMessage()), $e);
        }
    }
}
