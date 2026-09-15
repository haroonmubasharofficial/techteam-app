<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierPayment extends Model
{
    protected $fillable = ['payment_number','supplier_id','payment_date','payment_method','reference','amount','status','notes'];
    protected $casts = ['payment_date' => 'date', 'amount' => 'decimal:2'];

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function allocations(): HasMany { return $this->hasMany(SupplierPaymentAllocation::class); }
}
