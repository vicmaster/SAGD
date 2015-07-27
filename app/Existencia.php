<?php

namespace App;

class Existencia extends LGGModel
{
    //
    protected $table = "existencias";
    public $timestamps = false;
    protected $fillable = ['cantidad', 'cantidad_apartado',
        'cantidad_pretransferencia', 'cantidad_transferencia',
        'cantidad_garantia_cliente', 'cantidad_garantia_zegucom'];

    public static $rules = [
        'cantidad' =>  'integer|min:0',
        'cantidad_apartado' =>  'integer|min:0',
        'cantidad_pretransferencia' =>  'integer|min:0',
        'cantidad_transferencia' =>  'integer|min:0',
        'cantidad_garantia_cliente' =>  'integer|min:0',
        'cantidad_garantia_zegucom' =>  'integer|min:0',
    ];

    /**
     * Define the model hooks
     * @codeCoverageIgnore
     */
    public static function boot(){
        Existencia::creating(function($e){
            $e->cantidad || $e->cantidad = 0;
            $e->cantidad_apartado || $e->cantidad_apartado = 0;
            $e->cantidad_pretransferencia || $e->cantidad_pretransferencia = 0;
            $e->cantidad_transferencia || $e->cantidad_transferencia = 0;
            $e->cantidad_garantia_cliente || $e->cantidad_garantia_cliente = 0;
            $e->cantidad_garantia_zegucom || $e->cantidad_garantia_zegucom = 0;
            if ( !$e->isValid() ){
                return false;
            }
            return true;
        });
    }
}
