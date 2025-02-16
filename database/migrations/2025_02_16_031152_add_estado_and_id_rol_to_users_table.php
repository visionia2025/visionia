<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoAndIdRolToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->char('estado', 1)->default('1')->after('password');
            $table->unsignedBigInteger('idRol')->after('estado');

            // Definir la clave foránea para idRol
            $table->foreign('idRol')->references('id')->on('roles')->onDelete('cascade');
       
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['idRol']);
            $table->dropColumn(['estado', 'idRol']);
        });
    }
}
