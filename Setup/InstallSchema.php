<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_Ups
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Ups\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Install tables
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getTable('bss_ups_data');
        if (!$installer->tableExists('bss_ups_data')) {
            $_tableUpsData = $installer->getConnection()
                ->newTable($table)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    255,
                    ['nullable' => true],
                    'Order Id'
                )
                ->addColumn(
                    'versanddatum',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Versanddatum'
                )
                ->addColumn(
                    'zustelldatum',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Zustelldatum'
                )
                ->addColumn(
                    'paketnummer',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Paketnummer'
                )
                ->addColumn(
                    'paketanzahl',
                    Table::TYPE_SMALLINT,
                    255,
                    [],
                    'Paketanzahl'
                )
                ->addColumn(
                    'paketkilo',
                    Table::TYPE_TEXT,
                    null,
                    [],
                    'Paketkilo'
                )
                ->addColumn(
                    'nach',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Nach'
                )
                ->addColumn(
                    'paketart',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Paketart'
                )
                ->addColumn(
                    'kosten',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Kosten'
                )
                ->addColumn(
                    'nachnahme',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Nachnahme'
                )
                ->addColumn(
                    'retour',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Retour'
                )                             
                ->setComment(
                    'Ups Data'
                ); 
                $installer->getConnection()->createTable($_tableUpsData);
        }
        
        $table = $installer->getTable('bss_ups_message');
        if (!$installer->tableExists('bss_ups_message')) {
            $_tableUpsMessage = $installer->getConnection()
                ->newTable($table)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Order Id'
                )                
                ->addColumn(
                    'error',
                    Table::TYPE_SMALLINT,
                    255,
                    ['nullable' => true],
                    'Error'
                )
                ->addColumn(
                    'message',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Message'
                )
                ->addColumn(
                    'read',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Read'
                )
                ->addColumn(
                    'create',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => true],
                    'Create'
                )
                ->addIndex(
                    $installer->getIdxName('bss_ups_message', ['order_id']),
                    ['order_id']
                )
                ->setComment(
                    'Ups Message'
                );
                 $installer->getConnection()->createTable($_tableUpsMessage);
        }
        $installer->endSetup();
    }
}
