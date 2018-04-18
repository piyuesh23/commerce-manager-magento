<?php

/**
 * Acquia/CommerceManager/Model/Product/RelationInterface.php
 *
 * Acquia Commerce Product API Relationship Type Processor Interface
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * RelationInterface
 *
 * Acquia Commerce Product API Relationship Type Processor Interface
 */
interface RelationInterface
{
    /**
     * generateRelations
     *
     * Generate API relationship data for a product.
     *
     * @param ProductInterface $product Product to generate
     *
     * @return array $relation_data
     */
    public function generateRelations(ProductInterface $product);

    /**
     * handlesProduct
     *
     * Check if this type processor handles a certain product.
     *
     * @param ProductInterface $product
     *
     * @return bool $handles
     */
    public function handlesProduct(ProductInterface $product);
}
