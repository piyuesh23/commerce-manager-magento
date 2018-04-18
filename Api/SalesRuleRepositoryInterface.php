<?php

/**
 * Api/SalesRuleRepositoryInterface.php
 *
 * Acquia Commerce Manager Sales / Cart Rule Repository Interface
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Api;

/**
 * SalesRuleRepositoryInterface
 *
 * Acquia Commerce Manager Sales / Cart Rule Repository Interface
 */
interface SalesRuleRepositoryInterface
{
    /**
     * getList
     *
     * Get a list of enabled sales / cart price rules, along with product
     * discount data from the index table. Optionally filter the entire
     * result by rule ID, or filter the product discount data by
     * product ID.
     *
     * @param int|null $ruleId Sales Rule ID
     * @param int|null $productId Product ID
     *
     * @return \Acquia\CommerceManager\Api\Data\ExtendedSalesRuleInterface[]
     */
    public function getList($ruleId = 0, $productId = 0);
}
