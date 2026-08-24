<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'purchase_price',
        'selling_price',
        'total'
    ];

    /**
     * Get the sale that owns this item.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the product associated with this item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
