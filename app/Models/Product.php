<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model
{
    protected $fillable = ['product_category_id','unit_id','sku','name','description','item_type','brand','model','warranty','purchase_cost','default_selling_price','tax_rate','is_active','track_serial'];
}
