<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id','quotation_item_id','product_id','line_no','description','quantity','unit',
        'actual_cost_unit','selling_price_unit','discount','tax_rate','tax_amount','cost_total','actual_profit','actual_margin_percent',
    ];

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function quotationItem(): BelongsTo { return $this->belongsTo(QuotationItem::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
