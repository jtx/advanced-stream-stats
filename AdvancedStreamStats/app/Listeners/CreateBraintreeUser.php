<?php

namespace App\Listeners;

use App\Models\BraintreeCustomer;
use App\Services\BraintreeService;
use Illuminate\Auth\Events\Registered;

class CreateBraintreeUser
{
    /**
     * @param BraintreeService $service
     */
    public function __construct(protected BraintreeService $service) {}

    /**
     * @param Registered $event
     * @return void
     */
    public function handle(Registered $event)
    {
        try {
            $result = $this->service->createCustomer($event->user);

            BraintreeCustomer::create([
                'user_id' => $event->user->id,
                'braintree_customer_id' => $result->id,
            ]);
        } catch (\Throwable $e) {
            // Well, something went wrong; I'd probably log it if this weren't just a demo.
        }
    }
}
