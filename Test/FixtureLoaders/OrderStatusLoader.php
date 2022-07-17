<?php

namespace Rezolve\APISalesV4\Test\FixtureLoaders;

use Magento\Sales\Model\Order\Status;

class OrderStatusLoader extends AbstractLoader
{
    private $orderStatuses;

    public function __construct($orderStatuses)
    {
        $this->orderStatuses = $orderStatuses;
    }

    public function createOrderStatuses()
    {
        foreach ($this->orderStatuses as $orderStatus) {
            /** @var Status $status */
            $status = $this->createObject(Status::class)->load($orderStatus['status']);
            $status->setData($orderStatus)->setStatus($orderStatus['status']);
            $status->save();

            $status->assignState($orderStatus['state'], $orderStatus['is_default'], $orderStatus['visible_on_front']);
        }
    }
}
