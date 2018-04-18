<?php

/**
 * Acquia/CommerceManager/Model/Plugin/SalesRuleIndexer/Website.php
 *
 * Acquia Commerce Manager Sales Rule Indexer Website Plugin
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model\Plugin\SalesRuleIndexer;

use Acquia\CommerceManager\Model\Indexer\ProductSalesRuleProcessor;
use Magento\Store\Model\Website as WebsiteEntity;

/**
 * Website
 *
 * Acquia Commerce Manager Sales Rule Indexer Website Plugin
 */
class Website
{
    /**
     * Sales Rule Index Product Processor
     * @var ProductSalesRuleProcessor $processor
     */
    protected $processor;

    /**
     * Constructor
     *
     * @param ProductSalesRuleProcessor $processor
     */
    public function __construct(ProductSalesRuleProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * afterSave
     *
     * After Website save, mark the indexer as invalid.
     *
     * @param WebsiteEntity $subject
     * @param WebsiteEntity $result
     *
     * @return WebsiteEntity
     */
    public function afterSave(
        WebsiteEntity $subject,
        WebsiteEntity $result
    ) {
        //a dummy instruction to avoid code-sniff warning '$subject isn't used'
        get_class($subject);

        $this->processor->markIndexerAsInvalid();
        return ($result);
    }

    /**
     * afterDelete
     *
     * After Product Website delete, mark the indexer as invalid.
     * @param WebsiteEntity $subject
     * @param WebsiteEntity $result
     *
     * @return WebsiteEntity
     */
    public function afterDelete(
        WebsiteEntity $subject,
        WebsiteEntity $result
    ) {
        //a dummy instruction to avoid code-sniff warning '$subject isn't used'
        $subject->getCode();

        $this->processor->markIndexerAsInvalid();
        return ($result);
    }
}
