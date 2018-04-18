<?php

/**
 * Acquia/CommerceManager/Api/Data/TargetRuleProductsInterface.php
 *
 * Acquia Commerce Manager Target Rule / Relation Products API Data Object
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Api\Data;

/**
 * TargetRuleProductsInterface
 *
 * Acquia Commerce Manager Target Rule / Relation Products API Data Object
 */
interface TargetRuleProductsInterface
{
    /**
     * getCrosssell
     *
     * Product by target rule type 'crosssell'
     *
     * @return string[] $crosssell
     */
    public function getCrosssell();

    /**
     * getRelated
     *
     * Product by target rule type 'related'
     *
     * @return string[] $related
     */
    public function getRelated();

    /**
     * getUpsell
     *
     * Product by target rule type 'upsell'
     *
     * @return string[] $upsell
     */
    public function getUpsell();
}
