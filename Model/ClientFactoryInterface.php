<?php

/**
 * Acquia/CommerceManager/Model/ClientFactoryInterface.php
 *
 * Acquia Commerce Manager API Client Factory Interface
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

/**
 * ClientFactoryInterface
 *
 * Acquia Commerce Manager API Client Factory Interface
 */
interface ClientFactoryInterface
{
    /**
     * createClient
     *
     * Create a Connector API Client instance.
     *
     * @param array $options Client Options
     *
     * @return \GuzzleHttp\Client
     */
    public function createClient(array $options = []);
}
