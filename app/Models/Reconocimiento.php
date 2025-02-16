<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reconocimiento extends Model {
    use HasFactory;

    protected $table = 'reconocimiento';
    protected $fillable = ['fecha', 'resultado', 'userId', 'tipoReconocimientoId'];

    public function usuario() {
        return $this->belongsTo(User::class, 'userId');
    }

    public function tipoReconocimiento() {
        return $this->belongsTo(TipoReconocimiento::class, 'tipoReconocimientoId');
    }
}

