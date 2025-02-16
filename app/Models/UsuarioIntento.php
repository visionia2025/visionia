<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioIntento extends Model {
    use HasFactory;

    protected $table = 'usuarioIntentos';
    protected $fillable = ['fecha', 'userId', 'ip'];

    public function usuario() {
        return $this->belongsTo(User::class, 'userId');
    }
}

