<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Unidad extends LGGModel
{
    //
    protected $table = "unidades";
    public $timestamps = false;
    protected $fillable = ['clave', 'nombre'];

    public static $rules = [
        'clave' => ['required','max:4','alpha','regex:/[A-Z]{4}/', 'unique:unidades'],
        'nombre' => 'required|max:45'
    ];

    public $updateRules = [];

    /**
     * Define the model hooks
     * @codeCoverageIgnore
     */
    public static function boot() {
        Unidad::creating(function($unidad){
            $unidad->clave = strtoupper($unidad->clave);
            if ( !$unidad->isValid() ){
                return false;
            }
            return true;
        });
        Unidad::updating(function($unidad){
            $unidad->updateRules = self::$rules;
            $unidad->updateRules['clave'] = [
                'required','max:4','alpha',
                'regex:/[A-Z]{4}/', 'unique:unidades,clave,'.$unidad->id];
            return $unidad->isValid('update');
        });
    }

    /**
     * Get the associated productos with unidad
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function productos()
    {
        return $this->hasMany('App\Producto');
    }
}