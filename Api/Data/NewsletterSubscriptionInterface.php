<?php

/**
 * Acquia/CommerceManager/Api/Data/NewsletterSubscriptionInterface.php
 *
 * Acquia Commerce Manager Customer Newsletter Subscription Status API Data Object
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Api\Data;

/**
 * NewsletterSubscriptionInterface
 *
 * Acquia Commerce Manager Customer Newsletter Subscription Status API Data Object
 */
interface NewsletterSubscriptionInterface
{
    /**
     * getCustomerId
     *
     * Get the customer ID for this subscription status.
     *
     * @return int $customerId
     */
    public function getCustomerId();

    /**
     * getCustomerEmail
     *
     * Get the customer email for this subscription status.
     *
     * @return string $email
     */
    public function getCustomerEmail();

    /**
     * getIsSubscribed
     *
     * Is this customer subscribed.
     *
     * @return bool $subscribed
     */
    public function getIsSubscribed();
}
