<?php

use Illuminate\Database\Migrations\Migration;

class CreatePaginasWebDistribuidoresTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        //
        Schema::create('paginas_web_distribuidores', function ($table) {
            $table->increments('id');
            $table->boolean('activo')->default(false);
            $table->timestamp('fecha_vencimiento');
            $table->string('url', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
        Schema::drop('paginas_web_distribuidores');
    }
}
