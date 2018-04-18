<?php

namespace Acquia\CommerceManager\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface
{
    /**
     * uninstall
     * Uninstall the Acquia Commerce Manager Magento Module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // The unused $context is here as a place holder when/if you want to use it later
        // These lines prevent the codesniffer from being upset about unused parameters
        $dummy = $context;
        unset($dummy);

        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $setup->getConnection();
        $connection->dropTable('acq_salesrule_product');

        $setup->endSetup();
    }
}
