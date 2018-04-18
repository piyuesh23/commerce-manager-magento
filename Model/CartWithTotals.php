<?php

/**
 * Acquia/CommerceManager/Model/CartWithTotals.php
 *
 * Acquia Commerce Manager Combined Cart Totals Entity
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

use Acquia\CommerceManager\Api\Data\CartWithTotalsInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\TotalsInterface;

/**
 * CartWithTotals
 *
 * Acquia Commerce Manager Combined Cart Totals Entity
 */
class CartWithTotals implements CartWithTotalsInterface
{
    /**
     * Quote Entity
     * @var \Magento\Quote\Api\Data\CartInterface $quote
     */
    protected $quote;

    /**
     * Quote Totals Entity
     * @var \Magento\Quote\Api\Data\TotalsInterface $totals
     */
    protected $totals;

    /**
     * The response message info.
     * @var array
     */
    protected $responseMessage = [];

    /**
     * Constructor
     *
     * @param CartInterface $quote
     * @param TotalsInterface|null $totals
     */
    public function __construct(
        CartInterface $quote,
        TotalsInterface $totals = null,
        $responseMessage = []
    ) {
        $this->quote = $quote;
        $this->totals = $totals;
        $this->responseMessage = $responseMessage;
    }

    /**
     * getCart
     *
     * Get the Cart / Quote object updated / loaded.
     *
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function getCart()
    {
        return ($this->quote);
    }

    /**
     * getTotals
     *
     * Get the calculated cart totals.
     *
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function getTotals()
    {
        return ($this->totals);
    }

    /**
     * getResponseMessage
     *
     * Get the response message info.
     *
     * @return array
     */
    public function getResponseMessage()
    {
        return($this->responseMessage);
    }

}
