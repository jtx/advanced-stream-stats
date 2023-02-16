<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class BraintreeSubscriptions extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The details for both of these plans can be viewed in the object returned from $this->getPlans().
     * I haven't done thorough testing, but you have to define the plans in braintree ahead of time and
     * name them... whatever you want.  I've named them accord to what they do.  I'm curious what would
     * happen if I tried to delete a plan that people were subscribed to?  I'd hope it would error. That's
     * for another day though
     */
    public const PLAN_MONTHLY = 'monthly';
    public const PLAN_ANNUAL = 'yearly';

    protected $table = 'braintree_subscriptions';

    protected $fillable = [
        'braintree_customer_id',
        'braintree_payment_method_token',
        'subscription_id',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];


}
