<?php

namespace App;


/**
 * App\Subfamilia
 *
 * @property integer $id
 * @property string $clave
 * @property string $nombre
 * @property integer $familia_id
 * @property integer $margen_id
 * @property-read \App\Familia $familia
 * @property-read \App\Margen $margen
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Producto[] $productos
 * @method static \Illuminate\Database\Query\Builder|\App\Subfamilia whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subfamilia whereClave($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subfamilia whereNombre($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subfamilia whereFamiliaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subfamilia whereMargenId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LGGModel last()
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\Subfamilia whereDeletedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\IcecatCategory[] $icecatCategories
 */
class Subfamilia extends LGGModel {

    protected $table = "subfamilias";
    public $timestamps = false;
    protected $fillable = ['clave', 'nombre', 'familia_id', 'margen_id'];

    public static $rules = [
        'clave'      => 'required|string|max:4|alpha|unique:subfamilias',
        'nombre'     => 'required|string|max:45',
        'familia_id' => 'required|integer',
        'margen_id'  => 'integer'
    ];

    public $updateRules = [];

    /**
     * Define the model hooks
     * @codeCoverageIgnore
     */
    public static function boot() {
        parent::boot();
        Subfamilia::creating(function ($subfamilia) {
            $subfamilia->clave = strtoupper($subfamilia->clave);
            if (!$subfamilia->isValid()) {
                return false;
            }

            return true;
        });
        Subfamilia::updating(function ($subfamilia) {
            $subfamilia->updateRules = self::$rules;
            $subfamilia->updateRules['clave'] .= ',clave,' . $subfamilia->id;

            return $subfamilia->isValid('update');
        });
    }

    /**
     * Obtiene el Familia asociada con Subfamilia
     * @return App\Familia
     */
    public function familia() {
        return $this->belongsTo('App\Familia');
    }

    /**
     * Obtiene el Margen asociado con Subfamilia
     * @return App\Margen
     */
    public function margen() {
        return $this->belongsTo('App\Margen');
    }

    /**
     * Obtiene los Productos asociados con Subfamilia
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function productos() {
        return $this->hasMany('App\Producto');
    }

    /**
     * Obtiene las categorias de Icecat que pertencen a la subfamilia
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function icecatCategories(){
        return $this->hasMany('App\IcecatCategory');
    }
}
