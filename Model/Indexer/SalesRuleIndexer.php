<?php

/**
 * Acquia/CommerceManager/Model/Indexer/SalesRuleIndexer.php
 *
 * Acquia Commerce Manager Sales Rule / Product Abstract Indexer
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Indexer;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Indexer\CacheContext;
use Magento\Framework\Exception\LocalizedException;

/**
 * SalesRuleIndexer
 *
 * Acquia Commerce Manager Sales Rule / Product Abstract Indexer
 */
abstract class SalesRuleIndexer implements IndexerActionInterface, MviewActionInterface, IdentityInterface
{

    /**
     * Magento Application Cache Context
     * @var \Magento\Framework\Indexer\CacheContext
     */
    protected $cacheContext;

    /**
     * Magento Application Cache Instance
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cacheManager;

    /**
     * Magento EDA Event Dispatcher
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Sales Rule / Product Index Builder
     * @var SalesRuleBuilder $indexBuilder
     */
    protected $indexBuilder;

    /**
     * Constructor
     *
     * @param SalesRuleBuilder $indexBuilder
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        SalesRuleBuilder $indexBuilder,
        ManagerInterface $eventManager
    ) {
        $this->indexBuilder = $indexBuilder;
        $this->eventManager = $eventManager;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($ids)
    {
        $this->executeList($ids);
    }

    /**
     * {@inheritDoc}
     */
    public function executeFull()
    {
        $this->indexBuilder->reindexFull();
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this]);
        $this->getCacheManager()->clean($this->getIdentities());
    }

    /**
     * {@inheritDoc}
     */
    public function executeList(array $ids)
    {
        if (!$ids) {
            throw new LocalizedException(
                __('Could not rebuild index for empty entity array')
            );
        }

        $this->reindexList($ids);
    }

    /**
     * {@inheritDoc}
     */
    public function executeRow($id)
    {
        if (!$id) {
            throw new LocalizedException(
                __('Could not rebuild index for undefined entity.')
            );
        }

        $this->reindexRow($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentities()
    {
        return [
            \Magento\Catalog\Model\Category::CACHE_TAG,
            \Magento\Catalog\Model\Product::CACHE_TAG,
            \Magento\Framework\App\Cache\Type\Block::CACHE_TAG
        ];
    }

    /**
     * reindexList
     *
     * Reindex multiple entities due to a mass action update.
     *
     * @param int[] $ids Entity Ids
     *
     * @return void
     */
    abstract protected function reindexList(array $ids);

    /**
     * reindexRow
     *
     * Reindex a single entity due to normal / mview update.
     *
     * @param int $id Entity ID
     *
     * @return void
     */
    abstract protected function reindexRow($id);

    /**
     * Get cache instance
     *
     * @return \Magento\Framework\App\CacheInterface|mixed
     */
    private function getCacheManager()
    {
        if ($this->cacheManager === null) {
            $this->cacheManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\App\CacheInterface::class
            );
        }
        return $this->cacheManager;
    }

    /**
     * Get cache context
     *
     * @return \Magento\Framework\Indexer\CacheContext
     * @deprecated
     */
    protected function getCacheContext()
    {
        if (!($this->cacheContext instanceof CacheContext)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(CacheContext::class);
        } else {
            return $this->cacheContext;
        }
    }
}
