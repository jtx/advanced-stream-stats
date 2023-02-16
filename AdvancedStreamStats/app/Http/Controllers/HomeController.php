<?php

namespace App\Http\Controllers;

use App\Models\BraintreeSubscriptions;
use App\Services\BraintreeService;
use Braintree\Subscription;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param BraintreeService $service
     * @return Response
     * @throws \Braintree\Exception\NotFound
     */
    public function index(BraintreeService $service): Response
    {
        $userData = $service->getCustomer(auth()->user());
        $subscriptions = data_get($userData, 'paymentMethods.*.subscriptions');
        $activeSubscriptions = [];
        foreach ($subscriptions as $subscription) {
            $activeSubscriptions = array_merge($activeSubscriptions, array_filter($subscription, fn($sub) => $sub->status == Subscription::ACTIVE));
        }

        $plans = data_get($activeSubscriptions, '*.planId');

        return Inertia::render('Dashboard', [
            'subscriptions' => $activeSubscriptions,
            'hasYearly' => in_array($plans, BraintreeSubscriptions::PLAN_ANNUAL),
        ]);
    }
}
