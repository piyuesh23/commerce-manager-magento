<?php

/**
 * Acquia/CommerceManager/Model/NewsletterSubscription
 *
 * Acquia Commerce Manager Customer Newsletter Subscription Status API Data Object
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use Acquia\CommerceManager\Api\Data\NewsletterSubscriptionInterface;

/**
 * NewsletterSubscription
 *
 * Acquia Commerce Manager Customer Newsletter Subscription Status API Data Object
 */
class NewsletterSubscription implements NewsletterSubscriptionInterface
{
    /**
     * Customer ID
     * @var int $customerId
     */
    protected $customerId;

    /**
     * Customer Subscribed Email
     * @var string $customerEmail
     */
    protected $customerEmail;

    /**
     * Subscribed Status
     * @var bool $isSubscribed
     */
    protected $isSubscribed;

    /**
     * {@inheritDoc}
     */
    public function getCustomerId()
    {
        return ($this->customerId);
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomerEmail()
    {
        return ($this->customerEmail);
    }

    /**
     * {@inheritDoc}
     */
    public function getIsSubscribed()
    {
        return ($this->isSubscribed);
    }

    /**
     * setCustomerId
     *
     * Set the customer ID for this subscription status.
     *
     * @param int $customerId Customer ID
     *
     * @return self
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = (int)$customerId;
        return ($this);
    }

    /**
     * setCustomerEmail
     *
     * Set the customer email for this subscription status.
     *
     * @param string $email Customer Email
     *
     * @return self
     */
    public function setCustomerEmail($email)
    {
        $this->customerEmail = (string)$email;
        return ($this);
    }

    /**
     * setIsSubscribed
     *
     * Set the customer subscribed status.
     *
     * @param bool $subscribed
     *
     * @return self
     */
    public function setIsSubscribed($subscribed)
    {
        $this->isSubscribed = (bool)$subscribed;
        return ($this);
    }
}
