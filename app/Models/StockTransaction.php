<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class StockTransaction extends Model {
    protected $fillable=['warehouse_id','product_id','purchase_item_id','invoice_item_id','transaction_type','transaction_date','quantity_in','quantity_out','unit_cost','reference','notes'];
    protected $casts=['transaction_date'=>'datetime'];
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function purchaseItem(): BelongsTo { return $this->belongsTo(PurchaseItem::class); }
}
