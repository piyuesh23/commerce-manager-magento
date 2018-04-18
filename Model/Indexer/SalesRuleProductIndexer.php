<?php

/**
 * Acquia/CommerceManager/Model/Indexer/SalesRuleProductIndexer.php
 *
 * Acquia Commerce Manager Sales Rule / Product Indexer
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Indexer;

/**
 * SalesRuleProductIndexer
 *
 * Acquia Commerce Manager Sales Rule / Product Indexer
 */
class SalesRuleProductIndexer extends SalesRuleIndexer
{
    /**
     * {@inheritDoc}
     */
    protected function reindexList(array $ids)
    {
        $this->indexBuilder->reindexByRuleIds($ids);
        $this->getCacheContext()->registerTags($this->getIdentities());
    }

    /**
     * {@inheritDoc}
     */
    protected function reindexRow($id)
    {
        $this->indexBuilder->reindexByRuleIds([$id]);
    }
}
