<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpZipCodeValidator
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpZipCodeValidator\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class CreateAttributes implements DataPatchInterface
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
     * @var \Webkul\Marketplace\Model\ControllersRepository
     */
    protected $controllerRepository;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        \Webkul\Marketplace\Model\ControllersRepository $controllerRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->controllerRepository = $controllerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $regionSource = \Webkul\MpZipCodeValidator\Model\Config\Source\RegionOptions::class;
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'available_region',
            [
                'type' => 'text',
                'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                'frontend' => '',
                'label' => 'Available Regions',
                'input' => 'multiselect',
                'group' => 'General',
                'class' => '',
                'source' => $regionSource,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => false,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to'=>'simple,configurable,bundle,grouped'
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'zip_code_validation',
            [
                'type' => 'text',
                'label' => 'Zip Code Validation',
                'input' => 'select',
                'group' => 'General',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => false,
                'required' => false,
                'user_defined' => true,
                'default' => "2",
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to'=>'simple,configurable,bundle,grouped'
            ]
        );

        $data = [];
        if (empty($this->controllerRepository->getByPath('mpzipcodevalidator/region/add'))) {
            $data[] = [
                'module_name' => 'Webkul_MpZipCodeValidator',
                'controller_path' => 'mpzipcodevalidator/region/add',
                'label' => 'Add Region',
                'is_child' => '0',
                'parent_id' => '0',
            ];
        }
        if (empty($this->controllerRepository->getByPath('mpzipcodevalidator/region/view'))) {
            $data[] = [
                'module_name' => 'Webkul_MpZipCodeValidator',
                'controller_path' => 'mpzipcodevalidator/region/view',
                'label' => 'View Region',
                'is_child' => '0',
                'parent_id' => '0',
            ];
        }
        if ($data) {
            $setup->getConnection()
                ->insertMultiple($setup->getTable('marketplace_controller_list'), $data);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
