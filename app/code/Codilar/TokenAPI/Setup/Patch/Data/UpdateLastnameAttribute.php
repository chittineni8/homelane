<?php
/**
 * UpdateLastnameAttribute.php
 *
 * @package     Homelane
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 */

namespace Codilar\TokenAPI\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class UpdateLastnameAttribute
 *
 * @package     Homelane
 * @description UpdateLastnameAttribute class for updating customer lastname attribute
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * UpdateLastnameAttribute class for updating customer lastname attribute
 */
class UpdateLastnameAttribute implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->updateAttribute(
                Customer::ENTITY,
                    'lastname',
                    'is_required',
                    false
            );
        $this->moduleDataSetup->getConnection()->endSetup();
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getVersion()
    {
        return '2.0.15';
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}
