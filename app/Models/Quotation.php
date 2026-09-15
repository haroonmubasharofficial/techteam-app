<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quotation extends Model
{
    protected $fillable = ['quotation_number','customer_id','quote_date','valid_until','reference','project_name','summary','status','currency','subtotal','discount_total','tax_total','total_amount','estimated_cost_total','estimated_profit','estimated_margin_percent','terms'];
    protected $casts = ['quote_date'=>'date','valid_until'=>'date'];
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function items(): HasMany { return $this->hasMany(QuotationItem::class)->orderBy('line_no'); }
    public function invoice(): HasOne { return $this->hasOne(Invoice::class); }
}
