<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InicioSesion extends Model {
    use HasFactory;

    protected $table = 'inicio_sesion';
    protected $fillable = ['fecha', 'userId', 'ip'];

    public function usuario() {
        return $this->belongsTo(User::class, 'userId');
    }
}

