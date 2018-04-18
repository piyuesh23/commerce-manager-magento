<?php

/**
 * Acquia/CommerceManager/Model/TargetRuleRepository.php
 *
 * Acquia Commerce Manager Target / Related Product Repository
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use Acquia\CommerceManager\Api\Data\TargetRuleProductsInterface;
use Acquia\CommerceManager\Api\TargetRuleRepositoryInterface;
use Acquia\CommerceManager\Model\TargetRuleProducts;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Module\ModuleListInterface;

/**
 * TargetRuleRepository
 *
 * Acquia Commerce Manager Target / Related Product Repository
 */
class TargetRuleRepository implements TargetRuleRepositoryInterface
{

    /**
     * Magento Target Rule Module Name
     * @const TARGETRULE_MODULE
     */
    const TARGETRULE_MODULE = 'Magento_TargetRule';

    /**
     * String key for meta type all
     * @const ALL_TYPES
     */
    const ALL_TYPES = 'all';

    /**
     * Magento Module List Service
     * @var ModuleListInterface $moduleList
     */
    private $moduleList;

    /**
     * Product Collection Factory
     * @var CollectionFactory $productCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * Catalog Product Repository
     * @var ProductRepositoryInterface $productRepository
     */
    private $productRepository;

    /**
     * Target Rule Module Data Helper
     * @var \Magento\TargetRule\Helper\Data $targetRuleData
     */
    private $targetRuleData;

    /**
     * Target Rule Index Model
     * @var \Magento\TargetRule\Model\Index $targetRuleIndex
     */
    private $targetRuleIndex;

    /**
     * Product Visibility Model
     * @var Visibility $visibility
     */
    private $visibility;

    /**
     * Constructor
     *
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ModuleListInterface $moduleList,
        ProductRepositoryInterface $productRepository,
        CollectionFactory $productCollectionFactory,
        Visibility $visibility
    ) {
        $this->moduleList = $moduleList;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->visibility = $visibility;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $sku Product SKU
     * @param string $type Link Type: ['related', 'upsell', 'crosssell', 'all']
     *
     * @return \Acquia\CommerceManager\Api\Data\TargetRuleProductsInterface $products
     */
    public function getProductsByType($sku, $type)
    {
        $result = [];

        if (!$this->targetModuleEnabled()) {
            return (new TargetRuleProducts());
        }

        $ruleClass = 'Magento\TargetRule\Model\Rule';
        $types = [
            'related' => constant($ruleClass . '::RELATED_PRODUCTS'),
            'upsell' => constant($ruleClass . '::UP_SELLS'),
            'crosssell' => constant($ruleClass . '::CROSS_SELLS'),
        ];

        if (!isset($types[$type]) && ($type !== self::ALL_TYPES)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid relation type %s.',
                $type
            ));
        }

        $getTypes = ($type === self::ALL_TYPES) ? array_keys($types) : [$type];

        $product = $this->productRepository->get($sku);
        $excludeIds = [$product->getId()];

        foreach ($getTypes as $type) {
            $products = $this->getRuleProducts($types[$type], $product);
            $typeProducts = [];
            foreach ($products as $typeProduct) {
                $typeProducts[] = $typeProduct->getSku();
            }

            $result[$type] = $typeProducts;
        }

        return (new TargetRuleProducts(
            (isset($result['crosssell'])) ? $result['crosssell'] : [],
            (isset($result['related'])) ? $result['related'] : [],
            (isset($result['upsell'])) ? $result['upsell'] : []
        ));
    }

    /**
     * {@inheritDoc}
     *
     * @return bool $enabled
     */
    public function getTargetRulesEnabled()
    {
        return ($this->targetModuleEnabled());
    }

    /**
     * getRuleProducts
     *
     * Get a collection of products matching the type / current product.
     *
     * @return Collection $products
     */
    private function getRuleProducts($type, ProductInterface $product)
    {
        $limit = $this->getTargetRuleData()->getMaxProductsListResult();

        $indexModel = $this->getTargetRuleIndex()
            ->setType($type)
            ->setLimit($limit)
            ->setProduct($product)
            ->setExcludeProductIds([$product->getId()]);

        $productIds = $indexModel->getProductIds();
        $productIds = (count($productIds)) ? $productIds : [0];

        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['in' => $productIds]);

        $collection
            ->setPageSize($limit)
            ->setFlag('do_not_use_category_id', true)
            ->setVisibility($this->visibility->getVisibleInCatalogIds());

        return ($collection);
    }

    /**
     * getTargetRuleData
     *
     * Get target rule data helper from the ObjectManager to prevent
     * coupling to EE.
     *
     * @return \Magento\TargetRule\Helper\Data $ruleData
     */
    private function getTargetRuleData()
    {
        if (!$this->targetRuleData) {
            $this->targetRuleData = ObjectManager::getInstance()->get(
                \Magento\TargetRule\Helper\Data::class
            );
        }

        return ($this->targetRuleData);
    }

    /**
     * getTargetRuleIndex
     *
     * Build a Target Rule Index model from the ObjectManager to prevent
     * coupling to EE.
     *
     * @return \Magento\TargetRule\Model\Index $index
     */
    public function getTargetRuleIndex()
    {
        if (!$this->targetRuleIndex) {
            $this->targetRuleIndex = ObjectManager::getInstance()->get(
                \Magento\TargetRule\Model\IndexFactory::class
            )->create();
        }

        return ($this->targetRuleIndex);
    }

    /**
     * targetModuleEnabled
     *
     * Check if the Magento EE Target Rules Module is installed / enabled.
     *
     * @return bool $enabled
     */
    private function targetModuleEnabled()
    {
        return ($this->moduleList->has(self::TARGETRULE_MODULE));
    }
}
