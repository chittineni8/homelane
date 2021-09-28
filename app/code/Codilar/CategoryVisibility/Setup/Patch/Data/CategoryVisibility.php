<?php
namespace Codilar\CategoryVisibility\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
//use Magento\Framework\Setup\InstallDataInterface;
//use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


class CategoryVisibility implements DataPatchInterface
{


    private $moduleDataSetup;

    private $eavSetupFactory;
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory)
    {
        $this->moduleDataSetup=$moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        /**
         * Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'cat_custom_attribute',
            [
                'type' => 'varchar',
                'label' => 'Category visibility',
                'input' => 'multiselect',
                'required' => false,
                'source' => 'Codilar\CategoryVisibility\Model\Category\Attribute\Source\Option',
                'sort_order' => 100,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information',
                'user_defined' => false,
                'default' => null,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => true,
                'backend'=> 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',


            ]
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'cat_custom_attribute');

        $this->moduleDataSetup->getConnection()->endSetup();
    }
    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [

        ];
    }

}
