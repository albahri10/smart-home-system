<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'quotation_date',
        'notes',
        'total_amount',
        'discount_amount',
        'tax_amount',
        'grand_total',
    ];

    public function lines()
    {
        return $this->hasMany(QuotationLine::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}