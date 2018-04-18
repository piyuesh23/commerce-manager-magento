<?php

namespace Acquia\CommerceManager\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    private $logger;

    /**
     * @var \Magento\Integration\Api\IntegrationServiceInterface
     */
    private $integrationService;

    /**
     * Init
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Integration\Api\IntegrationServiceInterface $integrationService,
        \Psr\Log\LoggerInterface $logger //log injection
    ) {
        $this->integrationService = $integrationService;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->logger->info("UPGRADED ACM MODULE DATA FROM ".$context->getVersion());
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            //code to upgrade to 1.0.1
            $this->logger->info("Upgraded to 1.0.1");
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            //code to upgrade to 1.0.2
            $this->logger->info("Upgraded to 1.0.2");
        }

        if (version_compare($context->getVersion(), '1.1.1') < 0) {
            //code to upgrade to 1.1.1
            $this->logger->info("Upgraded to 1.1.1");
        }

        if (version_compare($context->getVersion(), '1.1.2') < 0) {
            //code to upgrade to 1.1.2

            //see if there is an incumbent Acquia Commerce Connector integration
            $deprecatedIntegration = $this->integrationService->findByName("AcquiaConductor");
            if ($deprecatedIntegration->getId()) {
                $oldData = $this->integrationService->delete($deprecatedIntegration->getId());
            }
            $this->logger->info("Upgraded to 1.1.2");
        }

        if (version_compare($context->getVersion(), '1.1.3') < 0) {
            //code to upgrade to 1.1.3
            $this->logger->info("Upgraded to 1.1.3");
        }

        $setup->endSetup();
    }
}
