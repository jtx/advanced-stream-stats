<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BraintreeCustomerPaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'braintree_customer_payment_methods';

    /**
     * @var string[]
     */
    protected $fillable = [
        'braintree_customer_id',
        'braintree_payment_method_token',
        'payment_method_meta',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'payment_method_meta' => 'array'
    ];

    /**
     * @return BelongsTo
     */
    public function braintree_customer(): BelongsTo
    {
        return $this->belongsTo(BraintreeCustomer::class);
    }
}
