<?php

/**
 * Acquia/CommerceManager/Model/Indexer/SalesRuleApplier.php
 *
 * Acquia Commerce Manager Sales Rule / Product Applier
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Indexer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\Quote\AddressFactory;
use Magento\Quote\Model\Quote\ItemFactory;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory;
use Magento\SalesRule\Model\Utility;
use Magento\SalesRule\Model\Validator;

/**
 * SalesRuleApplier
 *
 * Acquia Commerce Manager Sales Rule / Product Applier
 */
class SalesRuleApplier
{
    /**
     * General / Default Customer Group ID
     * @const CUSTOMER_GROUP_GENERAL
     */
    const CUSTOMER_GROUP_GENERAL = 1;

    /**
     * Discount Calculator Model Factory
     * @var CalculatorFactory $calculatorFactory
     */
    protected $calculatorFactory;

    /**
     * Sales Rule Validator Utility
     * @var Utility $validator
     */
    protected $validator;

    /**
     * Sales Rule Validator Utility
     * @var Validator $validator
     */
    protected $validatorModel;

    /**
     * Quote / Cart Model Factory
     * @var QuoteFactory $quoteFactory
     */
    protected $quoteFactory;

    /**
     * Quote Item Model Factory
     * @var ItemFactory $itemFactory
     */
    protected $itemFactory;

    /**
     * Quote Address Model Factory
     * @var AddressFactory $addressFactory
     */
    protected $addressFactory;

    /**
     * Constructor
     *
     * @param CalculatorFactory $calculatorFactory
     * @param Utility $validator
     * @param Validator $validatorModel
     * @param QuoteFactory $quoteFactory
     * @param ItemFactory $itemFactory
     * @param AddressFactory $addressFactory
     */
    public function __construct(
        CalculatorFactory $calculatorFactory,
        Utility $validator,
        Validator $validatorModel,
        QuoteFactory $quoteFactory,
        ItemFactory $itemFactory,
        AddressFactory $addressFactory
    ) {
        $this->calculatorFactory = $calculatorFactory;
        $this->validator = $validator;
        $this->validatorModel = $validatorModel;
        $this->quoteFactory = $quoteFactory;
        $this->itemFactory = $itemFactory;
        $this->addressFactory = $addressFactory;
    }

    /**
     * getDiscountData
     *
     * Validate a sales rule to determine if it applies to a product and
     * generate a quote / quote item for that product to calculate the
     * discount price.
     *
     * @param ProductInterface $product Product to test
     * @param Rule $rule Sales rule to test
     * @param int $storeId Store ID for rule / product
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data|null $discount
     */
    public function getDiscountData(
        ProductInterface $product,
        Rule $rule,
        $storeId
    ) {
        // Create Quote / Item for Product
        $item = $this->getProductQuoteItem($product, $storeId);

        if (!$this->validateRuleConditions($rule, $item)) {
            return (null);
        }

        // Calculate discount data / validate
        $calculator = $this->calculatorFactory->create($rule->getSimpleAction());

        // Initialize Validator model for fixed by cart discount type calculation.
        $code = ($rule->getPrimaryCoupon()) ? $rule->getPrimaryCoupon()->getCode() : '';
        $this->validatorModel->init(
            $storeId,
            self::CUSTOMER_GROUP_GENERAL,
            $code
        );
        $this->validatorModel->initTotals([$item], $item->getQuote()->getBillingAddress());

        $discountData = $calculator->calculate($rule, $item, 1);

        $this->validator->deltaRoundingFix($discountData, $item);
        $this->validator->minFix($discountData, $item, 1);

        return ($discountData);
    }

    /**
     * getProductQuoteItem
     *
     * Generate a quote / quote item and associated data for a product to
     * test rule conditions and generate discounts.
     *
     * @param ProductInterface $product Product to generate
     * @param int $storeId Store ID for quote / price
     *
     * @return CartItemInterface $item
     */
    protected function getProductQuoteItem(ProductInterface $product, $storeId)
    {
        $item = $this->itemFactory->create();
        $item
            ->setProduct($product)
            ->setStoreId($storeId)
            ->setPrice($product->getPrice())
            ->setCalculationPrice($product->getPrice())
            ->setBaseCalculationPrice($product->getPrice())
            ->setQty(1);

        if ($product->getTypeId() === ConfigurableType::TYPE_CODE) {
            $children = $product->getTypeInstance()->getUsedProducts($product);
            $children = (is_array($children) && count($children)) ? $children : [];

            foreach ($children as $child) {
                if (!$child->isSalable()) {
                    continue;
                }

                $product->setCustomOptions([
                    'simple_product' => new \Magento\Framework\DataObject([
                        'product' => $child,
                    ]),
                ]);

                $item
                    ->setPrice($product->getPrice())
                    ->setCalculationPrice($product->getPrice())
                    ->setBaseCalculationPrice($product->getPrice());

                break;
            }
        }

        $quote = $this->quoteFactory->create();
        $quote
            ->setStoreId($storeId)
            ->addItem($item);

        $address = $this->addressFactory->create();
        $address->addItem($item);
        $address->setData('cached_items_all', [$item]);

        $quote
            ->setBillingAddress($address)
            ->setShippingAddress($address);

        return ($item);
    }

    /**
     * validateRuleConditions
     *
     * Validate a generated quote item is applicable to a sales rule's
     * conditions and actions filters.
     *
     * @param Rule $rule Sales rule to validate
     * @param CartItemInterface $item Cart item to validate
     *
     * @return bool $valid
     */
    protected function validateRuleConditions(
        Rule $rule,
        CartItemInterface $item
    ) {
        return (
            $rule->validate($item->getAddress()) &&
            $rule->getActions()->validate($item)
        );
    }
}
