<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserService extends Model
{
    use HasFactory;
    protected $table = 'user_service';
    protected $fillable = ['login', 'password', 'status'];

    public function tokens()
    {
        return $this->hasMany(TokenServices::class, 'user_id');
    }
}
