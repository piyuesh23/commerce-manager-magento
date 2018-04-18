<?php

/**
 * Acquia/CommerceManager/Api/CustomerAuthManagementInterface.php
 *
 * Customer Authentication / Retrieval Management API Endpoint
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Api;

/**
 * CustomerAuthManagementInterface
 *
 * Customer Authentication / Retrieval Management API Endpoint
 */
interface CustomerAuthManagementInterface
{
    /**
     * getCustomerByLoginAndPassword
     *
     * Get a customer entity record by customer authentication information.
     *
     * @param string $username Magento Username
     * @param string $password Magento Password (plain text)
     *
     * @return \Acquia\CommerceManager\Api\Data\CustomerWithCartIdInterface
     * @throws \Magento\Framework\Exception\InvalidEmailOrPasswordException
     * @throws \Magento\Framework\Exception\EmailNotConfirmedException
     * @throws \Magento\Framework\Exception\State\UserLockedException;
     */
    public function getCustomerByLoginAndPassword($username, $password);

    /**
     * setCustomerPassword
     *
     * @param int $customerId Customer ID
     * @param string $password New Password (plaintext)
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\InvalidEmailOrPasswordException
     */
    public function setCustomerPassword($customerId, $password);
}
