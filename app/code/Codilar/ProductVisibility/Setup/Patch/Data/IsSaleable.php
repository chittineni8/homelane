<?php

namespace Codilar\ProductVisibility\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class Vendor
 * @package Codilar\ProductVisibility\Setup\Patch\Data
 */
class IsSaleable implements DataPatchInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;


    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;


    /**
     * Vendor constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory          $eavSetupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'issaleable', [
            'group' => 'General',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'sort_order' => 210,
            'label' => 'Is Saleable',
            'input' => 'select',
            'class' => '',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'used_in_product_listing' => true,
            'apply_to' => '',
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true
        ]);
    }
    /** Adding Revert Part => that will undo your patch when uninstalling Codilar_ProductVisibility => So you don't have to delete from DB  **/
    /**
     * {@inheritdoc}
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(
            Product::ENTITY,
            self::issaleable
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
