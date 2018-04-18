<?php

/**
 * Acquia/CommerceManager/Model/StockPush
 *
 * Acquia Connector - Process items in queue to push stock in background.
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use Acquia\CommerceManager\Helper\ProductBatch;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class StockPush
{
    /**
     * Product Helper object.
     *
     * @var ProductBatch
     */
    private $batchHelper;

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
     * @param ProductBatch         $batchHelper
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface       $logger
     */
    public function __construct(
        ProductBatch $batchHelper,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->batchHelper = $batchHelper;
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
        $data = json_decode($message, true);

        // Sanity check.
        if (empty($data['id']) || empty($data['sku']) || empty($data['qty'])) {
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

            $this->batchHelper->pushStock($stock);
        }
    }

}
