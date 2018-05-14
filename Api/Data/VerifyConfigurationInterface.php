<?php

namespace Acquia\CommerceManager\Api\Data;

interface VerifyConfigurationInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const STORE_ID = 'store_id';
    const STORE_CODE = 'store_code';
    const WEBSITE_ID = 'website_id';
    const WEBSITE_CODE = 'website_code';
    const DESCRIPTION = 'description';
    const LOCALE = 'locale';
    const DEFAULT_CURRENCY = 'default_currency';
    const SYSTEM_API_URL = 'system_api_url';
    const SYSTEM_ADVICE = 'system_advice';
    const CONNECTOR_API_URL = 'connector_api_url';
    const ACM_UUID = 'acm_uuid';
    const PASSED_VERIFICATION = 'passed_verification';
    /**#@-*/

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get store code
     *
     * @return string
     */
    public function getStoreCode();

    /**
     * Set store code
     *
     * @param string $storeCode
     * @return $this
     */
    public function setStoreCode($storeCode);

    /**
     * Get website id
     *
     * @return int
     */
    public function getWebsiteId();

    /**
     * Set website id
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * Get website code
     *
     * @return string
     */
    public function getWebsiteCode();

    /**
     * Set website code
     *
     * @param string $websiteCode
     * @return $this
     */
    public function setWebsiteCode($websiteCode);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale();

    /**
     * Set locale
     *
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale);

    /**
     * Get default currency
     *
     * @return string
     */
    public function getDefaultCurrency();

    /**
     * Set default currency
     *
     * @param string $defaultCurrency
     * @return $this
     */
    public function setDefaultCurrency($defaultCurrency);

    /**
     * Get system API URL
     *
     * @return string
     */
    public function getSystemApiUrl();

    /**
     * Set system API URL
     *
     * @param string $systemApiUrl
     * @return $this
     */
    public function setSystemApiUrl($systemApiUrl);

    /**
     * Get system advice
     *
     * @return string
     */
    public function getSystemAdvice();

    /**
     * Set system advice
     *
     * @param string $systemAdvice
     * @return $this
     */
    public function setSystemAdvice($systemAdvice);

    /**
     * Get connector API Url
     *
     * @return string
     */
    public function getConnectorApiUrl();

    /**
     * Set connector API Url
     *
     * @param string $connectorApiUrl
     * @return $this
     */
    public function setConnectorApiUrl($connectorApiUrl);

    /**
     * Get ACM_UUID
     *
     * @return string
     */
    public function getAcmUuid();

    /**
     * Set ACM_UUID
     *
     * @param string $acmUuid
     * @return $this
     */
    public function setAcmUuid($acmUuid);

    /**
     * Get passed-verification field
     *
     * @return bool
     */
    public function getPassedVerification();

    /**
     * Set passed-verification field
     *
     * @param bool $passedVerification
     * @return $this
     */
    public function setPassedVerification($passedVerification);

}