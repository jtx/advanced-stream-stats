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
        $activeSubscriptions = $service->getActiveSubscriptions(request()->user());

        // I don't feel like installing moment, so just do it here
        foreach ($activeSubscriptions as $key => $subscription) {
            $activeSubscriptions[$key]->_set('nextBillingDate', $subscription->nextBillingDate->format('m/d/Y'));
        }

        $plans = data_get($activeSubscriptions, '*.planId');

        return Inertia::render('Dashboard', [
            'subscriptions' => $activeSubscriptions,
            'hasYearly' => in_array(BraintreeSubscriptions::PLAN_ANNUAL, $plans),
            'plans' => $service->getPlans(),
        ]);
    }
}
