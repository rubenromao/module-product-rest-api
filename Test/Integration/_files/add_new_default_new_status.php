<?php

use Magento\Sales\Model\Order;
use Rezolve\APISalesV4\Test\FixtureLoaders\OrderStatusLoader;

$orderStatusData = [
    [
        'status' => 'custom_status_in_new_state',
        'label' => 'Custom Status In New State',
        'state' => Order::STATE_NEW,
        'is_default' => true,
        'visible_on_front' => true
    ], [
        'status' => 'pending',
        'label' => 'Awaiting Payment',
        'state' => Order::STATE_NEW,
        'is_default' => false,
        'visible_on_front' => true
    ]
];
$orderStatusLoader = new OrderStatusLoader($orderStatusData);
$orderStatusLoader->createOrderStatuses();
