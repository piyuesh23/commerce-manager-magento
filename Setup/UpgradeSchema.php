<?php

/**
 * Acquia/CommerceManager/Setup/UpgradeSchema.php
 *
 * Acquia Commerce Manager Integration Schema Install
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * UpgradeSchema
 *
 * Acquia Commerce Manager Integration Schema Install
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    private $logger;

    /**
     * Init
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->logger->info("UPGRADED ACM MODULE SCHEMA FROM ".$context->getVersion());
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            //code to upgrade to 1.0.2
            $this->createSalesRuleIndexTable($setup);
            $this->logger->info("Upgraded to 1.0.2");
        }
        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            //code to upgrade to 1.1.1
            $this->logger->info("Upgraded to 1.1.1");
        }

        $setup->endSetup();
    }

    /**
     * createSalesRuleIndexTable
     *
     * Create index table for sales rule / product matches and prices for
     * Acquia Commerce API.
     *
     * @param SchemaSetupInterface $setup
     *
     * @return void
     */
    protected function createSalesRuleIndexTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('acq_salesrule_product'))
            ->addColumn(
                'rule_product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Product Id'
            )
            //triggers Magento Marketplace code sniff warning. Consider renaming column acq_rule_id
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Rule Id'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Product Id'
            )
            ->addColumn(
                'rule_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                [12, 4],
                ['nullable' => false, 'default' => '0.0000'],
                'Rule Price'
            )
            ->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website Id'
            )
            ->addIndex(
                $setup->getIdxName(
                    'acq_salesrule_product',
                    ['rule_id', 'product_id', 'website_id'],
                    true
                ),
                ['rule_id', 'product_id', 'website_id'],
                ['type' => 'unique']
            )
            ->addIndex(
                $setup->getIdxName('acq_salesrule_product', ['website_id']),
                ['website_id']
            )
            ->addIndex(
                $setup->getIdxName('acq_salesrule_product', ['product_id']),
                ['product_id']
            )
            ->setComment('Acquia Commerce Manager SalesRule Product');

        $setup->getConnection()->createTable($table);
    }
}
