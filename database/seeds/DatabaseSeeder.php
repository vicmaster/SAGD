<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Model::unguard();

        $this->call(CodigoPostalTableSeeder::class);
        $this->call(EstadoApartadoTableSeeder::class);
        $this->call(EstadoEntradaTableSeeder::class);
        $this->call(EstadoSalidaTableSeeder::class);
        $this->call(EstadoTransferenciaTableSeeder::class);

        Model::reguard();
    }
}
