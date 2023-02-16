<?php

namespace App\Services;

use App\Models\User;
use Braintree\Configuration;
use Braintree\Exception;
use Braintree\Gateway;
use Braintree\PaymentMethod;
use Braintree\Plan;
use Braintree\Result\Successful;
use Braintree\Transaction;
use Braintree\ClientToken;
use Braintree\Customer;

class BraintreeService
{
    /**
     * @var Gateway
     */
    public Gateway $gateway;

    /**
     * The details for both of these plans can be viewed in the object returned from $this->getPlans().
     * I haven't done thorough testing, but you have to define the plans in braintree ahead of time and
     * name them... whatever you want.  I've named them accord to what they do.  I'm curious what would
     * happen if I tried to delete a plan that people were subscribed to?  I'd hope it would error. That's
     * for another day though
     */
    public const PLAN_MONTHLY = 'monthly';
    public const PLAN_ANNUAL = 'yearly';

    public function __construct()
    {
        Configuration::environment(config('braintree.environment'));
        Configuration::merchantId(config('braintree.merchant_id'));
        Configuration::publicKey(config('braintree.public_key'));
        Configuration::privateKey(config('braintree.private_key'));

        $this->gateway = Configuration::gateway();
    }


    public function createCustomer(User $user): Customer
    {
        $result = $this->gateway->customer()->create(
            [
                'firstName' => 'John',
                'lastName' => 'Smith',
                'company' => 'Smith Co.',
                'email' => 'john@smith.com',
                'website' => 'www.smithco.com',
                'fax' => '419-555-1234',
                'phone' => '614-555-1234',
            ]
        );

        if ($result instanceof Successful) {
            return $result->customer;
        }

        throw new Exception(object_get($result, 'errors'));
    }

    /**
     * List the subscriptions plans available
     *
     * @return Plan[]
     */
    public function getPlans(): array
    {
        return \Cache::remember('braintree_subscription_plans', 60 * 60 * 24, function () {
            return $this->gateway->plan()->all();
        });
    }

}
