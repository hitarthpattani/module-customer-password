<?php
/**
 * @package     HitarthPattani\CustomerPassword
 * @author      Hitarth Pattani <hitarthpattani@gmail.com>
 * @copyright   Copyright © 2021. All rights reserved.
 */
declare(strict_types=1);

namespace HitarthPattani\CustomerPassword\Test\Integration\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Customer;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\ObjectManager;
use HitarthPattani\CustomerPassword\Model\ChangePassword;

class ChangePasswordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @return void
     */
    public function setup()
    {
        $this->email = "roni_cost@example.com";
        $this->password = uniqid("customer-password");
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testChangeCustomerPasswordWithGlobalCustomerAccounts()
    {
        $changePassword = $this->createChangePasswordModel();
        $changePassword->execute($this->email, $this->password);

        $this->assertTrue($this->createCustomerModel()->authenticate($this->email, $this->password));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testChangeCustomerPasswordWithWebsiteScopeAccounts()
    {
        $website = $this->getDefaultWebsiteCode();

        $changePassword = $this->createChangePasswordModel();
        $changePassword->execute($this->email, $this->password, $website);

        $customer = $this->instantiateCustomerModel();
        $customer->setWebsiteId($this->getDefaultWebsiteId());

        $this->assertTrue($customer->authenticate($this->email, $this->password));
    }

    /**
     * @return ChangePassword
     */
    private function createChangePasswordModel(): ChangePassword
    {
        return ObjectManager::getInstance()->create(ChangePassword::class);
    }

    /**
     * @return Customer
     */
    private function createCustomerModel(): Customer
    {
        return ObjectManager::getInstance()->create(Customer::class);
    }

    /**
     * @return StoreManagerInterface
     */
    private function getStoreManager(): StoreManagerInterface
    {
        return ObjectManager::getInstance()->get(StoreManagerInterface::class);
    }

    /**
     * @return int
     */
    private function getDefaultWebsiteId(): int
    {
        return (int) $this->getStoreManager()->getDefaultStoreView()->getWebsiteId();
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    private function getDefaultWebsiteCode(): string
    {
        return $this->getStoreManager()->getWebsite($this->getDefaultWebsiteId())->getCode();
    }
}
