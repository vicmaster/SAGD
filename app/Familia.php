<?php

namespace App;

class Familia extends LGGModel
{
    //
    protected $table = "familias";
    public $timestamps = false;
    protected $fillable = ['clave', 'nombre', 'descripcion'];

    public static $rules = [
        'clave' => 'required|max:4|unique:familias',
        'nombre' => 'required|max:45',
        'descripcion' => 'max:100'
    ];

    public $updateRules = [];

    /**
     * Define the model hooks
     * @codeCoverageIgnore
     */
    public static function boot() {
        Familia::creating(function($familia){
            $familia->clave = strtoupper($familia->clave);
            if ( !$familia->isValid() ){
                return false;
            }
            return true;
        });
        Familia::updating(function($familia){
            $familia->updateRules = self::$rules;
            $familia->updateRules['clave'] .= ',clave,'.$familia->id;
            return $familia->isValid('update');
        });
    }

    public function subfamilias()
    {
        return $this->hasMany('App\Subfamilia');
    }
}