<?php

namespace Codilar\TokenAPI\Setup;

use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config          $eavConfig
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);


        //  Field
        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'homelane_user_id', [
                'label' => 'HOMELANE User Id',
                'system' => 0,
                'position' => 720,
                'sort_order' => 720,
                'visible' => true,
                'note' => '',
                'type' => 'varchar',
                'input' => 'text',
            ]
        );

        $this->getEavConfig()->getAttribute('customer', 'homelane_user_id')->setData('is_user_defined', 1)->setData('is_required', 0)->setData('default_value', '')->setData('used_in_forms', ['adminhtml_customer', 'checkout_register', 'customer_account_create', 'customer_account_edit', 'adminhtml_checkout'])->save();

    }

    public function getEavConfig()
    {
        return $this->eavConfig;
    }
}
