<?php

namespace App\Http\Controllers;
use App\Services\BraintreeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
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
     * @param Request $request
     * @return JsonResponse
     * @throws \Braintree\Exception
     * @throws \Braintree\Exception\NotFound
     */
    public function purchase(BraintreeService $service, Request $request): JsonResponse
    {
        $request->validate([
            'subscription' => 'required',
            'card_number' => 'required',
            'expiration_date' => 'required',
            'cvv' => 'required',
        ]);

        $user = $request->user();

        // Normally I'd have an option to choose a card already on file, put I don't have time for that really right now
        // So... Just make another card on file even if it already exists.
        $paymentMethod = $service->createPaymentMethod($user, $request->get('card_number'), $request->get('expiration_date'), $request->get('cvv'));

        $res = $service->createSubscription($request->get('subscription'), $paymentMethod->token);

        // dispatch event

        $activeSubscriptions = $service->getActiveSubscriptions($user);

        // I don't feel like installing moment, so just do it here
        // Yes, this is duplicated code.  It shouldn't be here to begin with
        foreach ($activeSubscriptions as $key => $subscription) {
            $activeSubscriptions[$key]->_set('nextBillingDate', $subscription->nextBillingDate->format('m/d/Y'));
        }

        return response()->json($service->getActiveSubscriptions($user));
    }
}
