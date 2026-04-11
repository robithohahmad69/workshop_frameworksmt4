<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Vendor extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password'];
    public function menus()
{
    return $this->hasMany(Menu::class);
}
}