<?php

/**
 * Acquia/CommerceManager/NewsletterManagement.php
 *
 * Acquia Commerce Manager Newsletter Subscription Api Operations
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use Acquia\CommerceManager\Api\NewsletterManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * NewsletterManagementInterface
 *
 * Acquia Commerce Manager Newsletter Subscription Api Operations
 */
class NewsletterManagement implements NewsletterManagementInterface
{
    const ALREADY_SUBSCRIBER = 0;
    const NEW_SUBSCRIBER = 1;

    /**
     * Factory used for manipulating newsletter subscriptions
     *
     * @var SubscriberFactory $subscriberFactory
     */
    private $subscriberFactory;

    /**
     * Factory for API result models
     * @var NewsletterSubscriptionFactory $subscriptionResultFactory
     */
    private $subscriptionResultFactory;

    /**
     * Factory for API result models
     * @var StatusFactory $statusResultFactory
     */
    private $statusResultFactory;

    /**
     * @var Subscriber $subscriber
     */
    private $subscriber;

    /**
     * @var CustomerRepositoryInterface $customerRepository
     */
    private $customerRepository;

    /**
     * @var StoreManagerInterface $storeManager
     */
    private $storeManager;

    /**
     * Constructor
     *
     * @param SubscriberFactory $subscriberFactory
     * @param \Acquia\CommerceManager\Model\NewsletterSubscriptionFactory $subscriptionResultFactory
     * @param \Acquia\CommerceManager\Model\StatusFactory $statusResultFactory
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     *
     * @internal param \Acquia\CommerceManager\Model\NewsletterSubscriptionFactory $resultFactory
     *
     */
    public function __construct(
        SubscriberFactory $subscriberFactory,
        NewsletterSubscriptionFactory $subscriptionResultFactory,
        StatusFactory $statusResultFactory,
        Subscriber $subscriber,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->subscriberFactory = $subscriberFactory;
        $this->subscriptionResultFactory = $subscriptionResultFactory;

        $this->statusResultFactory = $statusResultFactory;
        $this->subscriber = $subscriber;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscriptionByEmail($email)
    {
        $subscription = $this->subscriberFactory->create()
            ->loadByEmail($email);

        $result = $this->subscriptionResultFactory->create()
            ->setCustomerId($subscription->getCustomerId())
            ->setCustomerEmail($subscription->getSubscriberEmail())
            ->setIsSubscribed($subscription->isSubscribed());

        return ($result);
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscriptionById($customerId)
    {
        $subscription = $this->subscriberFactory->create()
            ->loadByCustomerId($customerId);

        $result = $this->subscriptionResultFactory->create()
            ->setCustomerId($subscription->getCustomerId())
            ->setCustomerEmail($subscription->getSubscriberEmail())
            ->setIsSubscribed($subscription->isSubscribed());

        return ($result);
    }

    /**
     * {@inheritDoc}
     */
    public function setSubscriptionByEmail($email)
    {
        try {
            $subscriber = $this->subscriber->loadByEmail($email);

            if (!$subscriber->isSubscribed()) {
                $customer = $this->customerRepository->get($email);

                $subscriber->setStatus(Subscriber::STATUS_SUBSCRIBED);
                $subscriber->setSubscriberEmail($email);
                $subscriber->setStoreId($customer->getStoreId());
                $subscriber->setCustomerId($customer->getId());
                $subscriber->setStatusChanged(true);
                $subscriber->save();
            }
        } catch (NoSuchEntityException $e) {
            $subscriber = $this->subscriberFactory->create();
            $subscriber->setStatus(Subscriber::STATUS_SUBSCRIBED);
            $subscriber->setSubscriberEmail($email);
            $subscriber->setStoreId($this->storeManager->getStore()->getId());
            $subscriber->setCustomerId(0);
            $subscriber->setStatusChanged(true);
            $subscriber->save();
        }

        // Return the operation result.
        $result = $this->statusResultFactory->create()
            ->setStatus($subscriber->isStatusChanged() ? self::NEW_SUBSCRIBER : self::ALREADY_SUBSCRIBER);

        return ($result);
    }
}
