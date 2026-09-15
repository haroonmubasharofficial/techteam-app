<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerPayment extends Model
{
    protected $fillable = ['receipt_number','customer_id','payment_date','payment_method','reference','amount','status','notes'];
    protected $casts = ['payment_date' => 'date', 'amount' => 'decimal:2'];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function allocations(): HasMany { return $this->hasMany(CustomerPaymentAllocation::class); }
}
