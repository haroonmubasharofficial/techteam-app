<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Customer extends Model
{
    protected $fillable = ['company_name','contact_person','address','city','phone','mobile','email','ntn','strn','payment_terms','credit_limit','tax_treatment','is_active','notes'];
}
