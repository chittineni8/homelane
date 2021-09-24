<?php
/**
 * ProductAdditionalAttributes.php
 *
 * @package     Homelane
 * @description ProductAttributes Module to show custom attributes value in PDP tabs
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * ProductAttributes Module to show custom attributes value in PDP tabs
 */

namespace Codilar\ProductAttributes\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Zend_Validate_Exception;

/**
 * Class ProductAdditionalAttributes
 *
 * @package     Homelane
 * @description ProductAdditionalAttributes class for creating custom product attributes
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * ProductAdditionalAttributes class for creating custom product attributes
 */
class ProductAdditionalAttributes implements DataPatchInterface, PatchRevertableInterface
{
    const PDP_TABS_ATTRIBUTES = [
        ['label' => 'Brand', 'code' => 'brand'],
        ['label' => 'Height', 'code' => 'height'],
        ['label' => 'Material', 'code' => 'material'],
        ['label' => 'Warranty', 'code' => 'warranty'],
    ];

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * CmsPageAttribute Constructor
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     *
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        foreach (self::PDP_TABS_ATTRIBUTES as $attribute) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                $attribute['code'],
                [
                    'group' => 'General',
                    'type' => 'varchar',
                    'label' => $attribute['label'],
                    'input' => 'text',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => null,
                    'sort_order' => 260,
                    'source' => '',
                    'is_html_allowed_on_front' => true,
                    'backend' => '',
                    'apply_to' => 'simple,configurable,virtual,bundle,downloadable'
                ]
            );
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        foreach (self::PDP_TABS_ATTRIBUTES as $attribute) {
            $eavSetup->removeAttribute(Product::ENTITY, $attribute['code']);
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }
    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
