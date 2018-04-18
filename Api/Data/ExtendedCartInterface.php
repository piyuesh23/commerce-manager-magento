<?php

/**
 * Api/Data/ExtendedCartInterface.php
 *
 * Acquia Commerce Manager Extended Cart API Data Object
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Api\Data;

use Magento\Quote\Api\Data\CartInterface;

/**
 * ExtendedCartInterface
 *
 * Acquia Commerce Manager Extended Cart API Data Object
 */
interface ExtendedCartInterface extends CartInterface
{
    /**
     * getAppliedRuleIds
     *
     * Get the list of applied sales rules for the quote,
     * as a comma seperated string.
     *
     * @return string
     */
    public function getAppliedRuleIds();
}
