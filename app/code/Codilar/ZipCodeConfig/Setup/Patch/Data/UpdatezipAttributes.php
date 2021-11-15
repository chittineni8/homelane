<?php
/**
 * UpdateLastnameAttribute.php
 *
 * @package     Homelane
 * @author      Abhinav Vinayak <abhinav.v@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 */

namespace Codilar\ZipCodeConfig\Setup\Patch\Data;


use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class UpdateLastnameAttribute
 *
 * @package     Homelane
 * @description UpdatezipAttributes class for updating MyZipCodeValidator Scope
 * @author      Abhinav Vinayak <abhinav.v@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * UpdateLastnameAttribute class for updating customer lastname attribute
 */
class UpdatezipAttributes implements DataPatchInterface
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
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
         $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
      $eavSetup->updateAttribute(Product::ENTITY, 'available_region', ['is_global' => ScopedAttributeInterface::SCOPE_STORE]);
    $eavSetup->updateAttribute(Product::ENTITY, 'zip_code_validation', ['is_global' => ScopedAttributeInterface::SCOPE_STORE]);
     $this->moduleDataSetup->getConnection()->endSetup();
        return $this;       
    
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
