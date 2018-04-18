<?php

/**
 * Acquia/CommerceManager/Model/GuzzleClientFactory.php
 *
 * Acquia Commerce Manager Guzzle API Client Factory
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use GuzzleHttp\Client;

/**
 * GuzzleClientFactory
 *
 * Acquia Commerce Manager Guzzle API Client Factory
 */
final class GuzzleClientFactory implements ClientFactoryInterface
{
    /**
     * createClient
     *
     * {@inheritDoc}
     *
     * @param array $options Client Options
     *
     * @return Client
     */
    public function createClient(array $options = [])
    {
        return new Client($options);
    }
}
