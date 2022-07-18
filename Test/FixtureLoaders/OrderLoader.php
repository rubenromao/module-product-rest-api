<?php

namespace Rezolve\APISalesV4\Test\FixtureLoaders;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\Data\OrderExtension;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Report\Invoiced;
use Magento\Sales\Model\ResourceModel\Report\Order as OrderReport;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Store\Model\StoreManagerInterface;
use Rezolve\APISalesV4\Model\Basket\CoreOrderId;

class OrderLoader extends AbstractLoader
{
    private $orderData;

    /**
     * OrderLoader constructor.
     *
     * @param array $orderData
     */
    public function __construct(array $orderData)
    {
        $this->orderData = $orderData;
    }

    public function createOrders($processRefund = false)
    {
        foreach ($this->orderData as $order) {
            $state  = $order['state'];
            $_order = $this->createOrder($order);
            if ($processRefund && isset($order['partial_refund']) && $order['partial_refund'] === true) {
                $this->refundOrder($_order, true);
            }
            if ($state == 'canceled') {
                $this->refundOrder($_order);
            }
            $_order->setState($state)
                ->setStatus($_order->getConfig()->getStateDefaultStatus($state));
            $_order->save();
        }

        /** @var Invoiced $invoicedReport */
        $invoicedReport = $this->getObject(Invoiced::class);
        $invoicedReport->aggregate();

        /** @var OrderReport $orderReqport */
        $orderReport = $this->getObject(OrderReport::class);
        $orderReport->aggregate();
    }

    public function removeOrders()
    {
        $this->setSecureArea();
        /** @var $order \Magento\Sales\Model\Order */
        $orderCollection = $this->createObject(Collection::class);
        foreach ($orderCollection as $order) {
            $order->delete();
        }

        /**
         * I'm not messing around looking up the IDs for 100 orders - lets force the auto-increment back to 1
         */
        /** @var ResourceConnection $resource */
        $resource   = $this->createObject(ResourceConnection::class);
        $connection = $resource->getConnection();
        $table      = $connection->getTableName('sales_order');
        $connection->query("ALTER TABLE `$table` AUTO_INCREMENT=1");
        /**
         * Need to ensure that the increment ID is reset as well
         */
        $sequenceTable = $connection->getTableName('sequence_order_1');
        $connection->truncateTable($sequenceTable);
        $connection->query("ALTER TABLE `$sequenceTable` AUTO_INCREMENT=1");
    }

    private function createOrder(array $orderData)
    {
        $state         = 'processing';
        $incrementId   = $orderData['increment_id'];
        $customer      = $orderData['customer'];
        $customerEmail = $customer['email'];

        /** @var Order $order */
        $order = $this->createObject(Order::class);

        $order->loadByIncrementId($incrementId);
        if ($order->getId() !== null) {
            return;
        }
        $order
            ->setIncrementId($incrementId)
            ->setState($state)
            ->setStatus($order->getConfig()->getStateDefaultStatus($state));

        $subTotal   = 0;
        $grandTotal = 0;
        $tax        = 0;
        $isVirtual = true;
        foreach ($orderData['items'] as $item) {
            $orderItem = $this->getOrderItem($item);
            $order->addItem($orderItem);
            $subTotal   += $orderItem->getRowTotal();
            $grandTotal += $orderItem->getRowTotalInclTax();
            $tax        += $orderItem->getTaxAmount();
            if (!isset($item['type']) || $item['type'] != Type::TYPE_VIRTUAL) {
                $isVirtual = false;
            }
        }
        $order->setIsVirtual($isVirtual);
        $shipping = 0;
        if (isset($orderData['shipping'])) {
            $shipping   = $orderData['shipping'];
            $grandTotal += $shipping;
        }

        /** @var CoreOrderId $coreOrderId */
        $coreOrderId = $this->getObject(CoreOrderId::class);

        $extensionAttributes = $this->getExtensionAttributes($order);
        $extensionAttributes->setRezolveGeoLong($orderData['location']['long']);
        $extensionAttributes->setRezolveGeoLat($orderData['location']['lat']);
        $extensionAttributes->setRezolveOrderType($orderData['type']);
        $extensionAttributes->setRezolveOrderId($coreOrderId->convertToStorageVersion($orderData['rezolve_order_id']));
        $extensionAttributes->setRezolvePartnerId($orderData['partner_id']);
        if (isset($customer['unique_id'])) {
            $extensionAttributes->setRezolveCustomerId($customer['unique_id']);
        }
        $order->setExtensionAttributes($extensionAttributes);

        if (isset($orderData['shipping_description'])) {
            $order->setShippingDescription($orderData['shipping_description']);
        } else {
            $order->setShippingDescription('Flat Rate - Fixed');
        }
        if (isset($orderData['shipping_method'])) {
            $order->setShippingMethod($orderData['shipping_method']);
        }

        $orderDate = $orderData['created_at'];
        $order->setCreatedAt($orderDate)->setUpdatedAt($orderDate);

        $order->setTotalPaid($grandTotal)
              ->setTaxAmount($tax)
              ->setShippingAmount($shipping)
              ->setSubtotal($subTotal)
              ->setGrandTotal($grandTotal)
              ->setBaseSubtotal($subTotal)
              ->setBaseGrandTotal($grandTotal)
              ->setOrderCurrencyCode($orderData['currency'])
              ->setCustomerIsGuest(true)
              ->setCustomerEmail($customerEmail)
              ->setBillingAddress($this->getBillingAddress($customer))
              ->setShippingAddress($this->getShippingAddress($customer))
              ->setStoreId($this->getStoreId())
              ->setPayment($this->getPayment($orderData['payment']));

        $order->save();
        $this->createInvoice($order);
        return $order;
    }

