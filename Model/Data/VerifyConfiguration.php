<?php

namespace Acquia\CommerceManager\Model\Data;

/**
 * Verify configuration data model.
 */
class VerifyConfiguration
    extends \Magento\Framework\Api\AbstractSimpleObject
        implements \Acquia\CommerceManager\Api\Data\VerifyConfigurationInterface
{
    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get store code
     *
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_get(self::STORE_CODE);
    }

    /**
     * Set store code
     *
     * @param string $storeCode
     * @return string|null
     */
    public function setStoreCode($storeCode)
    {
        return $this->setData(self::STORE_CODE, $storeCode);
    }

    /**
     * Get website id
     *
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->_get(self::WEBSITE_ID);
    }

    /**
     * Set website id
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * Get website code
     *
     * @return string
     */
    public function getWebsiteCode()
    {
        return $this->_get(self::WEBSITE_CODE);
    }

    /**
     * Set website code
     *
     * @param string $websiteCode
     * @return string|null
     */
    public function setWebsiteCode($websiteCode)
    {
        return $this->setData(self::WEBSITE_CODE, $websiteCode);
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Set description code
     *
     * @param string $description
     * @return string|null
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->_get(self::LOCALE);
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return string|null
     */
    public function setLocale($locale)
    {
        return $this->setData(self::LOCALE, $locale);
    }

    /**
     * Get default currency
     *
     * @return string
     */
    public function getDefaultCurrency()
    {
        return $this->_get(self::DEFAULT_CURRENCY);
    }

    /**
     * Set default currency
     *
     * @param string $defaultCurrency
     * @return string|null
     */
    public function setDefaultCurrency($defaultCurrency)
    {
        return $this->setData(self::DEFAULT_CURRENCY, $defaultCurrency);
    }

    /**
     * Get system API URL
     *
     * @return string
     */
    public function getSystemApiUrl()
    {
        return $this->_get(self::SYSTEM_API_URL);
    }

    /**
     * Set system API URL
     *
     * @param string $systemApiUrl
     * @return string|null
     */
    public function setSystemApiUrl($systemApiUrl)
    {
        return $this->setData(self::SYSTEM_API_URL, $systemApiUrl);
    }

    /**
     * Get system advice
     *
     * @return string
     */
    public function getSystemAdvice()
    {
        return $this->_get(self::SYSTEM_ADVICE);
    }

    /**
     * Set system advice
     *
     * @param string $systemAdvice
     * @return string|null
     */
    public function setSystemAdvice($systemAdvice)
    {
        return $this->setData(self::SYSTEM_ADVICE, $systemAdvice);
    }

    /**
     * Get connector API URL
     *
     * @return string
     */
    public function getConnectorApiUrl()
    {
        return $this->_get(self::CONNECTOR_API_URL);
    }

    /**
     * Set connector API URL
     *
     * @param string $connectorApiUrl
     * @return string|null
     */
    public function setConnectorApiUrl($connectorApiUrl)
    {
        return $this->setData(self::CONNECTOR_API_URL, $connectorApiUrl);
    }

    /**
     * Get ACM_UUID
     *
     * @return string
     */
    public function getAcmUuid()
    {
        return $this->_get(self::ACM_UUID);
    }

    /**
     * Set AcmUuid
     *
     * @param string $acmUuid
     * @return string|null
     */
    public function setAcmUuid($acmUuid)
    {
        return $this->setData(self::ACM_UUID, $acmUuid);
    }


    /**
     * Get passed-verification field
     *
     * @return bool
     */
    public function getPassedVerification()
    {
        return $this->_get(self::PASSED_VERIFICATION);
    }

    /**
     * Set passed-verification field
     *
     * @param bool $passedVerification
     * @return string|null
     */
    public function setPassedVerification($passedVerification)
    {
        return $this->setData(self::PASSED_VERIFICATION, $passedVerification);
    }

}
