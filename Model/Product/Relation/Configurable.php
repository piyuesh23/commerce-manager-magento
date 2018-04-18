<?php

/**
 * Acquia/CommerceManager/Model/Product/Relation/Configurable.php
 *
 * Acquia Commerce Configurable Product API Relationship Processor
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Product\Relation;

use Acquia\CommerceManager\Model\Product\RelationInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

/**
 * Configurable
 *
 * Acquia Commerce Configurable Product API Relationship Processor
 */
class Configurable implements RelationInterface
{
    /**
     * Attribute Join Processor Service
     * @var JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    private $extensionAttributesJoinProcessor;

    /**
     * Constructor
     *
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * {@inheritDoc}
     */
    public function generateRelations(ProductInterface $product)
    {
        if (!$this->handlesProduct($product)) {
            return ([]);
        }

        // Load Linked Children.
        $productTypeInstance = $product->getTypeInstance();
        $productTypeInstance->setStoreFilter($product->getStoreId(), $product);

        $children = [];
        foreach ($productTypeInstance->getUsedProducts($product) as $child) {
            $children[] = ['id' => $child->getId(), 'sku' => $child->getSku()];
        }

        // Load Attribute Groups / Values.
        $groups = [];

        $attributeCollection = $productTypeInstance
            ->getConfigurableAttributeCollection($product);

        $this->extensionAttributesJoinProcessor->process($attributeCollection);

        foreach ($attributeCollection as $attribute) {
            $values = [];
            $attributeOptions = $attribute->getOptions();

            $eav = $attribute->getProductAttribute();
            $eav->setStoreId($product->getStoreId());

            $eavAttributeOptions = $eav->getSource()->getAllOptions(false);

            $eavAttributeOptionIndexed = [];
            foreach ($eavAttributeOptions as $option) {
                $eavAttributeOptionIndexed[$option['value']] = $option['label'];
            }

            if (is_array($attributeOptions)) {
                foreach ($attributeOptions as $option) {
                    $values[] = [
                        'value_id' => $option['value_index'],
                        'label' => $eavAttributeOptionIndexed[$option['value_index']],
                    ];
                }
            }

            $groups[] = [
                'attribute_id' => $eav->getAttributeId(),
                'code' => $eav->getAttributeCode(),
                'label' => $product->getResource()
                    ->getAttribute($eav->getAttributeCode())
                    ->getStoreLabel($product->getStoreId()),
                'position' => $attribute->getPosition(),
                'values' => $values,
            ];
        }

        return ([
            'configurable_product_links' => $children,
            'configurable_product_options' => $groups,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function handlesProduct(ProductInterface $product)
    {
        return ($product->getTypeId() === ConfigurableType::TYPE_CODE);
    }
}
