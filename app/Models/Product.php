<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'unit_type',
        'unit_price',
        'category_id',
    ];

    public function quotationLines()
    {
        return $this->hasMany(QuotationLine::class);
    }
}