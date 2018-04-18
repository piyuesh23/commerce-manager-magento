<?php

/**
 * Acquia/CommerceManager/Observer/CategorySaveObserver.php
 *
 * Acquia Commerce Connector Category Save Observer
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * CategorySaveObserver
 *
 * Acquia Commerce Connector Category Save Observer
 */
class CategorySaveObserver extends CategoryObserver implements ObserverInterface
{
    /**
     * execute
     *
     * Send updated category data to Acquia Commerce Manager.
     *
     * @param Observer $observer Incoming Observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();

        $this->logger->info(sprintf(
            '%s: save category %s (%d)',
            $this->getLogName(),
            $category->getName(),
            $category->getId()
        ));

        $this->sendStoreCategories($category);
    }

    /**
     * {@inheritDoc}
     */
    protected function getLogName()
    {
        return ('CategorySaveObserver');
    }
}
