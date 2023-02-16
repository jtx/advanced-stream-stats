<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BraintreeCardOnFile extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'braintree_cards_on_file';

    /**
     * @var string[]
     */
    protected $fillable = [
        'braintree_customer_id',
        'token',
        'masked_number',
        'expiration_date',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at', 'expirationDate',
    ];

    /**
     * @return BelongsTo
     */
    public function braintree_customer(): BelongsTo
    {
        return $this->belongsTo(BraintreeCustomer::class);
    }
}
