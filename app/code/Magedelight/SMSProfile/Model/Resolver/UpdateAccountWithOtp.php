<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magedelight\SMSProfile\Model\Resolver;

use Magento\Authorization\Model\UserContextInterface;
use Magento\CustomerGraphQl\Model\Customer\ExtractCustomerData;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthenticationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magedelight\SMSProfile\Api\SMSProfieApiServicesInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;

/**
 * Customers Token resolver, used for GraphQL request processing.
 */
class UpdateAccountWithOtp implements ResolverInterface
{
    /**
     * @var GetCustomer
     */
    private $getCustomer;
    /**
     * @var SMSProfieApiServicesInterface
     */
    private $smsProfieApiServices;

    /**
     * @var PaymentInterfaceFactory
     */
    protected $customerInterfaceFactory;

    /**
     * @param SMSProfieApiServicesInterface $customerTokenService
     */
    public function __construct(
        SMSProfieApiServicesInterface $smsProfieApiServices,
        CustomerInterfaceFactory $customerInterfaceFactory,
        ExtractCustomerData $extractCustomerData,
        GetCustomer $getCustomer
    ) {
        $this->smsProfieApiServices = $smsProfieApiServices;
        $this->extractCustomerData = $extractCustomerData;
        $this->getCustomer = $getCustomer;
        $this->customerInterfaceFactory = $customerInterfaceFactory;
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
        if (!isset($args['input']) || !is_array($args['input']) || empty($args['input'])) {
            throw new GraphQlInputException(__('"input" value should be specified'));
        }

        if (!isset($args['password']) || empty($args['password'])) {
            throw new GraphQlInputException(__('Specify the "password" value.'));
        }

        if (!isset($args['mobile']) || empty($args['mobile'])) {
            throw new GraphQlInputException(__('Specify the "mobile" value.'));
        }

        if (!isset($args['otp']) || empty($args['otp'])) {
            throw new GraphQlInputException(__('Specify the "otp" value.'));
        }

        try {
            $customer = $this->getCustomer->execute($context);
            $args['input']['id'] = $customer->getId();
            $args['input']['website_id'] = $customer->getWebsiteId();
            $customerData = $this->customerInterfaceFactory->create([ 'data' => $args['input'] ]);
            $customer = $this->smsProfieApiServices->updateAccountWithOtp($customerData, $args['password'], $args['mobile'], $args['otp'], $args['websiteId']);
            $data = $this->extractCustomerData->execute($customer);
            return ['customer' => $data];
        } catch (AuthenticationException $e) {
            throw new GraphQlAuthenticationException(__($e->getMessage()), $e);
        }
    }
}
