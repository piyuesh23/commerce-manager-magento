<?php

/**
 * Acquia/CommerceManager/Model/Product/RelationBuilderInterface.php
 *
 * Acquia Commerce Product API Relationship Builder Interface
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * RelationBuilderInterface
 *
 * Acquia Commerce Product API Relationship Builder Interface
 */
interface RelationBuilderInterface
{
    /**
     * addRelationType
     *
     * Add a relation type processor to the available processors pool.
     *
     * @param RelationInterface $relation Relation type processor
     *
     * @return self $this
     */
    public function addRelationType(RelationInterface $relation);

    /**
     * generateRelations
     *
     * Use registered relation type processor services to generate
     * relations API data for a product.
     *
     * @param ProductInterface $product Product to generate
     *
     * @return array $relation_data
     */
    public function generateRelations(ProductInterface $product);
}
