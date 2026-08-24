<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'user_id',
        'subtotal',
        'tax',
        'discount',
        'grand_total',
        'cash_received',
        'change_returned',
        'status'
    ];

    /**
     * Get the user (cashier) who created the sale.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in this sale.
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
