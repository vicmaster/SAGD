<?php

use Illuminate\Database\Migrations\Migration;

class AddDatosContactosConstraints extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //
        Schema::table('datos_contactos', function ($table) {
            $table->foreign('empleado_id')->references('id')->on('empleados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
        Schema::table('datos_contactos', function ($table) {
            $table->dropForeign('datos_contactos_empleado_id_foreign');
        });
    }
}
