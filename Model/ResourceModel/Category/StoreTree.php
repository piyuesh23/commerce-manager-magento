<?php

/**
 * Acquia/CommerceManager/Model/ResourceModel/Category/StoreTree.php
 *
 * Category Tree Resource Supporting Forced Reload
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\ResourceModel\Category;

use Magento\Catalog\Model\ResourceModel\Category\Tree;

/**
 * StoreTree
 *
 * Category Tree Resource Supporting Forced Reload
 */
class StoreTree extends Tree
{
    /**
     * setForceReload
     *
     * Force the next node load operation to rebuild the tree query,
     * for building multiple store trees.
     *
     * @return self
     */
    public function setForceReload()
    {
        $this->_loaded = false;

        return ($this);
    }
}
