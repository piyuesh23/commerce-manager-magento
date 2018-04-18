<?php

/**
 * Acquia/CommerceManager/Api/Data/CustomerWithCartIdInterface.php
 *
 * Acquia Commerce Manager Customer With CardId API Data Object
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Api\Data;

/**
 * CustomerWithCartIdInterface
 *
 * Acquia Commerce Manager Customer With CartId API Data Object
 *
 * @api
 */
interface CustomerWithCartIdInterface
{
    /**
     * getCustomer
     *
     * Get the Customer.
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer();

    /**
     * getCartId
     *
     * Get the cart_id.
     *
     * @return int
     */
    public function getCartId();
}
