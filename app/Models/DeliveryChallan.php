<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryChallan extends Model
{
    protected $fillable = ['challan_number','customer_id','invoice_id','challan_date','delivery_date','shipped_date','delivery_challan_for','shipping_to','status','terms'];
    protected $casts = ['challan_date'=>'date','delivery_date'=>'date','shipped_date'=>'date'];
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function items(): HasMany { return $this->hasMany(DeliveryChallanItem::class)->orderBy('line_no'); }
}
