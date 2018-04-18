<?php
namespace Acquia\CommerceManager\Plugin;
class CustomerRepositoryPlugin
{
    private $storeManager;
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->storeManager = $storeManager;
    }
    /**
     * We want to improve the Magento function. Currently this function is called
     * for both 'save' and 'update' but the core function does a bad job of
     * determining which it is and in the process makes a mess of the website_id
     *
     * The main purpose of this plugin is to avoid the need to pass in website_id
     * in the request body because store_id is sufficient.
     * Now we need only send in store_id => create customer
     * *or* store_id and customer_id => update customer
     *
     * You can omit store_id. In that case we use the current store to get the store_id
     * and thence to get the website_id -- so make sure you are calling this function
     * using the full REST API URL including the store code so that store_id is set here.
     *
     * Code for setting website_id can be found here for example
     * \Magento\Customer\Model\AccountManagement::createAccountWithPasswordHash()
     *
     * Background on the need for this plugin can be found here
     * https://github.com/magento/magento2/issues/5115
     * https://magento.stackexchange.com/questions/122097/cant-put-customer-in-rest-api-magento2-fails-to-complete
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $subject
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param null $passwordHash
     * @return array
     */
    public function beforeSave($subject, \Magento\Customer\Api\Data\CustomerInterface $customer, $passwordHash = null)
    {
        // Kill any website_id passed here. Do not send website_id. Use only store_id.
        $customer->setWebsiteId(null);
        // If we are creating a new customer, don't do anything more
        if (!$customer->getId())
        {
            return [$customer, $passwordHash];
        }
        // If we are updating a customer. Set the website_id based on the store ID
        $storeId = $customer->getStoreId();
        if ($storeId === null)
        {
            $storeId = $this->storeManager->getStore()->getId();
            $customer->setStoreId($storeId);
        }
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        // Please note that here we over-write any erroneously-sent-in website_id
        $customer->setWebsiteId($websiteId);
        return [$customer, $passwordHash];
    }
}
