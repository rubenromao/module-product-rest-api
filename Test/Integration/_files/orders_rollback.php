<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\OrderLoader;

$orderDetails = [];
require __DIR__ . '/_fixtures/orders.php';

$orderLoader = new OrderLoader($orderDetails);
$orderLoader->removeOrders();
