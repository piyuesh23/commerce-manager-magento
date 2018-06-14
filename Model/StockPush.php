<?php

/**
 * Acquia/CommerceManager/Model/StockPush
 *
 * Acquia Connector - Process items in queue to push stock in background.
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use Acquia\CommerceManager\Helper\Stock as StockHelper;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class StockPush
{
    /**
     * Acquia Connector Stock Helper.
     *
     * @var StockHelper
     */
    private $stockHelper;

    /**
     * Store Manager object.
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Logger object.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Cache website <-> store mappings.
     *
     * @var array
     */
    protected $websitesToStoreIds;

    /**
     * StockPush constructor.
     *
     * @param StockHelper $stockHelper
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        StockHelper $stockHelper,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->stockHelper = $stockHelper;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Push stock for particular product.
     *
     * @param mixed $message
     */
    public function pushStock($message)
    {
        $data = json_decode($message, TRUE);

        $data['qty'] = isset($data['qty']) ? $data['qty'] : 0;

        // Sanity check.
        if (empty($data['id']) || empty($data['sku'])) {
            $this->logger->warning('Invalid message for push stock queue.', [
                'message' => $message,
            ]);

            return;
        }

        if (!isset($data['website_ids']) || !is_array($data['website_ids'])) {
            // We will use default scope, for which we use NULL here.
            // So it goes inside the loop once.
            $data['website_ids'] = [null];
        }

        foreach ($data['website_ids'] as $websiteId) {
            // Prepare stock data to be pushed.
            $stock = [
                'qty' => $data['qty'],
                'is_in_stock' => (bool) $data['qty'],
                'sku' => $data['sku'],
                'product_id' => $data['id'],
                'website_id' => $websiteId,
            ];

            // Static cache for website <-> store mapping.
            if (!isset($this->websitesToStoreIds[$websiteId])) {
                $this->websitesToStoreIds[$websiteId] = $this->storeManager->getWebsite($websiteId)->getStoreIds();
            }

            // We push only for the first store in website, it is common for all stores.
            $stock['store_id'] = reset($this->websitesToStoreIds[$websiteId]);

            $this->logger->debug('Pushing stock for product.', $stock);

            $this->stockHelper->pushStock($stock, $stock['store_id']);
        }
    }

}
