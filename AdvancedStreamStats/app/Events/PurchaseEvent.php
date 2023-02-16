<?php

namespace App\Events;

class PurchaseEvent
{
    /**
     * @param \JsonSerializable $purchase
     */
    public function __construct(protected \JsonSerializable $purchase) {}
}
