<?php

/**
 * Acquia/CommerceManager/Api/VerifyConfigurationInterface.php
 *
 * Acquia Commerce Manager Cart Api Extended Operations
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Api;

/**
 * VerifyConfigurationInterface
 *
 * Acquia Commerce Manager Verification endpoint
 * @api
 */
interface VerifyConfigurationInterface
{
    /**
     * Unassociates all carts that are currently attached to a customer.
     *
     * @return \Acquia\CommerceManager\Api\Data\VerifyConfigurationInterface
     */
    public function verifyConfiguration();

}
