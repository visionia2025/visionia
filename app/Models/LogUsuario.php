<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogUsuario extends Model {
    use HasFactory;

    protected $table = 'logUsuarios';
    protected $fillable = ['userId', 'fecha', 'datosAnteriores', 'datosNuevos', 'tipoAccionId'];

    public function usuario() {
        return $this->belongsTo(User::class, 'userId');
    }

    public function tipoAccion() {
        return $this->belongsTo(TipoAccion::class, 'tipoAccionId');
    }
}

