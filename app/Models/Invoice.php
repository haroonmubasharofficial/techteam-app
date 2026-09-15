<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number','customer_id','quotation_id','invoice_date','reference','summary','status','currency',
        'subtotal','discount_total','tax_total','total_amount','actual_cost_total','actual_profit','actual_margin_percent',
        'fbr_status','fbr_invoice_number','fbr_uuid','fbr_submission_date','fbr_response','terms',
    ];

    protected $casts = ['invoice_date' => 'date', 'fbr_submission_date' => 'datetime', 'fbr_response' => 'array'];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function quotation(): BelongsTo { return $this->belongsTo(Quotation::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class)->orderBy('line_no'); }
    public function paymentAllocations(): HasMany { return $this->hasMany(CustomerPaymentAllocation::class); }
}
