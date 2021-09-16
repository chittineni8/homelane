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

/**
 * Customers Token resolver, used for GraphQL request processing.
 */
class InitiatePasswordResetWithOTP implements ResolverInterface
{
    /**
     * @var SMSProfieApiServicesInterface
     */
    private $smsProfieApiServices;

    /**
     * @param SMSProfieApiServicesInterface $customerTokenService
     */
    public function __construct(
        SMSProfieApiServicesInterface $smsProfieApiServices
    ) {
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

        if (!isset($args['template']) || empty($args['template'])) {
            throw new GraphQlInputException(__('Specify the "template" value.'));
        }

        if (!isset($args['websiteId']) || empty($args['websiteId'])) {
            throw new GraphQlInputException(__('Specify the "websiteId" value.'));
        }

        try {
            $response = $this->smsProfieApiServices->initiatePasswordResetWithOTP($args['mobile'], $args['otp'], $args['template'], $args['websiteId']);
            if ($response) {
                return ['message'=>'Password Change Successfully'];
            } else {
                return ['message'=>"Password doesn't change"];
            }
        } catch (AuthenticationException $e) {
            throw new GraphQlAuthenticationException(__($e->getMessage()), $e);
        }
    }
}
