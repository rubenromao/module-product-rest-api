<?php

use Rezolve\APISalesV4\Test\FixtureLoaders\OrderLoader;

$orderDetails = [];
require __DIR__ . '/_fixtures/completeOrders.php';
$orderLoader = new OrderLoader($orderDetails);
$orderLoader->createOrders();
