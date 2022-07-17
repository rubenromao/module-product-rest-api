<?php

namespace Rezolve\APISalesV4\Test\FixtureLoaders;

use Magento\Tax\Model\Calculation\Rate;
use Magento\Tax\Model\Calculation\Rule;
use Magento\Tax\Model\ClassModel;
use Magento\Tax\Model\TaxRuleRepository;

class TaxLoader extends AbstractLoader
{
    private $taxDetails;

    private $taxRules;

    public function __construct($taxDetails)
    {
        $this->taxDetails = $taxDetails;
    }

    public function createTaxRules()
    {
        foreach ($this->taxDetails as $details) {
            $customerTaxClass = $this->createCustomerClass($details['customer_class_name']);
            $productTaxClass  = $this->createProductClass($details['product_class_name']);
            $taxRate          = $this->createTaxRate($details['rate_details']);
            $ruleDetails      = [
                'code'                   => $details['rule_name'],
                'priority'               => '0',
                'position'               => '0',
                'customer_tax_class_ids' => [$customerTaxClass->getId(), 3],
                'product_tax_class_ids'  => [$productTaxClass->getId()],
                'tax_rate_ids'           => [$taxRate->getId()],
            ];
            $this->createTaxRule($ruleDetails);
        }
    }

    public function removeTaxRules()
    {
        foreach ($this->taxDetails as $details) {
            $this->removeTaxRule($details['rule_name']);
            $this->removeTaxRate($details['rate_details']['code']);
            $this->removeCustomerTaxClass($details['customer_class_name']);
            $this->removeProductTaxClass($details['product_class_name']);
        }
    }

    private function removeTaxRule($ruleName)
    {
        if (null === $this->taxRules) {
            $rules = [];
            /** @var Rule $class */
            $class          = $this->createObject(Rule::class);
            $ruleCollection = $class->getCollection();
            foreach ($ruleCollection as $rule) {
                /** @var Rule $rule */
                $rules[$rule->getCode()] = $rule->getId();
            }
            $this->taxRules = $rules;
        }

        if (!isset($this->taxRules[$ruleName])) {
            throw new \Exception("Could not find a tax rule with code of $ruleName");
        }

        /** @var TaxRuleRepository $repository */
        $repository = $this->createObject(TaxRuleRepository::class);
        $repository->deleteById($this->taxRules[$ruleName]);
    }

    private function removeTaxRate($code)
    {
        /** @var Rate $rate */
        $rate = $this->createObject(Rate::class);
        $rate->loadByCode($code);
        $rate->delete();
    }

    private function removeProductTaxClass($name)
    {
        $type = ClassModel::TAX_CLASS_TYPE_PRODUCT;
        $this->removeTaxClass($type, $name);
    }

    private function removeCustomerTaxClass($name)
    {
        $type = ClassModel::TAX_CLASS_TYPE_CUSTOMER;
        $this->removeTaxClass($type, $name);
    }

    private function removeTaxClass($type, $name)
    {
        $class = $this->getTaxClass($type, $name);
        $class->delete();
    }

    private function createCustomerClass($name)
    {
        $type = ClassModel::TAX_CLASS_TYPE_CUSTOMER;

        return $this->createTaxClass($name, $type);
    }

    private function createProductClass($name)
    {
        $type = ClassModel::TAX_CLASS_TYPE_PRODUCT;

        return $this->createTaxClass($name, $type);
    }

    private function createTaxRate(array $rateDetails)
    {
        $code = $rateDetails['code'];
        /** @var Rate $rate */
        $rate = $this->createObject(Rate::class);
        $rate->loadByCode($code);
        if ($rate->getId() !== null) {
            return $rate;
        }

        $rate->setData($rateDetails)->save();

        return $rate;
    }

    private function createTaxRule($ruleDetails)
    {
        $code = $ruleDetails['code'];
        /** @var Rule $taxRule */
        $taxRule = $this->createObject(Rule::class);
        $taxRule->load($code, 'code');
        if ($taxRule->getId() !== null) {
            return $taxRule;
        }
        $taxRule->setData($ruleDetails)->save();

        return $taxRule;
    }

    private function createTaxClass($name, $type)
    {
        /** @var ClassModel $taxClass */
        $taxClass = $this->createObject(ClassModel::class);

        $taxClass->load($name, 'class_name');
        if ($taxClass->getClassId() !== null && $taxClass->getClassType() == $type) {
            return $taxClass;
        }

        if ($taxClass->getClassId() !== null) {
            $taxClass = $this->createObject(ClassModel::class);
        }
        $taxClass->setClassName($name)->setClassType($type)->save();

        return $taxClass;
    }
}
