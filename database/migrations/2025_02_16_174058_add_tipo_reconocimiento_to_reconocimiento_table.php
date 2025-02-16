<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoReconocimientoToReconocimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reconocimiento', function (Blueprint $table) {
            $table->foreignId('tipoReconocimientoId')->constrained('tiporeconocimiento')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reconocimiento', function (Blueprint $table) {
            //
        });
    }
}
