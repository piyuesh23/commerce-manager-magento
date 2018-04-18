<?php

/**
 * Acquia/CommerceManager/Helper/ProductBatch.php
 *
 * Acquia Commerce Product Batch Helper
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Helper;

use Acquia\CommerceManager\Helper\Data as ClientHelper;
use Acquia\CommerceManager\Helper\Acm as AcmHelper;
use Acquia\CommerceManager\Model\Product\RelationBuilderInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory;
use Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Gallery;
use Magento\Framework\Webapi\ServiceOutputProcessor;
use Magento\CatalogRule\Model\Rule;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Module\ModuleListInterface;
/**
 * ProductBatch
 *
 * Acquia Commerce Product Helper
 */
class ProductBatch extends AbstractHelper
{
    /**
     * Connector Product Update Endpoint
     *
     * @const ENDPOINT_PRODUCT_UPDATE
     */
    const ENDPOINT_PRODUCT_UPDATE = 'ingest/product';

    /**
     * Connector Stock Update Endpoint
     *
     * @const ENDPOINT_PRODUCT_UPDATE
     */
    const ENDPOINT_STOCK_UPDATE = 'ingest/product-stock';

    /**
     * Consumer name (see etc/communications.xml).
     */
    const PRODUCT_PUSH_CONSUMER = 'connector.product.push';

    /**
     * Consumer name for stock.
     */
    const STOCK_PUSH_CONSUMER = 'connector.stock.push';

    /**
     * Magento Message Queue Module Name
     * @const MESSAGEQUEUE_MODULE
     */
    const MESSAGEQUEUE_MODULE = 'Magento_MessageQueue';

    /**
     * Metadata service.
     *
     * @var ProductAttributeRepositoryInterface $metadataService
     */
    protected $metadataService;

    /**
     * Media Gallery Resource Model
     *
     * @var Gallery $galleryResource
     */
    protected $galleryResource;

    /**
     * Magento WebAPI Output Processor
     *
     * @var ServiceOutputProcessor $serviceOutputProcessor
     */
    protected $serviceOutputProcessor;

    /**
     * Catalog rule model.
     *
     * @var \Magento\CatalogRule\Model\Rule $catalogRule
     */
    protected $catalogRule;

    /**
     * Product API Relation Data Builder
     *
     * @var RelationBuilderInterface $relationBuilder
     */
    protected $relationBuilder;

    /**
     * AttributeSetRepositoryInterface object
     *
     * @var AttributeSetRepositoryInterface $attributeSet
     */
    protected $attributeSet;

    /**
     * Stock Item factory object.
     *
     * @var \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory
     */
    protected $stockItemFactory;

    /**
     * Stock Item Repository object.
     *
     * @var \Magento\CatalogInventory\Api\StockItemRepositoryInterface
     */
    protected $stockItemRepository;

    /**
     * Stock Item Criteria builder object.
     *
     * @var \Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory
     */
    protected $stockItemCriteriaFactory;

    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Acquia Commerce Manager Client Helper
     *
     * @var ClientHelper $clientHelper
     */
    private $clientHelper;

    /**
     * Acquia Commerce Manager ACM Helper
     *
     * @var AcmHelper $clientHelper
     */
    private $acmHelper;

    /**
     * System Logger
     *
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * Magento WebAPI Service Class Name (for output formatting)
     *
     * @var string $serviceClassName
     */
    protected $serviceClassName = 'Magento\Catalog\Api\ProductRepositoryInterface';

    /**
     * Magento WebAPI Service Class Name (for output formatting of stock)
     *
     * @var string $stockServiceClassName
     */
    protected $stockServiceClassName = 'Magento\CatalogInventory\Api\StockItemRepositoryInterface';

    /**
     * Magento WebAPI Service Method Name (for output formatting)
     *
     * @var string $serviceMethodName
     */
    protected $serviceMethodName = 'get';

    /**
     * \Magento\Framework\MessageQueue\PublisherInterface
     * EE only
     */
    private $publisher;

    /**
     * @var ProductRepositoryInterface $productRepository
     */
    private $productRepository;

