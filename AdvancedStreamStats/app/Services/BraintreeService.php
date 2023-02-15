<?php

namespace App\Services;

use Braintree\Configuration;
use Braintree\PaymentMethod;
use Braintree\Transaction;
use Braintree\ClientToken;
use Braintree\Customer;

class BraintreeService
{
    public function __construct()
    {
        Configuration::environment(config('braintree.environment'));
        Configuration::merchantId(config('braintree.merchant_id'));
        Configuration::publicKey(config('braintree.public_key'));
        Configuration::privateKey(config('braintree.private_key'));
    }
}
