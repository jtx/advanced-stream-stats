<?php

namespace App\Services;

use App\Models\BraintreeCardOnFile;
use App\Models\BraintreeCustomer;
use App\Models\User;
use Braintree\Exception;
use Braintree\Gateway;
use Braintree\Plan;
use Braintree\Result\Error;
use Braintree\Result\Successful;
use Braintree\Customer;
use Braintree\Subscription;
use JsonSerializable;

class BraintreeService
{
    /**
     * @var Gateway
     */
    public Gateway $gateway;

    public function __construct()
    {
        $this->gateway = new Gateway([
            'environment' => config('braintree.environment'),
            'merchantId' => config('braintree.merchant_id'),
            'publicKey' => config('braintree.public_key'),
            'privateKey' => config('braintree.private_key'),
        ]);
    }

    /**
     * @param User $user
     * @return Customer
     * @throws Exception
     */
    public function createCustomer(User $user): Customer
    {
        $result = $this->gateway->customer()->create(
            [
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'email' => $user->email,
            ]
        );

        if ($result instanceof Successful) {
            return $result->customer;
        }

        throw new Exception(data_get($result, 'errors'));
    }

    /**
     * @param User $user
     * @return Customer
     * @throws Exception\NotFound
     */
    public function getCustomer(User $user): Customer
    {
        return $this->gateway->customer()->find($user->braintree_customer->braintree_customer_id);
    }

    /**
     * @param User $user
     * @return array
     * @throws Exception\NotFound
     */
    public function getActiveSubscriptions(User $user): array
    {
        $userData = $this->getCustomer($user);
        $subscriptions = data_get($userData, 'paymentMethods.*.subscriptions');
        $activeSubscriptions = [];
        foreach ($subscriptions as $subscription) {
            $activeSubscriptions = array_merge($activeSubscriptions, array_filter($subscription, fn($sub) => $sub->status == Subscription::ACTIVE));
        }

        return $activeSubscriptions;
    }

    /**
     * Create a new payment method
     *
     * @param User $user
     * @param string $cardNumber
     * @param \DateTime $expirationDate
     * @param int $cvv
     * @return mixed
     * @throws Exception
     */
    public function createPaymentMethod(User $user, string $cardNumber, \DateTime $expirationDate, int $cvv): BraintreeCardOnFile
    {
        $customer = $this->resolveCustomer($user);

        $result = $this->gateway->creditCard()->create([
            'customerId' => $customer->braintree_customer_id,
            'number' => $cardNumber,
            'expirationDate' => $expirationDate->format('m/y'),
            'cvv' => $cvv,
        ]);

        if ($result instanceof Error) {
            throw new Exception($result->message);
        }

        return BraintreeCardOnFile::create([
            'braintree_customer_id' => $customer->id,
            'token' => data_get($result, 'creditCard.token'),
            'masked_number' => data_get($result, 'creditCard.maskedNumber'),
            'expiration_date' => $expirationDate,
        ]);
    }

    /**
     * List the subscriptions plans available.  We should have one with the id 'monthly' and one with an id of 'yearly'... At least that's how I set it up
     * in the sandbox.  I don't see a way to do this elegantly, I feel like with this API we sort of have to hard code this, thus the constants up top.
     *
     * @return Plan[]
     */
    public function getPlans(): array
    {
        return \Cache::remember('braintree_subscription_plans', 60 * 60 * 24, function () {
            return $this->gateway->plan()->all();
        });
    }

    /**
     * @param string $planId
     * @param string $paymentToken
     * @return JsonSerializable
     * @throws Exception
     */
    public function createSubscription(string $planId, string $paymentToken): JsonSerializable
    {
        $result = $this->gateway->subscription()->create([
            'paymentMethodToken' => $paymentToken,
            'planId' => $planId,
        ]);

        if ($result instanceof Error) {
            throw new Exception($result->message);
        }

        return $result;
    }

    /**
     * @param User $user
     * @return string
     * @throws Exception\NotFound
     */
    public function getClientToken(User $user): string
    {
        $braintreeCustomerId = data_get($user, 'braintree_customer.braintree_customer_id');
        if ($braintreeCustomerId === null) {
            throw new Exception\NotFound('User does not appear to have a braintree customer id');
        }

        return \Cache::remember("braintree_client_token_{$braintreeCustomerId}", 60 * 60 * 24, function () use ($user) {
            return $this->gateway->clientToken()->generate([
                "customerId" => data_get($user, 'braintree_customer.braintree_customer_id'),
            ]);
        });
    }

    /**
     * In case the event listener didn't make a braintree user for some reason
     * @param User $user
     * @return BraintreeCustomer
     * @throws Exception
     */
    protected function resolveCustomer(User $user): BraintreeCustomer
    {
        if ($user->braintree_customer instanceof BraintreeCustomer) {
            return $user->braintree_customer;
        }

        $result = $this->createCustomer($user);

        return BraintreeCustomer::create([
            'user_id' => $user->id,
            'braintree_customer_id' => $result->id,
        ]);
    }

}
