<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoReconocimiento extends Model {
    use HasFactory;

    protected $table = 'tiporeconocimiento'; // Nombre de la tabla
    protected $fillable = ['nombretipo', 'estado'];

    public function reconocimientos() {
        return $this->hasMany(Reconocimiento::class, 'tipoReconocimientoId');
    }
}

