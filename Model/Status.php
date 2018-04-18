<?php

/**
 * Acquia/CommerceManager/Model/Status
 *
 * Acquia Commerce Manager Status API Data Object
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use Acquia\CommerceManager\Api\Data\StatusInterface;

/**
 * Status
 *
 * Acquia Commerce Manager Status API Data Object
 */
class Status implements StatusInterface
{
    /**
     * Status
     * @var int $status
     */
    protected $status;

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {
        return ($this->status);
    }

    /**
     * setStatus
     *
     * Set the status.
     *
     * @param int $status The status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = (int)$status;
        return ($this);
    }
}
