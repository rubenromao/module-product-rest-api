<?php

namespace Rezolve\APISalesV4\Test\FixtureLoaders;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class CustomerLoader extends AbstractLoader
{
    private $customerData;

    public function __construct($customerData)
    {
        $this->customerData = $customerData;
    }

    public function createCustomers()
    {
        $this->setSecureArea();
        /** @var CustomerFactory $factory */
        $factory = $this->createObject(CustomerFactory::class);
        foreach ($this->customerData as $customer) {
            $customerModel = $factory->create();
            try {
                $customerModel->setWebsiteId($customer['website_id']);
                $customerModel->loadByEmail($customer['email']);
            } catch (NoSuchEntityException $e) {
                // If they don't exist lets make that clear
                $customerModel->isObjectNew(true);
            }

            foreach ($customer as $key => $value) {
                $customerModel->setData($key, $value);
            }

            $customerModel->save();
        }
    }

    public function removeCustomers()
    {
        $this->setSecureArea();
        /** @var CustomerRepositoryInterface $repository */
        $repository = $this->createObject(CustomerRepositoryInterface::class);
        foreach ($this->customerData as $customer) {
            try {
                $currentCustomer = $repository->get($customer['email']);
                $repository->deleteById($currentCustomer->getId());
            } catch (NoSuchEntityException $e) {
                // If they don't exist I don't care that I can't delete them
            }
        }
    }
}
