<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id', 'midtrans_order_id', 'snap_token', 'status'];

    // Relasi ke order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}