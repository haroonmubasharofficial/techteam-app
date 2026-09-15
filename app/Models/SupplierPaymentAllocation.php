<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPaymentAllocation extends Model
{
    protected $fillable = ['supplier_payment_id','purchase_id','amount'];
    protected $casts = ['amount' => 'decimal:2'];

    public function payment(): BelongsTo { return $this->belongsTo(SupplierPayment::class, 'supplier_payment_id'); }
    public function purchase(): BelongsTo { return $this->belongsTo(Purchase::class); }
}
