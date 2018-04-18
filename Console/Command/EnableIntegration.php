<?php

/**
 * Acquia/CommerceManager/Console/Command/EnableIntegration.php
 *
 * Acquia Commerce Connector Enable Integration Command.
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Console\Command;

use Magento\Integration\Model\IntegrationFactory;
use Magento\Integration\Model\Oauth\TokenFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command Enable integration for enabling AcquiaConnector integration.
 * @package Acquia\CommerceManager\Console
 */
class EnableIntegration extends Command
{
    /**
     * ObjectManager instance.
     *
     * @var IntegrationFactory
     */
    private $integrationFactory;

    /**
     * Used to create the token.
     *
     * @var TokenFactory
     */
    private $tokenFactory;

    /**
     * Constructor.
     *
     * @param IntegrationFactory $integrationFactory
     * @param TokenFactory $tokenFactory
     */
    public function __construct(
        IntegrationFactory $integrationFactory,
        TokenFactory $tokenFactory
    ) {
        $this->integrationFactory = $integrationFactory;
        $this->tokenFactory = $tokenFactory;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('acquia:enable-integration');
        $this->setDescription('Enable AcquiaConnector integration.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Meet code sniff standards (must use all passed in parameters)
        $dummy = $input;
        unset($dummy);

        // Check if Integration already exists.
        /** @var \Magento\Integration\Model\Integration $integration */
        $integration = $this->integrationFactory->create()
            ->load('AcquiaConnector', 'name');
        if (!empty($integration)) {
            try {
                $consumerId = $integration->getConsumerId();

                // Authorize Consumer and create Access Token and Access Token Secret.
                /** @var \Magento\Integration\Model\Oauth\Token $token */
                $token = $this->tokenFactory->create();
                $uri = $token->createVerifierToken($consumerId);
                $token->setType('access');
                $token->save();

                // Enable integration.
                $integration->setStatus(1);
                $integration->save();
                $output->writeln("Integration was enabled.");
            } catch (\Exception $e) {
                $output->writeln("Error : " . $e->getMessage());
            }
        } else {
            $output->writeln("Integration wasn't found!");
        }
    }
}
