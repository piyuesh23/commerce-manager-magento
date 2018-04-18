<?php

/**
 * Acquia/CommerceManager/Model/Indexer/ProductSalesRuleIndexer.php
 *
 * Acquia Commerce Manager Product / Sales Rule Indexer
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Indexer;

/**
 * ProductSalesRuleIndexer
 *
 * Acquia Commerce Manager Product / Sales Rule Indexer
 */
class ProductSalesRuleIndexer extends SalesRuleIndexer
{
    /**
     * {@inheritDoc}
     */
    protected function reindexList(array $ids)
    {
        $this->indexBuilder->reindexByProductIds(array_unique($ids));
        $this->getCacheContext()->registerEntities(\Magento\Catalog\Model\Product::CACHE_TAG, $ids);
    }

    /**
     * {@inheritDoc}
     */
    protected function reindexRow($id)
    {
        $this->indexBuilder->reindexByProductIds([$id]);
    }
}