    /**
     * Magento Module List Service
     * @var ModuleListInterface $moduleList
     */
    private $moduleList;

    /**
     * ProductBatch constructor.
     *
     * @param Context                             $context
     * @param ProductAttributeRepositoryInterface $metadataServiceInterface
     * @param Gallery                             $galleryResource
     * @param ServiceOutputProcessor              $outputProc
     * @param Rule                                $catalog_rule
     * @param ProductRepositoryInterface          $productRepository
     * @param RelationBuilderInterface            $relationBuilder
     * @param AttributeSetRepositoryInterface     $attributeSet
     * @param StockItemInterfaceFactory           $stockItemFactory
     * @param StockItemCriteriaInterfaceFactory   $stockItemCriteriaFactory
     * @param StockItemRepositoryInterface        $stockItemRepository
     * @param StoreManagerInterface               $store_manager
     * @param Data                                $clientHelper
     */
    public function __construct(
        Context $context,
        ProductAttributeRepositoryInterface $metadataServiceInterface,
        Gallery $galleryResource,
        ServiceOutputProcessor $outputProc,
        Rule $catalog_rule,
        ProductRepositoryInterface $productRepository,
        RelationBuilderInterface $relationBuilder,
        AttributeSetRepositoryInterface $attributeSet,
        StockItemInterfaceFactory $stockItemFactory,
        StockItemCriteriaInterfaceFactory $stockItemCriteriaFactory,
        StockItemRepositoryInterface $stockItemRepository,
        StoreManagerInterface $store_manager,
        ClientHelper $clientHelper,
        AcmHelper $acmHelper,
        ModuleListInterface $moduleList
    ) {
        $this->metadataService = $metadataServiceInterface;
        $this->galleryResource = $galleryResource;
        $this->serviceOutputProcessor = $outputProc;
        $this->catalogRule = $catalog_rule;
        $this->productRepository = $productRepository;
        $this->relationBuilder = $relationBuilder;
        $this->attributeSet = $attributeSet;
        $this->stockItemFactory = $stockItemFactory;
        $this->stockItemCriteriaFactory = $stockItemCriteriaFactory;
        $this->stockItemRepository = $stockItemRepository;
        $this->storeManager = $store_manager;
        $this->clientHelper = $clientHelper;
        $this->acmHelper = $acmHelper;
        $this->moduleList = $moduleList;
        $this->logger = $context->getLogger();
        parent::__construct($context);
    }


    /**
     * {@inheritDoc}
     *
     * @return bool $enabled
     */
    public function getMessageQueueEnabled()
    {
        return ($this->_getMessageQueueEnabled());
    }

    /**
     * targetModuleEnabled
     *
     * Check if the Magento EE Magento\Framework\MessageQueue\PublisherInterface class is installed / enabled.
     *
     * @return bool $enabled
     */
    private function _getMessageQueueEnabled()
    {
        return ($this->moduleList->has(self::MESSAGEQUEUE_MODULE));
    }

    /**
     * getMessageQueue
     *
     * Build a MessageQueue\Publisher model from the ObjectManager to prevent
     * coupling to EE.
     *
     * @return null|\Magento\Framework\MessageQueue\Publisher
     */
    private function getMessageQueue()
    {
        if(!$this->publisher) {
            if ($this->getMessageQueueEnabled()) {
                // Object Manager's get() is type-preference aware,
                // so we can request a class using its interface
                $this->publisher = \Magento\Framework\App\ObjectManager::getInstance()->get(
                    \Magento\Framework\MessageQueue\PublisherInterface::class
                )->create();
            } else {
                $this->publisher = null;
                // Or perhaps use a class that mimics the queue using Magento CRON
            }
        }

        return ($this->publisher);
    }


    /**
     * pushProduct.
     *
     * Helper function to push product to front-end.
     *
     * @param ProductInterface $product
     * @param string           $action
     */
    public function pushProduct(ProductInterface $product, $action = 'productSave')
    {
        $storeId = $product->getStoreId();

        // Load again using getById to ensure every-thing is loaded.
        $product = $this->productRepository->getById(
            $product->getId(),
            false,
            $storeId
        );

        $productDataByStore[$storeId][] = $this->acmHelper->getProductDataForAPI($product);

        $this->pushMultipleProducts($productDataByStore, $action);
    }

