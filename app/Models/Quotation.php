<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Quotation extends Model
{
    protected $fillable = ['quotation_number','customer_id','quote_date','valid_until','reference','project_name','summary','status','currency','subtotal','discount_total','tax_total','total_amount','estimated_cost_total','estimated_profit','estimated_margin_percent','terms'];
    public function items(): HasMany { return $this->hasMany(QuotationItem::class); }
}
