<?php

namespace App;

class Salida extends LGGModel
{
    //
    protected $table = "salidas";
    public $timestamps = false;
    protected $fillable = ['fecha_salida', 'motivo',
        'empleado_id', 'sucursal_id', 'estado_salida_id'];

    public static $rules = [
        'fecha_salida' => 'date',
        'motivo' => 'required|max:255',
        'empleado_id' => 'required|integer',
        'sucursal_id' => 'required|integer',
        'estado_salida_id' => 'required|integer',
    ];
    public $updateRules = [];

    /**
     * Define the model hooks
     * @codeCoverageIgnore
     */
    public static function boot(){
        Salida::creating(function($model){
            return $model->isValid();
        });
        Salida::updating(function($model){
            $model->updateRules = self::$rules;
            return $model->isValid('update');
        });
    }


    /**
    * Obtiene el Empleado asociado con la Salida
    * @return App\Empleado
    */
    public function empleado()
    {
        return $this->belongsTo('App\Empleado', 'empleado_id');
    }


    /**
    * Obtiene la Sucursal asociada con la Salida
    * @return App\Sucursal
    */
    public function sucursal()
    {
        return $this->belongsTo('App\Sucursal', 'sucursal_id');
    }


    /**
    * Obtiene el Estado asociado con la Salida
    * @return App\EstadoSalida
    */
    public function estado()
    {
        return $this->belongsTo('App\EstadoSalida', 'estado_salida_id');
    }


    /**
    * Obtiene las Salidas Detalles asociadas con la Salida
    * @return Illuminate\Database\Eloquent\Collection
    */
    public function detalles()
    {
        return $this->hasMany('App\SalidaDetalle', 'salida_id');
    }
}