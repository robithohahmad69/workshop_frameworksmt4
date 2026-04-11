<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_name', 'vendor_id', 'total', 'status_bayar', 'status'];

    // Relasi ke vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Relasi ke items dalam order ini
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relasi ke payment
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}