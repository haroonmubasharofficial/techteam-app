<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class StockAdjustment extends Model {
    protected $fillable=['adjustment_number','warehouse_id','adjustment_date','reason','status','notes'];
    protected $casts=['adjustment_date'=>'date'];
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function items(): HasMany { return $this->hasMany(StockAdjustmentItem::class); }
}
