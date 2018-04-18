<?php

/**
 * Acquia/CommerceManager/Setup/InstallData.php
 *
 * Acquia Commerce Manager Integration Install
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Setup;

use Magento\Integration\Model\ConfigBasedIntegrationManager;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * InstallData
 *
 * Acquia Commerce Manager Integration Install
 */
final class InstallData implements InstallDataInterface
{
    /**
     * Integration Manager
     * @var ConfigBasedIntegrationManager $integrationManager
     */
    private $integrationManager;

    /**
     * Constructor
     *
     * @param ConfigBasedIntegrationManager $integrationManager Integrations Manager
     *
     */
    public function __construct(ConfigBasedIntegrationManager $integrationManager)
    {
        $this->integrationManager = $integrationManager;
    }

    /**
     * install
     *
     * {@inheritDoc}
     *
     * @param ModuleDataSetupInterface $setup Module Data Setup
     * @param ModuleContextInterface $context Module Context
     *
     * @return void
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->integrationManager->processIntegrationConfig(['AcquiaConnector']);

        // The unused $setup and $context are here as a place holder when/if you want to use it later
        // These lines prevent the codesniffer from being upset about unused parameters
        $dummy = $setup;
        $dummy = $context;
        unset($dummy);
    }
}
