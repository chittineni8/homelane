<?php
namespace Codilar\ProductVisibility\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;


class ProductVisibility implements DataPatchInterface {

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
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }
    public function apply() {

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,'product_visibility', [
            'group' => 'General',
            'type' => 'text',
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            'frontend' => '',
            'sort_order' => 210,
            'label' => 'Product Visibility',
            'input' => 'multiselect',
            'class' => '',
            'source' => 'Codilar\ProductVisibility\Model\Source\Config\Product\ProductVisibilityOption',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'apply_to' => ''
        ]);
    }
    /** Adding Revert Part --- that will undo your patch when uninstalling Codilar_ProductVisibility => So you don't have to delete from DB  **/
    /**
     * {@inheritdoc}
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(
            Product::ENTITY,
            self::product_visibility
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies() {
        return [];
    }
    public function getAliases() {
        return [];
    }
}
