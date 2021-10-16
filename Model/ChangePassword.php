<?php
/**
 * @package     HitarthPattani\CustomerPassword
 * @author      Hitarth Pattani <hitarthpattani@gmail.com>
 * @copyright   Copyright © 2021. All rights reserved.
 */
declare(strict_types=1);

namespace HitarthPattani\CustomerPassword\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Store\Model\StoreManagerInterface;

class ChangePassword
{
    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var CustomerResource
     */
    private $customerResource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CustomerFactory $customerFactory
     * @param StoreManagerInterface $storeManager
     * @param CustomerResource $resource
     */
    public function __construct(
        CustomerFactory $customerFactory,
        StoreManagerInterface $storeManager,
        CustomerResource $resource
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerResource = $resource;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $website
     * @return void
     * @throws \Exception
     */
    public function execute(string $email, string $password, string $website = '')
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // phpcs:disable Magento2.Exceptions.DirectThrow.FoundDirectThrow
            throw new \Exception(sprintf('Invalid email address "%s".', $email));
            // phpcs:enable
        }

        $customer = $this->getCustomerByEmail($email, $website);
        $customer->setPassword($password);
        $this->customerResource->save($customer);
    }

    /**
     * @param $email
     * @param $website
     * @return Customer
     * @throws LocalizedException
     */
    private function getCustomerByEmail($email, $website): Customer
    {
        $customer = $this->customerFactory->create();

        if ($website) {
            $websiteId = $this->getWebsiteIdByCode($website);
            $customer->setWebsiteId($websiteId);
        }

        $this->customerResource->loadByEmail($customer, $email);

        if (!$customer->getId()) {
            // phpcs:disable Magento2.Exceptions.DirectThrow.FoundDirectThrow
            throw new \Exception(sprintf('No customer with email "%s" found.', $email));
            // phpcs:enable
        }

        return $customer;
    }

    /**
     * @param string $code
     * @return int
     * @throws LocalizedException
     */
    private function getWebsiteIdByCode(string $code): int
    {
        $website = $this->storeManager->getWebsite($code);

        if (! $website->getId()) {
            // phpcs:disable Magento2.Exceptions.DirectThrow.FoundDirectThrow
            throw new \Exception(sprintf('No website with ID "%s" found.', $code));
            // phpcs:enable
        }

        return (int) $website->getId();
    }
}
