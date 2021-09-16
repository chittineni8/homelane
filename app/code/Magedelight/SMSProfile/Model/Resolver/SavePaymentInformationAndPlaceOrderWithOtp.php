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
use Magento\Quote\Api\Data\PaymentInterfaceFactory;
use Magento\Quote\Api\Data\AddressInterfaceFactory;

/**
 * Customers Token resolver, used for GraphQL request processing.
 */
class SavePaymentInformationAndPlaceOrderWithOtp implements ResolverInterface
{
    /**
     * @var SMSProfieApiServicesInterface
     */
    private $smsProfieApiServices;

    /**
     * @var PaymentInterfaceFactory
     */
    protected $paymentInterfaceFactory;

    /**
     * @var AddressInterfaceFactory
     */
    protected $addressInterfaceFactory;
    /**
     * @param SMSProfieApiServicesInterface $customerTokenService
     */
    public function __construct(
        SMSProfieApiServicesInterface $smsProfieApiServices,
        PaymentInterfaceFactory $paymentInterfaceFactory,
        AddressInterfaceFactory $addressInterfaceFactory
    ) {
        $this->paymentInterfaceFactory = $paymentInterfaceFactory;
        $this->smsProfieApiServices = $smsProfieApiServices;
        $this->addressInterfaceFactory = $addressInterfaceFactory;
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
        if (!isset($args['guestCartId']) || empty($args['guestCartId'])) {
            throw new GraphQlInputException(__('Specify the "guestCartId" value.'));
        }

        if (!isset($args['emailId']) || empty($args['emailId'])) {
            throw new GraphQlInputException(__('Specify the "emailId" value.'));
        }

        if (!isset($args['mobile']) || empty($args['mobile'])) {
            throw new GraphQlInputException(__('Specify the "mobile" value.'));
        }

        if (!isset($args['otp']) || empty($args['otp'])) {
            throw new GraphQlInputException(__('Specify the "otp" value.'));
        }

        if (!isset($args['paymentInformation']) || empty($args['paymentInformation'])) {
            throw new GraphQlInputException(__('Specify the "paymentMethod" value.'));
        }

        try {
            ['paymentMethod' => $paymentMethod, 'billing_address' => $billingAddress ] = $args['paymentInformation'];
            $paymentMethod = $this->paymentInterfaceFactory->create([ 'data' => $paymentMethod ]);
            $billingAddressObject = $this->addressInterfaceFactory->create([ 'data' => $billingAddress ]);

            $response = $this->smsProfieApiServices->savePaymentInformationAndPlaceOrderWithOtp($args['guestCartId'], $args['emailId'], $args['mobile'], $args['otp'], $paymentMethod, $billingAddressObject);
            return ["message" => "Payment Information Successfully Save", "orderId" => $response];
        } catch (AuthenticationException $e) {
            throw new GraphQlAuthenticationException(__($e->getMessage()), $e);
        }
    }
}
