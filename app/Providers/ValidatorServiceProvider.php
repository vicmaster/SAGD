<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Validator;

class ValidatorServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        /*
         * greater_than:field
         */
        Validator::extend('greater_than', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            return $value > $data[$parameters[0]];
        });
        Validator::replacer('greater_than', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });

        /*
         *
         */
        Validator::extend('less_than', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            return $value < $data[$parameters[0]];
        });
        Validator::replacer('less_than', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });

        /**
         * Verifica que el campo ($value) sea igual a la multiplicacion de los dos parametros
         */
        Validator::extend('mult', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            return $value === $data[$parameters[0]] * $data[$parameters[1]];
        });
        Validator::replacer('mult', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':field1', ':field2'], [$parameters[0], $parameters[1]], $message);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }
}
