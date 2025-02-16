<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAccion extends Model {
    use HasFactory;

    protected $table = 'tipoAccion';
    protected $fillable = ['nombretipo', 'estado'];

    public function logUsuarios() {
        return $this->hasMany(LogUsuario::class, 'tipoAccionId');
    }
}

