<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable=[
        'username',
        'password'
    ];
    public function setPasswordAttribute($value)
    {
        $this->attributes['Password']=bcrypt($value);
    }
}
