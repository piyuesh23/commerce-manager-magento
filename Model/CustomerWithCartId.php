<?php

/**
 * Acquia/CommerceManager/Model/CustomerWithCartId.php
 *
 * Acquia Commerce Manager Combined Cart Totals Entity
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use Acquia\CommerceManager\Api\Data\CustomerWithCartIdInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * CustomerWithCartId
 *
 * Acquia Commerce Manager Combined Customer Entity and Cart ID.
 */
class CustomerWithCartId implements CustomerWithCartIdInterface
{
    /**
     * Customer Entity
     * @var CustomerInterface $customer
     */
    protected $customer;

    /**
     * Cart ID
     * @var int $cartId
     */
    protected $cartId;

    /**
     * Constructor
     *
     * @param CustomerInterface $customer
     * @param $cart_id
     */
    public function __construct(
        CustomerInterface $customer,
        $cart_id
    ) {
        $this->customer = $customer;
        $this->cartId = $cart_id;
    }

    /**
     * getCustomer
     *
     * Get the Customer loaded.
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer()
    {
        return ($this->customer);
    }

    /**
     * getCartId
     *
     * Get the Customer's cart ID.
     *
     * @return int
     */
    public function getCartId()
    {
        return ($this->cartId);
    }
}