    private function createInvoice(Order $order)
    {
        if ($order->canInvoice() === false) {
            return;
        }
        /** @var InvoiceService $invoiceService */
        $invoiceService = $this->getObject(InvoiceService::class);
        $transaction    = $this->getObject(\Magento\Framework\DB\Transaction::class);
        $invoice        = $invoiceService->prepareInvoice($order);
        $invoice->register();
        $invoice->setCreatedAt($order->getCreatedAt());
        $invoice->save();
        $transactionSave = $transaction->addObject(
            $invoice
        )->addObject(
            $invoice->getOrder()
        );
        $transactionSave->save();
    }

    private function getExtensionAttributes(Order $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->createObject(OrderExtension::class);
        }

        return $extensionAttributes;
    }

    private function getStoreId()
    {
        return $this->createObject(StoreManagerInterface::class)->getStore()->getId();
    }

    private function getAddressData(array $customerDetails)
    {
        return [
            'region'     => $customerDetails['region'],
            'postcode'   => $customerDetails['postcode'],
            'lastname'   => $customerDetails['last_name'],
            'firstname'  => $customerDetails['first_name'],
            'street'     => $customerDetails['street'],
            'city'       => $customerDetails['city'],
            'email'      => $customerDetails['email'],
            'telephone'  => $customerDetails['telephone'],
            'country_id' => $customerDetails['country_id'],
        ];
    }

    private function getBillingAddress(array $customerDetails)
    {
        $billingAddress = $this->createObject(Address::class);
        $billingAddress->setData($this->getAddressData($customerDetails));
        $billingAddress->setAddressType('billing');

        return $billingAddress;
    }

    private function getShippingAddress(array $customerDetails)
    {
        $shippingAddress = $this->createObject(Address::class);
        $shippingAddress->setData($this->getAddressData($customerDetails));
        $shippingAddress->setAddressType('shipping');

        return $shippingAddress;
    }

    private function getProduct($sku)
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->createObject(ProductRepository::class);

        return $productRepository->get($sku);
    }

    private function getOrderItem(array $itemData)
    {
        $sku           = $itemData['sku'];
        $quantity      = $itemData['qty'];
        $product       = $this->getProduct($sku);
        $basePrice     = $product->getFinalPrice();
        $rowTotal      = $basePrice * $quantity;
        $tax           = 0;
        $taxPercentage = 0;
        if (isset($itemData['tax'])) {
            $tax = $itemData['tax'];
        }
        if ($tax !== 0 && $basePrice !== 0) {
            $taxPercentage = round(($tax / ($basePrice)) * 100, 2);
        }
        $rowTotalIncTax = ($basePrice + $tax) * $quantity;

        /** @var Item $orderItem */
        $orderItem = $this->createObject(Item::class);
        $orderItem->setProductId($product->getId())->setQtyOrdered($quantity);
        $orderItem->setBasePrice($basePrice);
        $orderItem->setPrice($basePrice);
        $orderItem->setPriceInclTax($basePrice + $tax);
        $orderItem->setTaxAmount($tax * $quantity);
        $orderItem->setTaxPercent($taxPercentage);
        $orderItem->setRowTotal($rowTotal);
        $orderItem->setProductType($product->getTypeId());
        $orderItem->setRowTotalInclTax($rowTotalIncTax);
        $orderItem->setName($product->getName());
        $orderItem->setDescription($product->getDescription());
        if (isset($itemData['info_buyRequest'])) {
            $buyRequest = $this->handleOrderItems($itemData['info_buyRequest'], $product);
            $orderItem->setProductOptions($buyRequest);
        }

        return $orderItem;
    }

    private function getPayment(array $paymentData)
    {
        /** @var Payment $payment */
        $payment = $this->createObject(Payment::class);
        $payment->setMethod('rezolve')
                ->setAdditionalInformation(['info' => [
                        'data' => $paymentData['additional_data'],
                        'type' => $paymentData['type']
                    ]
                ]);

        return $payment;
    }

    private function handleOrderItems(array $options, ProductInterface $product)
    {
        $buyRequest     = [];
        $productOptions = [];
        if (isset($options['super_attribute'])) {
            $configurableOptions               = [];
            $productOptions['attributes_info'] = [];
            foreach ($options['super_attribute'] as $code => $value) {
                $productOptions['attributes_info'][] = [
                    'label' => $code,
                    'value' => $value
                ];
                $configurableOptions                 = $this->getAttributeDetails($code, $value, $configurableOptions);
            }
            $buyRequest['super_attribute'] = $configurableOptions;
        }
        if (isset($options['qty'])) {
            $buyRequest['qty'] = $options['qty'];
        }
        if (isset($options['options'])) {
            $customOptions             = [];
            $productOptions['options'] = [];
            foreach ($options['options'] as $optionName => $optionValue) {
                $optionDetails               = $this->getCustomOption(
                    $optionName,
                    $optionValue,
                    $product,
                    $customOptions
                );
                $customOptions[]             = $optionDetails;
                $productOptions['options'][] = $this->getOptionDetails($optionName, $optionValue, $product);
            }
            $buyRequest['options'] = $customOptions;
        }

        $productOptions['info_buyRequest'] = $buyRequest;

        return $productOptions;
    }

    private function getOptionDetails($optionName, $optionValue, ProductInterface $product)
    {
        $optionDetailArray = ['label' => $optionName, 'print_value' => $optionValue];
        $option            = $this->getOptionObject($optionName, $product);
        $values            = $option->getValues();

        if (null !== $values) {
            $selectValues = explode(',', $optionValue);
            $ids          = '';
            foreach ($selectValues as $selectValue) {
                foreach ($values as $value) {
                    if ($value->getTitle() == $selectValue) {
                        $ids .= $value->getOptionTypeId() . ',';
                    }
                }
                $optionDetailArray['option_value'] = trim($ids, ',');
            }
        }

        return $optionDetailArray;
    }

    private function getAttributeDetails($attributeCode, $optionName, $holdingArray)
    {
        /** @var Attribute $attribute */
        $attribute = $this->createObject(Attribute::class);
        $attribute->loadByCode('catalog_product', $attributeCode);
        $attributeId = $attribute->getId();

        $options  = $attribute->getOptions();
        $optionId = false;
        foreach ($options as $option) {
            if ($option->getData('label') == $optionName) {
                $optionId = $option->getData('value');
                break;
            }
        }
        if ($optionId === false) {
            throw new \Exception("Could not find an options of $optionName for the $attributeCode attribute");
        }

        $holdingArray[$attributeId] = $optionId;

        return $holdingArray;
    }

    private function getCustomOption($name, $value, ProductInterface $product, $holdingArray)
    {
        $selectedOption = $this->getOptionObject($name, $product);

        $holdingArray[$selectedOption->getOptionId()] = $value;

        return $holdingArray;
    }

    /**
     * @param                  $name
     * @param ProductInterface $product
     *
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface
     * @throws \Exception
     */
    private function getOptionObject($name, ProductInterface $product)
    {
        $customOptions = $product->getOptions();
        if (empty($customOptions)) {
            throw new \Exception('Product has no custom options');
        }

        $selectedOption = false;
        foreach ($customOptions as $option) {
            if ($option->getTitle() == $name) {
                $selectedOption = $option;
                break;
            }
        }

        if ($selectedOption === false) {
            throw new \Exception('Product has no custom options with title of ' . $name);
        }

        return $selectedOption;
    }

    public function refundOrder($order, $partial = false)
    {
        $refundOrder = $this->createObject(\Magento\Sales\Model\RefundOrder::class);
        $itemCreationFactory = $this->createObject(\Magento\Sales\Model\Order\Creditmemo\ItemCreationFactory::class);
        $creditmemoItem = $itemCreationFactory->create();
        $itemIdsToRefund = [];
        foreach ($order->getItems() as $item) {
            $itemId = $item->getId();
            $qty = $partial ? 1 : $item->getQtyOrdered();
            $creditmemoItem->setQty($qty)
                ->setOrderItemId($itemId);
            $itemIdsToRefund[] = $creditmemoItem;
            if ($partial) {
                break;
            }
        }
        $id = $refundOrder->execute(
            $order->getId(),
            $itemIdsToRefund
        );
        $creditmemo = $this->createObject(\Magento\Sales\Model\Order\Creditmemo::class)->load($id);
        $creditmemo->setCreatedAt($order->getCreatedAt());
        $creditmemo->save();
    }
}
