<?php

namespace App;

/**
 * Definicion de estados:
 * 1 => Abierta.                    Puede editarse y cargarse
 * 2 => Cargando Local.             En proceso de carga, no puede editarse
 * 3 => Cargada Local.              La carga se realizo con exito, no puede editarse
 * 4 => Iniciando Transferencia.
 * 5 => Transferencia Terminada.    Lista Para Cargarse Sucursal Destino
 * 6 => Cargando Otra Sucursal.     En proceso de carga en otra sucursal
 * 7 => Cargada Otra Sucursal.      Indica que se realizo la carga exitosamente
 */

class EstadoTransferencia extends LGGModel
{
    //
    protected $table = "estados_transferencias";
    public $timestamps = false;
    protected $fillable = ['nombre'];

    public static $rules = [
        'nombre' => 'required|max:45',
    ];
    public $updateRules = [];

    /**
     * Define the model hooks
     * @codeCoverageIgnore
     */
    public static function boot(){
        EstadoTransferencia::creating(function($model){
            return $model->isValid();
        });
        EstadoTransferencia::updating(function($model){
            $model->updateRules = self::$rules;
            return $model->isValid('update');
        });
    }


    /**
    * Obtiene las Transferencias asociadas con el Estado
    * @return Illuminate\Database\Eloquent\Collection
    */
    public function transferencias()
    {
        return $this->hasMany('App\Transferencia', 'estado_transferencia_id');
    }
}
