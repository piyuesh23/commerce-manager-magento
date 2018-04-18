<?php

/**
 * Acquia/CommerceManager/Observer/CategoryMoveObserver.php
 *
 * Acquia Commerce Connector Category Move Observer
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * CategoryMoveObserver
 *
 * Acquia Commerce Connector Category Move Observer
 */
class CategoryMoveObserver extends CategoryObserver implements ObserverInterface
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
        $parent = $observer->getEvent()->getParent();
        $cid = $observer->getEvent()->getCategoryId();

        $this->logger->info(sprintf(
            '%s: move category %d to parent %s (%d).',
            $this->getLogName(),
            $cid,
            $parent->getName(),
            $parent->getId()
        ));

        $this->sendStoreCategories($parent);
    }

    /**
     * {@inheritDoc}
     */
    protected function getLogName()
    {
        return ('CategoryMoveObserver');
    }
}
