<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BraintreeCustomer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'braintree_customers';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'braintree_customer_id',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function payment_methods(): HasMany
    {
        return $this->hasMany(BraintreeCustomerPaymentMethod::class);
    }

    /**
     * @return HasMany
     */
    public function braintree_card_on_file(): HasMany
    {
        return $this->hasMany(BraintreeCardOnFile::class, 'braintree_customer_id');
    }
}
