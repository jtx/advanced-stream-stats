<?php

namespace App\Events;

class PurchaseEvent
{
    /**
     * @param \JsonSerializable $purchase
     */
    public function __construct(public \JsonSerializable $purchase) {}
}
