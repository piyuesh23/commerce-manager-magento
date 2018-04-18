<?php

/**
 * Acquia/CommerceManager/Observer/ConnectorObserver.php
 *
 * Acquia Commerce Connector Observer Abstract
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Observer;

use Acquia\CommerceManager\Helper\Data as ClientHelper;
use Acquia\CommerceManager\Helper\Acm as AcmHelper;
use Magento\Framework\Webapi\ServiceOutputProcessor;
use Psr\Log\LoggerInterface;

/**
 * ConnectorObserver
 *
 * Acquia Commerce Connector Observer Abstract
 */
abstract class ConnectorObserver
{
    /**
     * Acquia Commerce Manager Client Helper
     * @var ClientHelper $clientHelper
     */
    protected $clientHelper;

    /**
     * @var AcmHelper $acmHelper
     */
    protected $acmHelper;

    /**
     * System Logger
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * Magento WebAPI Output Processor
     * @var ServiceOutputProcessor $serviceOutputProcessor
     */
    protected $serviceOutputProcessor;

    /**
     * Magento WebAPI Service Class Name (for output formatting)
     * @var string $serviceClassName
     */
    protected $serviceClassName;

    /**
     * Magento WebAPI Service Method Name (for output formatting)
     * @var string $serviceMethodName
     */
    protected $serviceMethodName;

    /**
     * ConnectorObserver constructor.
     * @param AcmHelper $acmHelper
     * @param ClientHelper $helper
     * @param ServiceOutputProcessor $outputProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        AcmHelper $acmHelper,
        ClientHelper $helper,
        ServiceOutputProcessor $outputProcessor,
        LoggerInterface $logger
    ) {
        $this->acmHelper = $acmHelper;
        $this->clientHelper = $helper;
        $this->serviceOutputProcessor = $outputProcessor;
        $this->logger = $logger;
    }

    /**
     * formatApiOutput
     *
     * Format a Magento entity into a JSON package as returned by the
     * normal Magento REST APIs.
     *
     * @param mixed $object Entity to format
     *
     * @return array|int|string|bool|float Scalar or array of scalars
     */
    protected function formatApiOutput($object)
    {
        if ($this->serviceClassName === null
            || $this->serviceMethodName === null) {
            throw new \RuntimeException('formatApiOutput requires service class / method name.');
        }

        $output = $this->serviceOutputProcessor->process(
            $object,
            $this->serviceClassName,
            $this->serviceMethodName
        );

        return ($output);
    }

    /**
     * tryRequest
     *
     * Make an API request to a Connector ingest endpoint.
     *
     * @param callable $doReq Guzzle request closure
     * @param string $action Action Name (for logging)
     *
     * @return bool $success
     */
    public function tryRequest(callable $doReq, $action, $storeId)
    {
        return ($this->clientHelper->tryRequest($doReq, $action, $storeId));
    }
}
