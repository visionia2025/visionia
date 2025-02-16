<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{


    protected $table = 'password_resets'; // Especifica el nombre de la tabla
    protected $fillable = ['email', 'token', 'expires_at', 'created_at'];

    public $timestamps = false; // Evita problemas con Laravel manejando timestamps
    public $incrementing = false; // Evita que Laravel busque un campo 'id'
    protected $primaryKey = 'email'; // Define 'email' como la clave primaria
    protected $keyType = 'string'; // Indica que la clave primaria es un string

}
