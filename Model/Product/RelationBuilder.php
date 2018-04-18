<?php

/**
 * Acquia/CommerceManager/Model/Product/RelationBuilder.php
 *
 * Acquia Commerce Product API Relationship Builder
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * RelationBuilder
 *
 * Acquia Commerce Product API Relationship Builder
 */
class RelationBuilder implements RelationBuilderInterface
{
    /**
     * Registered Product Relation Processors
     * @var RelationInterface[] $relations
     */
    protected $relations;

    /**
     * Constructor
     *
     * @param RelationInterface[] $relations = []
     */
    public function __construct(array $relations = [])
    {
        foreach ($relations as $relation) {
            if (!($relation instanceof RelationInterface)) {
                throw new \InvalidArgumentException(
                    'Invalid Relation argument.'
                );
            }
        }

        $this->relations = $relations;
    }

    /**
     * {@inheritDoc}
     */
    public function addRelationType(RelationInterface $relation)
    {
        $this->relations[] = $relation;

        return ($this);
    }

    /**
     * {@inheritDoc}
     */
    public function generateRelations(ProductInterface $product)
    {
        $data = [];

        foreach ($this->relations as $relation) {
            if ($relation->handlesProduct($product)) {
                $data = array_merge(
                    $data,
                    $relation->generateRelations($product)
                );
            }
        }

        return ($data);
    }
}
