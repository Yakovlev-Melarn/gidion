<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'password', 'token'];

    public function sellers()
    {
        return $this->hasMany(Seller::class, 'user_id', 'id');
    }
}
