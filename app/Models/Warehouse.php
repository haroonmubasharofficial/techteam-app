<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Warehouse extends Model {
    protected $fillable=['name','address','is_active'];
    protected $casts=['is_active'=>'boolean'];
    public function purchases(): HasMany { return $this->hasMany(Purchase::class); }
    public function stockTransactions(): HasMany { return $this->hasMany(StockTransaction::class); }
}
