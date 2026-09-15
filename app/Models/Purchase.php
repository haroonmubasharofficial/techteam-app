<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Purchase extends Model {
    protected $fillable=['purchase_number','supplier_id','warehouse_id','purchase_date','supplier_invoice_number','reference','status','currency','subtotal','tax_total','total_amount','notes'];
    protected $casts=['purchase_date'=>'date'];
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function items(): HasMany { return $this->hasMany(PurchaseItem::class)->orderBy('id'); }
}
