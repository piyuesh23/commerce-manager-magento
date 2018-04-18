<?php

/**
 * Acquia/CommerceManager/Api/Data/StatusInterface.php
 *
 * Acquia Commerce Manager Status API Data Object
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Api\Data;

/**
 * StatusInterface
 *
 * Acquia Commerce Manager Status API Data Object
 */
interface StatusInterface
{
    /**
     * getStatus
     *
     * Get the status.
     *
     * @return int $status
     */
    public function getStatus();
}
