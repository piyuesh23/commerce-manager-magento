<?php

/**
 * Acquia/CommerceManager/Model/VerifyConfiguration.php
 *
 * Acquia Commerce Manager
 *
 * All rights reserved. No unauthorized distribution or reproduction.
 */

namespace Acquia\CommerceManager\Model;

/**
 * VerifyConfiguration
 *
 * Methods to verify the Commerce Connector configurations
 */

class VerifyConfiguration implements \Acquia\CommerceManager\Api\VerifyConfigurationInterface
{
    /** @var \Acquia\CommerceManager\Helper\Data */
    private $helper;

    /** @var  \Acquia\CommerceManager\Api\Data\VerifyConfigurationInterfaceFactory */
    private $verificationDataFactory;

    /** @var  \Acquia\CommerceManager\Api\Data\VerifyConfigurationInterface */
    private $verificationData;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface  */
    private $scopeConfig;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Acquia\CommerceManager\Helper\Data $helper,
        \Acquia\CommerceManager\Api\Data\VerifyConfigurationInterfaceFactory $verifyConfigurationFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->verificationDataFactory = $verifyConfigurationFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $customerId Customer ID
     *
     * @return \Acquia\CommerceManager\Api\Data\VerifyConfigurationInterface
     */
    public function verifyConfiguration()
    {
        $advice = "";
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();
        $storeId = $store->getId();
        $website = $store->getWebsite();
        $websiteId = $website->getWebsiteId();
        $connectorOptions = $this->helper->getApiClientOptions([],$storeId);

        $this->verificationData = $this->verificationDataFactory->create();

        // Know your store view (code, id, locale, base currency, description)
        $this->verificationData->setStoreId($storeId);
        $this->verificationData->setStoreCode($store->getCode());
        $this->verificationData->setLocale(
            $this->scopeConfig->getValue('general/locale/code',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)
        );
        // Also ->getBaseCurrencyCode() but in product sync we get prices in defaultCurrencyCode
        $this->verificationData->setDefaultCurrency($store->getDefaultCurrencyCode());

        $advice .= "Store view name: ".$store->getName();
        if($store->isActive())
        {
            $advice .= " (is active).";
        }
        else
        {
            $advice .= " (not active).";
        }

        // Know your website (code, id, description)
        /** @var \Magento\Store\Model\Website $website */
        $this->verificationData->setWebsiteId($websiteId);
        $this->verificationData->setWebsiteCode($website->getCode());
        $advice .= "\nWebsite name: ".$website->getName();
        $advice .= " (store group name ".$store->getGroup()->getName().").";

        // Know your URLs (this API url, Commerce Connector URL)
        $this->verificationData->setSystemApiUrl(strstr($store->getCurrentUrl(true),"?",true));
        $this->verificationData->setConnectorApiUrl($connectorOptions['base_uri']);

        // Know your ACM-UUID
        $this->verificationData->setAcmUuid($connectorOptions['headers']['X-ACM-UUID']);

        // TODO (malachy): Check for unique ACM-UUID (one per store-view)

        $this->verificationData->setPassedVerification(false);
        // Check if the module is enabled.
        if (!$this->helper->getApiConfigData('acquia_commerce/connector_enable',$storeId))
        {
            $advice .= "\nThe Magento module is not enabled with {".$this->helper->getApiConfigData('acquia_commerce/connector_enable',$storeId)."}.";
        }
        else
        {
            // Check connection to CommerceConnector (config get mapping)
            // Except we can't know this system's mapping ID
            // See $this->helper->tryRequest but we break it down here for more control
            $canContinue = false;
            try {
                $client = $this->helper->getApiClient($storeId);
                $canContinue = true;
            }
            catch (\Exception $error)
            {
                $advice .= "\nCould not create client.";
                $advice .= "\n".$error->getCode();
                $advice .= "\n".$error->getMessage().".";
            }

            $body = null;
            if($canContinue)
            {
                try
                {
                    // TODO (malachy): pick a different endpoint (this will always 401)
                    $result = $client->get("config/system/1", []);
                    $body = $result->getBody();
                    $statusCode = $result->getStatusCode();
                    $advice .= "Commerce Connector connection test result: (HTTP "
                        .$statusCode.") ".$body;
                    switch ($statusCode)
                    {
                        case 200:
                            // Verify the result
                            // TODO (malachy): Develop result verification logic after test endpoint is known
                            $this->verificationData->setPassedVerification(true);
                            $advice .= "\nOutbound connection passed.";
                            break;
                        default:
                            $advice .= "\nOutbound connection failed.";
                    }
                }
                catch (\GuzzleHttp\Exception\RequestException $error)
                {
                    $advice .= "\nError connecting to Commerce Connector";
                    $advice .= "\n".$error->getCode();
                    $advice .= "\n".$error->getMessage().".";
                }
                catch (\Exception $error)
                {
                    $advice .= "\nSystem-side exception whilst trying to connect to Commerce Manager. Perhaps HMAC key and secret are missing from Magento configuration.";
                    $advice .= "\n".$error->getCode();
                    $advice .= "\n".$error->getMessage().".";
                }
            }
        }
        $this->verificationData->setSystemAdvice($advice);

        return $this->verificationData;
    }


}
