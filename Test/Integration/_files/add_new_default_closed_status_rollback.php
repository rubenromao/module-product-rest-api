<?php

use Magento\Sales\Model\Order;
use Rezolve\APISalesV4\Test\FixtureLoaders\OrderStatusLoader;

$orderStatusData = [
    [
        'status' => 'custom_status_in_closed_state',
        'label' => 'Custom Status In Closed State',
        'state' => Order::STATE_CLOSED,
        'is_default' => false,
        'visible_on_front' => true
    ], [
        'status' => 'closed',
        'label' => 'Cancelled',
        'state' => Order::STATE_CLOSED,
        'is_default' => true,
        'visible_on_front' => true
    ]
];
$orderStatusLoader = new OrderStatusLoader($orderStatusData);
$orderStatusLoader->createOrderStatuses();
