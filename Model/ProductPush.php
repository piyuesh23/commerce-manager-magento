<?php

/**
 * Acquia/CommerceManager/Model/ProductPush
 *
 * Acquia Commerce Manager - Process items in queue to push products in background.
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use Acquia\CommerceManager\Helper\Acm;
use Acquia\CommerceManager\Helper\ProductBatch as BatchHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Psr\Log\LoggerInterface;

class ProductPush
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var BatchHelper
     */
    private $batchHelper;

    /**
     * @var Acm
     */
    private $acmHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ProductPush constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param BatchHelper $batchHelper
     * @param Acm $acmHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
      ProductRepositoryInterface $productRepository,
      BatchHelper $batchHelper,
      Acm $acmHelper,
      LoggerInterface $logger
    ) {
        $this->productRepository = $productRepository;
        $this->batchHelper = $batchHelper;
        $this->acmHelper = $acmHelper;
        $this->logger = $logger;
    }

    /**
     * Push products in batch.
     *
     * @param string $batch
     */
    public function pushProductBatch($batch)
    {
        $productIds = json_decode($batch, TRUE);

        if (empty($productIds) || !is_array($productIds)) {
            $this->logger->error("ProductPush: Invalid data received in consumer", [
                'batch' => $batch,
            ]);

            return;
        }

        $productDataByStore = [];

        foreach ($productIds as $productId) {
            $product = $this->productRepository->getById($productId);

            $stores = $product->getStoreIds();

            foreach ($stores as $storeId) {
                if ($storeId == 0) {
                    continue;
                }

                $this->logger->notice(
                    sprintf('ProductPush: sending product for store %d.', $storeId),
                    [ 'sku' => $product->getSku(), 'id' => $product->getId() ]
                );
//NEEDS TRY CATCH
                $storeProduct = $this->productRepository->getById(
                    $product->getId(),
                    false,
                    $storeId
                );

                if ($storeProduct) {
                    $productDataByStore[$storeId][] = $this->acmHelper->getProductDataForAPI($storeProduct);
                }
            }
        }

        $this->batchHelper->pushMultipleProducts($productDataByStore);
    }
}
