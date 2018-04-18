<?php

/**
 * Acquia/CommerceManager/Model/CustomerAuthManagement.php
 *
 * Customer Authentication / Retrieval Management API Endpoint
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use Acquia\CommerceManager\Api\CustomerAuthManagementInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * CustomerAuthManagement
 *
 * Customer Authentication / Retrieval Management API Endpoint
 */
class CustomerAuthManagement implements CustomerAuthManagementInterface
{

    /**
     * Magento Account Manager Service
     * @var AccountManagementInterface $accountManagement
     */
    protected $accountManagement;

    /**
     * Magento Customer Entity Repository
     * @var CustomerRepositoryInterface $customerRepository
     */
    protected $customerRepository;

    /**
     * Magento Customer Entity Registry
     * @var CustomerRegistry $customerRegistry
     */
    protected $customerRegistry;

    /**
     * Magento Quote Entity Repository
     * @var CartRepositoryInterface $quoteRepository
     */
    protected $quoteRepository;

    /**
     * Magento Encryptor Service
     * @var Encryptor $encryptor
     */
    protected $encryptor;

    /**
     * Constructor
     *
     * @param AccountManagementInterface $accountManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerRegistry $customerRegistry
     * @param CartRepositoryInterface $quoteRepository
     * @param Encryptor $encryptor
     *
     */
    public function __construct(
        AccountManagementInterface $accountManagement,
        CustomerRepositoryInterface $customerRepository,
        CustomerRegistry $customerRegistry,
        CartRepositoryInterface $quoteRepository,
        Encryptor $encryptor
    ) {
        $this->accountManagement = $accountManagement;
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->quoteRepository = $quoteRepository;
        $this->encryptor = $encryptor;
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomerByLoginAndPassword($username, $password)
    {
        $account = $this->accountManagement->authenticate($username, $password);
        $cart_id = null;
        $cartExists = null;

        try {
            $cart = $this->quoteRepository->getForCustomer($account->getId());
            $cart_id = $cart->getId();
            $cartExists = true;
        } catch (NoSuchEntityException $e) {
            // Intentionally left blank.
            // Circumvents code-sniff 'empty catch is not allowed' warning
            $cartExists = false;
        }

        if(!$cartExists)
        {
            //create a new model anyway.
            //passing null for the cartID
            //tautology (this logic is deliberately contrived to placate the code sniffs)
            $cart_id = null;
        }
        return (new CustomerWithCartId($account, $cart_id));
    }

    /**
     * {@inheritDoc}
     */
    public function setCustomerPassword($customerId, $password)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            throw new InvalidEmailOrPasswordException(__('Invalid Customer ID.'));
        }

        $customerSecure =
            $this->customerRegistry->retrieveSecureData($customer->getId());

        $customerSecure->setRpToken(null);
        $customerSecure->setRpTokenCreatedAt(null);

        $customerSecure->setPasswordHash(
            $this->encryptor->getHash($password, true)
        );

        $this->customerRepository->save($customer);

        return ($customer);
    }
}
