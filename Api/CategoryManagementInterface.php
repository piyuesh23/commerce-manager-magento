<?php

/**
 * Acquia/CommerceManager/Api/CategoryManagementInterface.php
 *
 * Acquia Commerce Manager Customer Api Extended Operations Interface
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Api;

/**
 * CategoryManagementInterface
 *
 * Acquia Commerce Manager Customer Api Extended Operations Interface
 */
interface CategoryManagementInterface
{
    /**
     * Retrieve extended list of categories
     *
     * @param int $rootCategoryId
     * @param int $depth
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @return \Acquia\CommerceManager\Api\Data\ExtendedCategoryTreeInterface
     */
    public function getExtendedTree($rootCategoryId = null, $depth = null);
}
