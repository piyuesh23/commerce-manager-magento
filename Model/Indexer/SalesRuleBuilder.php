<?php

/**
 * Acquia/CommerceManager/Model/Indexer/SalesRuleBuilder.php
 *
 * Acquia Commerce Manager Sales Rule / Product Index Builder
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Indexer;

use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Rule\Model\Condition\Combine as CombineCondition;
use Magento\SalesRule\Model\Rule\Condition\Product as ProductCondition;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection as RuleCollection;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * SalesRuleBuilder
 *
 * Acquia Commerce Manager Sales Rule / Product Index Builder
 */
class SalesRuleBuilder
{
    /**
     * Sales Rule / Product Discount Applier
     * @var SalesRuleApplier $applier
     */
    protected $applier;

    /**
     * Database Transaction Batch Size
     * @var int $batchSize
     */
    protected $batchSize;

    /**
     * Magento Framework DB connection
     * @var ResourceConnection $connection
     */
    protected $connection;

    /**
     * Magento Product Collection Factory
     * @var ProductCollectionFactory $productCollection
     */
    protected $productCollection;

    /**
     * Magento DB Resource
     * @var ResourceConnection $resource
     */
    protected $resource;

    /**
     * Sales Rule Collection Factory
     * @var RuleCollectionFactory $ruleCollection
     */
    protected $ruleCollection;

    /**
     * Magento System Logger
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @return void
     */
    public function __construct(
        ProductCollectionFactory $productCollection,
        RuleCollectionFactory $ruleCollection,
        ResourceConnection $resource,
        SalesRuleApplier $applier,
        LoggerInterface $logger,
        $batchSize = 1000
    ) {
        $this->productCollection = $productCollection;
        $this->ruleCollection = $ruleCollection;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->applier = $applier;
        $this->logger = $logger;
        $this->batchSize = $batchSize;
    }

    /**
     * reindexByProductIds
     *
     * Rebuild sales rule / product indexes by product IDs.
     *
     * @param int[] $ids Product Ids
     *
     * @return void
     */
    public function reindexByProductIds(array $ids)
    {
        $this->logger->info(
            'Reindexing by product IDS.',
            $ids
        );

        // Delete existing product rows
        $this->cleanByProductIds($ids);

        // Generate new product indexes

        $buildProducts = function () use ($ids) {
            return ($this->getProductCollection()->addIdFilter($ids));
        };

        $rules = $this->getRuleCollection();

        $this->buildProductRuleIndex($rules, $buildProducts);
    }

    /**
     * reindexByRuleIds
     *
     * Rebuild sales rule / product indexes by rule IDS.
     *
     * @param int[] $ids Sales Rule Ids
     *
     * @return void
     */
    public function reindexByRuleIds(array $ids)
    {
        $this->logger->info(
            'Reindexing by sales rule IDS.',
            $ids
        );

        // Delete existing rule rows
        $this->cleanByRuleIds($ids);

        // Generate new rule indexes
        $rules = $this->getRuleCollection()
            ->addFieldToFilter('rule_id', ['in' => $ids]);

        $this->buildProductRuleIndex($rules);
    }

    /**
     * reindexFull
     *
     * Rebuild the full sales rule / product index.
     *
     * @return void
     */
    public function reindexFull()
    {
        $this->logger->info('reindexFull: reindexing.');

        // Truncate existing index
        $this->cleanAll();

        // Build new product indexes for each rule
        $rules = $this->getRuleCollection();

        $this->buildProductRuleIndex($rules);
    }

