<?php

/**
 * Acquia/CommerceManager/Model/ClientTokenManagement.php
 *
 * Braintree Client (Hosted Fields) Token Management API
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use Acquia\CommerceManager\Api\ClientTokenManagementInterface;
use Magento\Braintree\Model\Adapter\BraintreeAdapter;
use Magento\Braintree\Model\Ui\ConfigProvider;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * ClientTokenManagement
 *
 * Braintree Client (Hosted Fields) Token Management API
 */
class ClientTokenManagement implements ClientTokenManagementInterface
{

    /**
     * Braintree API Adapter
     * @var BraintreeAdapter $adapter
     */
    protected $adapter;

    /**
     * Braintree Config Model
     * @var ConfigProvider $config
     */
    protected $config;

    /**
     * User Cart / Quote Repository
     * @var CartRepositoryInterface $quoteRepository
     */
    protected $quoteRepository;

    /**
     * Constructor
     *
     * @param Config $config
     * @param Data $btHelper
     */
    public function __construct(
        ConfigProvider $config,
        BraintreeAdapter $adapter,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->config = $config;
        $this->adapter = $adapter;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * getClientToken
     *
     * Get a generic Braintree client token for the hosted fields frontend.
     *
     * @return string $token
     */
    public function getClientToken()
    {
        return ($this->config->getClientToken());
    }

    /**
     * getCustomerClientToken
     *
     * @param int $cartId Cart ID
     *
     * @return string $token
     */
    public function getCustomerClientToken($cartId)
    {
        if (!$cartId) {
            return ($this->getClientToken());
        }

        $quote = $this->quoteRepository->get($cartId);
        $customer = $quote->getCustomer();

        if (!$customer || !$customer->getId()) {
            return ($this->getClientToken());
        }

        $token = $this->adapter->generate([
            'customerId' => $customer->getId()
        ]);

        if ($token != "") {
            return ($token);
        } else {
            return ($this->getClientToken());
        }
    }
}
