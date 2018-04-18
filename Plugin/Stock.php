<?php

/**
 * Acquia/CommerceManager/Plugin/Stock.php
 *
 * Acquia Connector Plugin to override StockUpdateIdx.
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Acquia\CommerceManager\Helper\ProductBatch as BatchHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\InventoryMessageBus\Model\ResourceModel\StockUpdateIdx;
use Psr\Log\LoggerInterface;

class Stock
{

    /**
     * Product Batch Helper
     *
     * @var BatchHelper
     */
    private $batchHelper;

    /**
     * Magento Product Repository
     *
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Magento Database Resource
     *
     * @var ResourceConnection $resource
     */
    protected $resource;

    /**
     * Magento Database Connection
     *
     * @var AdapterInterface $connection
     */
    protected $connection;

    /**
     * Logger service.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Product IDs array to be updated.
     *
     * @var array
     */
    private $product_ids = [];

    /**
     * Stock constructor.
     *
     * @param BatchHelper                $batchHelper
     * @param ProductRepositoryInterface $productRepository
     * @param ResourceConnection         $resource
     * @param LoggerInterface            $logger
     */
    public function __construct(BatchHelper $batchHelper,
        ProductRepositoryInterface $productRepository,
        ResourceConnection $resource,
        LoggerInterface $logger
    ) {
        $this->batchHelper = $batchHelper;
        $this->productRepository = $productRepository;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->logger = $logger;
    }

    /**
     * beforeUpdateStockTable
     *
     * @param StockUpdateIdx $idx
     * @param array          $batch
     * @param array          $websiteIds
     *
     * @return array
     */
    public function beforeUpdateStockTable(StockUpdateIdx $idx, $batch,
        $websiteIds
    ) {
        // Don't do anything if we are not pushing stock changes.
        if ($this->batchHelper->getStockMode() !== 'push') {
            return [$batch, $websiteIds];
        }

        $batch = $this->assignProductId($batch);

        foreach ($batch as $row) {
            if (!empty($row['product_id'])) {
                $row['website_ids'] = $websiteIds;
                $this->product_ids[$row['product_id']] = $row;
            }
        }

        $this->filterProductsWithStockUpdate($websiteIds);

        return [$batch, $websiteIds];
    }

    /**
     * Update product after stock table update.
     *
     * @param StockUpdateIdx $idx
     * @param mix            $result
     *
     * @return mixed
     */
    public function afterUpdateStockTable(StockUpdateIdx $idx, $result)
    {
        if (!empty($this->product_ids)) {
            foreach ($this->product_ids as $product_id => $data) {
                $this->logger->info('afterUpdateStockTable: Adding product to queue.', [
                    'product_id' => $product_id,
                ]);

                $data = [
                    'id' => $product_id,
                    'sku' => $data['sku'],
                    'website_ids' => $data['website_ids'],
                    'qty' => $data['qty'],
                ];

                $message = json_encode($data);

                $this->batchHelper->addStockMessageToQueue($message);

                unset($this->product_ids[$product_id]);
            }
        }

        return $result;
    }

    /**
     * @param array $batch
     *
     * @return array
     */
    private function getUniqueSkus($batch)
    {
        $skus = array_unique(
            array_map(
                function ($item) {
                    return $item['sku'];
                }, $batch
            )
        );

        return $skus;
    }

    /**
     * assignProductId.
     *
     * Assign product ids to each row in batch. Function copied from
     * Magento\InventoryMessageBus\Model\ResourceModel\StockUpdateIdx.
     *
     * @param array $batch
     *
     * @return array
     */
    private function assignProductId($batch)
    {
        $skus = $this->getUniqueSkus($batch);

        $select = $this->connection->select()->from(
            $this->resource->getTableName('catalog_product_entity'),
            ['sku', 'entity_id']
        );

        $select->where('sku IN (?)', $skus);

        $productIds = $this->connection->fetchPairs($select);

        foreach ($batch as $key => $row) {
            if (isset($productIds[$row['sku']])) {
                $batch[$key]['product_id'] = $productIds[$row['sku']];
            } else {
                unset($batch[$key]);
            }
        }

        return $batch;
    }

    /**
     * filterProductsWithStockUpdate.
     *
     * Keep only those product ids in $this->product_ids for which stock is
     * updated.
     *
     * @param array $websiteIds
     */
    protected function filterProductsWithStockUpdate($websiteIds)
    {
        if (empty($this->product_ids)) {
            return;
        }

        // Tables like cataloginventory_stock_status_idx / cataloginventory_stock_status
        // are not updated in real-time. To ensure we check if stock is changed
        // against latest information we use cataloginventory_stock_item
        // which is updated real-time.
        $select = $this->connection->select()->from(
            $this->resource->getTableName('cataloginventory_stock_item'),
            ['product_id', 'qty']
        );

        $select->where('product_id IN (?)', array_keys($this->product_ids));
        $select->where('website_id IN (?)', $websiteIds);

        $productQuantities = $this->connection->fetchPairs($select);

        foreach ($this->product_ids as $product_id => $data) {
            $new_quantity = $data['qty'];

            if (isset($productQuantities[$product_id]) && $productQuantities[$product_id] == $new_quantity) {
                // Stock not changed for this product, we won't trigger stock
                // push for this one.
                $this->logger->debug('beforeUpdateStockTable: Stock not changed.', [
                        'product_id' => $product_id,
                        'old_stock' => $productQuantities[$product_id],
                        'new_stock' => $new_quantity,
                        'website_ids' => json_encode($websiteIds),
                    ]
                );

                // Stock not updated or is the same for this product.
                unset($this->product_ids[$product_id]);
            }
            else {
                // Stock changed for this product, we will trigger stock push
                // for this one.
                $this->logger->debug('beforeUpdateStockTable: Stock changed.', [
                    'product_id' => $product_id,
                    'old_stock' => $productQuantities[$product_id],
                    'new_stock' => $new_quantity,
                    'website_ids' => json_encode($websiteIds),
                ]);
            }
        }
    }

}
