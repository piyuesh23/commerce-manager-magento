<?php

/**
 * Acquia/CommerceManager/Api/ClientTokenManagementInterface.php
 *
 * Braintree Frontend Client Token Management API Endpoint
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Api;

/**
 * ClientTokenManagementInterface
 *
 * Braintree Frontend Client Token Management API Endpoint
 */
interface ClientTokenManagementInterface
{
    /**
     * getClientToken
     *
     * Get a generic (non-customer specific) Braintree client token.
     *
     * @return string $token Client Token
     */
    public function getClientToken();

    /**
     * getCustomerClientToken
     *
     * Get a customer specific Braintree client token based on Cart ID.
     *
     * @param int $cartId The cart ID.
     *
     * @return string $token Client Token
     */
    public function getCustomerClientToken($cartId);
}
