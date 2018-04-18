<?php

/**
 * Acquia/CommerceManager/Model/Data/ExtendedCatalogRule.php
 *
 * Acquia Commerce Manager Extended Catalog Rule Data Model
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Data;

use Magento\CatalogRule\Model\Rule;

/**
 * ExtendedCatalogRule
 *
 * Acquia Commerce Manager Extended Catalog Rule Data Model
 */
class ExtendedCatalogRule extends Rule implements \Acquia\CommerceManager\Api\Data\ExtendedCatalogRuleInterface
{
    const KEY_DISCOUNT_DATA = 'product_discounts';

    /**
     * {@inheritDoc}
     */
    public function getProductDiscounts()
    {
        return ($this->_get(self::KEY_DISCOUNT_DATA));
    }

    /**
     * {@inheritDoc}
     */
    public function setProductDiscounts(array $discounts)
    {
        return ($this->setData(self::KEY_DISCOUNT_DATA, $discounts));
    }
}
