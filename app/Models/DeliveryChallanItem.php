<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryChallanItem extends Model
{
    protected $fillable = ['delivery_challan_id','invoice_item_id','product_id','line_no','item_name','serial_number','quantity','unit','received_by','received_comment','received_date','delivered_by','delivered_comment','delivered_date'];
    protected $casts = ['quantity'=>'decimal:4','received_date'=>'date','delivered_date'=>'date'];
    public function challan(): BelongsTo { return $this->belongsTo(DeliveryChallan::class,'delivery_challan_id'); }
    public function invoiceItem(): BelongsTo { return $this->belongsTo(InvoiceItem::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
