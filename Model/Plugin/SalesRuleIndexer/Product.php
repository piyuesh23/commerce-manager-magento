<?php

/**
 * Acquia/CommerceManager/Model/Plugin/SalesRuleIndexer/Product.php
 *
 * Acquia Commerce Manager Sales Rule Indexer Product Save Plugin
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Plugin\SalesRuleIndexer;

use Acquia\CommerceManager\Model\Indexer\ProductSalesRuleProcessor;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Product
 *
 * Acquia Commerce Manager Sales Rule Indexer Product Save Plugin
 */
class Product
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
     * @return void
     */
    public function __construct(ProductSalesRuleProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * aroundSave
     *
     * On Product resource model save, rebuild the sales rule
     * indexes for that product.
     *
     * @param ProductResource $subject
     * @param callable $proceed
     * @param AbstractModel $product
     *
     * @return ProductResource
     */
    public function aroundSave(
        ProductResource $subject,
        callable $proceed,
        AbstractModel $product
    ) {
        //a dummy instruction to avoid code-sniff warning '$subject isn't used'
        get_class($subject);

        $saved = $proceed($product);

        if (!$product->getIsMassupdate()) {
            $this->processor->reindexRow($product->getId());
        }

        return ($saved);
    }
}
