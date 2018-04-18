<?php

/**
 * Acquia/CommerceManager/Model/Indexer/SalesRuleProductProcessor.php
 *
 * Acquia Commerce Manager Sales Rule / Product Index Processor
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Indexer;

use Magento\Framework\Indexer\AbstractProcessor;

/**
 * SalesRuleProductProcessor
 *
 * Acquia Commerce Manager Sales Rule / Product Index Processor
 */
class SalesRuleProductProcessor extends AbstractProcessor
{
    /**
     * Indexer System ID
     * @const INDEXER_ID
     */
    const INDEXER_ID = 'acq_salesrule_product';
}
