<?php

/**
 * Model/Plugin/SalesRuleIndexer/SalesRule.php
 *
 * Acquia Commerce Manager Sales Rule Indexer Rule Plugin
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Plugin\SalesRuleIndexer;

use Acquia\CommerceManager\Model\Indexer\SalesRuleProductProcessor;
use Magento\SalesRule\Model\Rule;

/**
 * SalesRule
 *
 * Acquia Commerce Manager Sales Rule Indexer Rule Plugin
 */
class SalesRule
{
    /**
     * Sales Rule Index Product Processor
     * @var SalesRuleProductProcessor $processor
     */
    protected $processor;

    /**
     * Constructor
     *
     * @param SalesRuleProductProcessor $processor
     */
    public function __construct(SalesRuleProductProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * afterSave
     *
     * After Sales Rule save, reindex the affected rule.
     *
     * @param Rule $subject
     * @param Rule $result
     *
     * @return Rule
     */
    public function afterSave(
        Rule $subject,
        Rule $result
    ) {
        //a dummy instruction to avoid code-sniff warning '$subject isn't used'
        get_class($subject);

        $this->processor->reindexList([$result->getId()]);
        return ($result);
    }
}
