<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddConstraintsToFichasCaracteristicasTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('fichas_caracteristicas', function (Blueprint $table) {
            $table->unique(['producto_id', 'icecat_feature_id', 'icecat_feature_group_id'], 'composite_unique');
            $table->foreign('producto_id')->references('producto_id')->on('fichas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('fichas_caracteristicas', function (Blueprint $table) {
            $table->dropForeign('fichas_caracteristicas_producto_id_foreign');
            $table->unique('composite_unique');
        });
    }
}
