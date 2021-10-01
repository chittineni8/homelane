<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_BetterWishlist
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterWishlist\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Mageplaza\BetterWishlist\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (!$installer->tableExists('mageplaza_wishlist_item')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_wishlist_item'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ], 'ID')
                ->addColumn('wishlist_item_id', Table::TYPE_INTEGER, null, ['nullable' => false], 'Wishlist Item Id')
                ->addColumn('category_id', Table::TYPE_TEXT, 255, [], 'Category Id')
                ->addColumn('category_name', Table::TYPE_TEXT, 255, [], 'Category Name')
                ->addColumn('qty', Table::TYPE_DECIMAL, '12,4', [], 'Quantity')
                ->addColumn('added_at', Table::TYPE_TIMESTAMP, null, [], 'Added At')
                ->addIndex(
                    $installer->getIdxName('mageplaza_wishlist_item', ['wishlist_item_id']),
                    ['wishlist_item_id']
                )
                ->setComment('Mageplaza Wishlist Item Table');

            $installer->getConnection()->createTable($table);
        }
        if (!$installer->tableExists('mageplaza_wishlist_user_category')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_wishlist_user_category'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ], 'ID')
                ->addColumn('customer_id', Table::TYPE_INTEGER, null, ['nullable' => false], 'Customer Id')
                ->addColumn('category_id', Table::TYPE_TEXT, 255, [], 'Category Id')
                ->addColumn('category_name', Table::TYPE_TEXT, 255, [], 'Category Name')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Updated At')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Created At')
                ->addIndex(
                    $installer->getIdxName('mageplaza_wishlist_user_category', ['customer_id', 'category_id']),
                    ['customer_id', 'category_id']
                )
                ->setComment('Mageplaza Wishlist User Category Table');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