    /**
     * pushProduct.
     *
     * Helper function to push product to front-end.
     *
     * @param array $productDataByStore
     * @param string $action
     */
    public function pushMultipleProducts($productDataByStore, $action = 'productSave' ) {

        // We need to have separate requests per store so we can assign them
        // correctly in middleware.
        foreach ($productDataByStore as $storeId => $arrayOfProducts) {

            // Send Connector request.
            $doReq = function ($client, $opt) use ($arrayOfProducts) {
                $opt['json'] = $arrayOfProducts;
                return $client->post('ingest/product', $opt);
            };
            $this->clientHelper->tryRequest($doReq, $action, $storeId);
        }
    }

    /**
     * pushStock.
     *
     * Helper function to push stock data through API.
     *
     * @param array $stockData
     *   Stock data.
     */
    public function pushStock($stockData) {
        // Send Connector request.
        $doReq = function ($client, $opt) use ($stockData) {
            $opt['json'] = $stockData;
            return $client->post(self::ENDPOINT_STOCK_UPDATE, $opt);
        };

        $this->clientHelper->tryRequest($doReq, 'pushStock');
    }


    /**
     * getStockInfo.
     *
     * Get stock info for a product.
     *
     * @param $productId
     * @param $scopeId
     * @param $returnObject
     *
     * @return array
     */
    public function getStockInfo($productId, $scopeId = NULL, $returnObject = FALSE)
    {
        // When consumers are running, StockRegistry uses static cache.
        // With this cache applied, stock for a particular product if
        // changed multiple times within lifespan of consumer, it pushes
        // only the first change every-time.
        // To avoid the issue, we use the code used to cache stock info
        // directly. Code taken from below class::method:
        // Magento\CatalogInventory\Model\StockRegistryProvider::getStockItem().
        $criteria = $this->stockItemCriteriaFactory->create();
        $criteria->setProductsFilter($productId);

        if ($scopeId) {
            $criteria->setScopeFilter($scopeId);
        }

        $collection = $this->stockItemRepository->getList($criteria);
        $stockItem = current($collection->getItems());

        if (!($stockItem && $stockItem->getItemId())) {
            $stockItem = $this->stockItemFactory->create();
        }

        if ($returnObject) {
            return $stockItem;
        }

        $stock = $this->serviceOutputProcessor->process(
            $stockItem,
            $this->stockServiceClassName,
            $this->serviceMethodName
        );

        return $stock;
    }

    /**
     * Add batch (consisting of product ids) to queue.
     *
     * @param mixed $batch
     */
    public function addBatchToQueue($batch)
    {
        $batch = is_array($batch) ? $batch : [$batch];

        // MessageQueue is EE only. So do nothing if there is no queue.
        $messageQueue = $this->getMessageQueue();
        if($messageQueue) {
            $this->getMessageQueue()->publish(self::PRODUCT_PUSH_CONSUMER, json_encode($batch));
        } else {
            // At 20180123, do nothing.
            // Later (Malachy or Anuj): Use Magento CRON instead
        }
    }

    /**
     * Add stock message (consisting of product id and sku) to queue.
     *
     * @param mixed $message
     */
    public function addStockMessageToQueue($message)
    {
        $this->publisher->publish(self::STOCK_PUSH_CONSUMER, $message);
    }

    /**
     * Get batch size from config.
     *
     * @return mixed
     */
    public function getProductPushBatchSize()
    {
        $path = 'webapi/acquia_commerce_settings/product_push_batch_size';

        $batchSize = $this->scopeConfig->getValue(
            $path,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        // Use 5 by default.
        if (empty($batchSize)) {
            $batchSize = 5;
        }

        return $batchSize;
    }

    /**
     * Get stock mode (pull / push).
     *
     * @return mixed
     */
    public function getStockMode() {
        $path = 'webapi/acquia_commerce_settings/push_stock';

        $stockMode = $this->scopeConfig->getValue(
            $path,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        return $stockMode ? 'push' : 'pull';
    }

}
