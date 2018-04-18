<?php

namespace Acquia\CommerceManager\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $installer;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    private $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Install DB tables and such
     *
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $this->installer = $setup;
        $this->installer->startSetup();

        // The unused $context is here as a place holder when/if you want to use it later
        // These lines prevent the codesniffer from being upset about unused parameters
        $dummy = $context;
        unset($dummy);

        $this->installer->endSetup();
    }
}
