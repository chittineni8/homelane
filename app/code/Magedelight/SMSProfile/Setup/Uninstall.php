<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */

namespace Magedelight\SMSProfile\Setup;
 
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;
 
class Uninstall implements UninstallInterface
{
    const TBL_SMSPROFILELOG = 'magedelight_smsprofilelog';
    const TBL_SMSPROFILETEMPLATES = 'magedelight_smsprofiletemplates';
    const TBL_SMSPROFILEOTP = 'magedelight_smsprofileotp';

    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
         $this->eavSetupFactory = $eavSetupFactory;
    }


    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $tableToDrop = [
            self::TBL_SMSPROFILELOG,
            self::TBL_SMSPROFILETEMPLATES,
            self::TBL_SMSPROFILEOTP,
        ];
        $this->_dropTable($setup, $tableToDrop);
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'customer_mobile');
        $setup->endSetup();
    }

    protected function _dropTable($setup, $tableName)
    {
        $connection = $setup->getConnection();
        foreach ($tableName as $tableName) {
            $connection->dropTable($connection->getTableName($tableName));
        }
    }
}
