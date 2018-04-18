<?php

/**
 * Acquia/CommerceManager/Model/TargetRuleProducts.php
 *
 * Acquia Commerce Manager Target Rule / Relation Products API Data Object
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use Acquia\CommerceManager\Api\Data\TargetRuleProductsInterface;

/**
 * TargetRuleProducts
 *
 * Acquia Commerce Manager Target Rule / Relation Products API Data Object
 */
class TargetRuleProducts implements TargetRuleProductsInterface
{
    /**
     * Data storage
     * @var array $typeData = []
     */
    private $typeData = [];

    /**
     * Constructor
     *
     * @param string[] $crosssell
     * @param string[] $related
     * @param string[] $upsell
     */
    public function __construct(
        array $crosssell = [],
        array $related = [],
        array $upsell = []
    ) {
        $this->typeData['crosssell'] = $crosssell;
        $this->typeData['related'] = $related;
        $this->typeData['upsell'] = $upsell;
    }

    /**
     * getCrosssell
     *
     * Product by target rule type 'crosssell'
     *
     * @return string[] $crosssell
     */
    public function getCrosssell()
    {
        return ($this->typeData['crosssell']);
    }

    /**
     * getRelated
     *
     * Product by target rule type 'related'
     *
     * @return string[] $related
     */
    public function getRelated()
    {
        return ($this->typeData['related']);
    }

    /**
     * getUpsell
     *
     * Product by target rule type 'upsell'
     *
     * @return string[] $upsell
     */
    public function getUpsell()
    {
        return ($this->typeData['upsell']);
    }
}
