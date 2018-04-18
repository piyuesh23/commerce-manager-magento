<?php

/**
 * Api/CatalogRuleRepositoryInterface.php
 *
 * Catalog Price Rule API Repository Interface
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Api;

/**
 * CatalogRuleRepositoryInterface
 *
 * Catalog Price Rule API Repository Interface
 */
interface CatalogRuleRepositoryInterface
{
    /**
     * getCount
     *
     * Retrieve a count of the Catalog Price rules in the system.
     *
     * @return int $ruleCount
     */
    public function getCount();

    /**
     * getList
     *
     * Retrieve a list of all Catalog Price rules, optionally with
     * a page size and count (by default returns all).
     *
     * @param int|null $pageSize Page Size
     * @param int|null $pageCount Page Count
     *
     * @return \Acquia\CommerceManager\Api\Data\ExtendedCatalogRuleInterface[]
     */
    public function getList($pageSize = 0, $pageCount = 0);
}
