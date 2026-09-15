<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class QuotationItem extends Model
{
    protected $fillable = ['quotation_id','product_id','line_no','description','quantity','unit','purchase_cost','delivery_cost','other_cost','selling_price','discount','tax_rate','tax_amount','estimated_cost_total','estimated_profit','estimated_margin_percent'];
}
