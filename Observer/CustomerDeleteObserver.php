<?php

/**
 * Acquia/CommerceManager/Observer/CustomerDeleteObserver.php
 *
 * Acquia Commerce Connector Customer Delete Observer
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Backend\Customer\Interceptor as CustomerInterceptor;

class CustomerDeleteObserver extends ConnectorObserver implements ObserverInterface
{
    /**
     * Connector Customer Delete Endpoint
     * @const ENDPOINT_CUSTOMER_DELETE
     */
    const ENDPOINT_CUSTOMER_DELETE = 'ingest/customer/delete';

    /**
     * When deleting customer, delete also from the Drupal.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getObject();
        // Only for customer backend.
        // Customer backend model adds getStoreId().
        // @TODO (malachy): change the code to this:
        // if ($customer instanceof \Magento\Customer\Model\Backend\Customer) {
        // Because the Interceptor classes are generated; we should not use
        // Interceptor classes in code. The Interceptors extend the class we want
        if ($customer instanceof CustomerInterceptor) {
            $storeId = $customer->getStoreId();
            $this->logger->notice(
                sprintf(
                    'CustomerDeleteObserver: deleting Customer id %d and email %s.',
                    $customer->getId(),
                    $customer->getEmail()
                ),
                [ 'id' => $customer->getId(), 'store_id' => $storeId]
            );

            $edata = [
                'email' =>  $customer->getEmail(),
            ];
            $doReq = function ($client, $opt) use ($edata) {
                $opt['json'] = $edata;
                return $client->post(self::ENDPOINT_CUSTOMER_DELETE, $opt);
            };

            $this->tryRequest($doReq, 'CustomerDeleteObserver', $storeId);
        }
    }
}
