<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = ['product_category_id','unit_id','sku','name','description','item_type','brand','model','warranty','purchase_cost','default_selling_price','tax_rate','is_active','track_serial'];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
