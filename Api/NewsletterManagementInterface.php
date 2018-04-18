<?php

/**
 * Acquia/CommerceManager/Api/NewsletterManagementInterface.php
 *
 * Acquia Commerce Manager Newsletter Subscription Api Operations
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Api;

/**
 * NewsletterManagementInterface
 *
 * Acquia Commerce Manager Newsletter Subscription Api Operations
 */
interface NewsletterManagementInterface
{
    /**
     * getSubscriptionByEmail
     *
     * Get Customer Newsletter Subscription Status By Customer email.
     *
     * @param string $email Customer email
     *
     * @return \Acquia\CommerceManager\Api\Data\NewsletterSubscriptionInterface
     */
    public function getSubscriptionByEmail($email);

    /**
     * getSubscriptionById
     *
     * Get Customer Newsletter Subscription Status By Customer ID.
     *
     * @param int $customerId Customer ID
     *
     * @return \Acquia\CommerceManager\Api\Data\NewsletterSubscriptionInterface
     */
    public function getSubscriptionById($customerId);

    /**
     * setSubscriptionByEmail
     *
     * @param string $email Customer email (plaintext)
     *
     * @return \Acquia\CommerceManager\Api\Data\StatusInterface
     */
    public function setSubscriptionByEmail($email);
}
