<?php

/**
 * Acquia/CommerceManager/Model/Plugin/SalesRuleIndexer/Category.php
 *
 * Acquia Commerce Manager Sales Rule Indexer Category Plugin
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Plugin\SalesRuleIndexer;

use Acquia\CommerceManager\Model\Indexer\ProductSalesRuleProcessor;
use Magento\Catalog\Model\Category as CategoryEntity;

/**
 * Category
 *
 * Acquia Commerce Manager Sales Rule Indexer Category Plugin
 */
class Category
{
    /**
     * Sales Rule Index Product Processor
     * @var ProductSalesRuleProcessor $processor
     */
    protected $processor;

    /**
     * Constructor
     *
     * @param ProductSalesRuleProcessor $processor
     *
     */
    public function __construct(ProductSalesRuleProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * afterSave
     *
     * After Product Category save, update any affected products
     * in the sales rule index.
     *
     * @param CategoryEntity $subject
     * @param CategoryEntity $result
     *
     * @return CategoryEntity
     */
    public function afterSave(
        CategoryEntity $subject,
        CategoryEntity $result
    ) {
        //a dummy instruction to avoid code-sniff warning '$subject isn't used'
        get_class($subject);

        $productIds = $result->getAffectedProductIds();

        if ($productIds) {
            $this->processor->reindexList($productIds);
        }

        return ($result);
    }

    /**
     * afterDelete
     *
     * After Product Category delete, mark the indexer as invalid.
     * @param CategoryEntity $subject
     * @param CategoryEntity $result
     *
     * @return CategoryEntity
     */
    public function afterDelete(
        CategoryEntity $subject,
        CategoryEntity $result
    ) {
        //a dummy instruction to avoid code-sniff warning '$subject isn't used'
        $subject->getCode();

        $this->processor->markIndexerAsInvalid();
        return ($result);
    }
}
