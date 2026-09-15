<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Supplier extends Model {
    protected $fillable=['company_name','contact_person','address','phone','email','ntn','strn','payment_terms','is_active','notes'];
    public function purchases(): HasMany { return $this->hasMany(Purchase::class); }
}