    /**
     * buildProductRuleIndex
     *
     * Iterate rules provided for indexing and generate matching product
     * collections for each rule, then apply the rule to each product
     * and index discount results.
     *
     * @param RuleCollection $rules Sales Rules Collection
     * @param Callable $buildProducts Product collection building closure
     *
     * @return void
     */
    protected function buildProductRuleIndex(
        RuleCollection $rules,
        $buildProducts = null
    ) {
        if (!$buildProducts || !is_callable($buildProducts)) {
            $buildProducts = function () {
                return ($this->getProductCollection());
            };
        }

        $rows = [];

        foreach ($rules as $rule) {
            // Assemble matching products collection to rule conditions
            $products = $buildProducts();
            $conditions = $this->locateProductConditions($rule->getConditions());

            foreach ($conditions as $prodCond) {
                $attribute = $prodCond->getAttribute();
                $option = $prodCond->getOperatorForValidate();
                $value = $prodCond->getValueParsed();

                $comparisons = [
                    '==' => 'eq',
                    '!=' => 'neq',
                    '>' => 'gt',
                    '>=' => 'gteq',
                    '<' => 'lt',
                    '<=' => 'lteq',
                    '()' => 'in',
                    '!()' => 'nin',
                    '{}' => 'in',
                    '!{}' => 'nin',
                ];

                if (isset($comparisons[$option])) {
                    $compare = $comparisons[$option];
                } else {
                    continue 2;
                }

                if ($attribute == 'category_ids') {
                    $products->addCategoriesFilter([$compare => $value]);
                } else {
                    $products->addAttributeToFilter($attribute, [$compare => $value]);
                }
            }

            // Iterate matched products and calculate discounts
            foreach ($products as $product) {
                foreach ($product->getWebsiteIds() as $storeId) {
                    $discount = $this->applier->getDiscountData($product, $rule, $storeId);
                    if ($discount) {
                        $rows[] = [
                            'rule_id' => $rule->getId(),
                            'product_id' => $product->getId(),
                            'rule_price' => $discount->getAmount(),
                            'website_id' => $storeId,
                        ];

                        if (count($rows) >= $this->batchSize) {
                            $this->connection->insertMultiple(
                                $this->resource->getTableName('acq_salesrule_product'),
                                $rows
                            );

                            $rows = [];
                        }
                    }
                }
            }
        }

        if (!empty($rows)) {
            $this->connection->insertMultiple(
                $this->resource->getTableName('acq_salesrule_product'),
                $rows
            );
        }
    }

    /**
     * locateProductConditions
     *
     * Traverse rule collection combinations and locate product specific
     * conditions to filter the product collection.
     *
     * @param CombineCondition $combine Rule Conditions
     *
     * @return ProductCondition[] $prodCond
     */
    protected function locateProductConditions(CombineCondition $combine)
    {
        $prodCond = [];

        foreach ($combine->getConditions() as $cid => $condition) {
            if ($condition instanceof CombineCondition) {
                $prodCond = array_merge(
                    $prodCond,
                    $this->locateProductConditions($condition)
                );
            } elseif ($condition instanceof ProductCondition) {
                $prodCond[] = $condition;
            }
        }

        return ($prodCond);
    }

    /**
     * cleanAll
     *
     * Remove all saved index rows (truncate index).
     *
     * @return void
     */
    protected function cleanAll()
    {
        $this->connection->delete(
            $this->resource->getTableName('acq_salesrule_product')
        );
    }

    /**
     * cleanByProductIds
     *
     * Remove saved index rows by product ID.
     *
     * @param int[] $productIds Product IDS
     *
     * @return void
     */
    protected function cleanByProductIds(array $productIds)
    {
        $query = $this->connection->deleteFromSelect(
            $this->connection
                ->select()
                ->from($this->resource->getTableName('acq_salesrule_product'), 'product_id')
                ->distinct()
                ->where('product_id IN (?)', $productIds),
            $this->resource->getTableName('acq_salesrule_product')
        );

        $this->connection->query($query);
    }

    /**
     * cleanByRuleIds
     *
     * Remove saved index rows by rule ID.
     *
     * @param int[] $ruleIds Rule IDS
     *
     * @return void
     */
    protected function cleanByRuleIds(array $ruleIds)
    {
        $query = $this->connection->deleteFromSelect(
            $this->connection
                ->select()
                ->from($this->resource->getTableName('acq_salesrule_product'), 'rule_id')
                ->distinct()
                ->where('rule_id IN (?)', $ruleIds),
            $this->resource->getTableName('acq_salesrule_product')
        );

        $this->connection->query($query);
    }

    /**
     * getProductCollection
     *
     * Build a collection of enabled simple products to compare to
     * available rule conditions.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection $products
     */
    protected function getProductCollection()
    {
        $products = $this->productCollection->create();

        $products
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', Status::STATUS_ENABLED);

        return ($products);
    }

    /**
     * getRuleCollection
     *
     * Build a collections of active sales (cart) rules to iterate and
     * match products to.
     *
     * @return RuleCollection $rules
     */
    protected function getRuleCollection()
    {
        $rules = $this->ruleCollection->create();

        $rules->addFieldToFilter('is_active', 1);

        return ($rules);
    }
}
