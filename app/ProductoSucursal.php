<?php

namespace App;


/**
 * App\ProductoSucursal
 *
 * @property integer $id
 * @property integer $producto_id
 * @property integer $sucursal_id
 * @property-read \App\Existencia $existencia
 * @property-read \App\Sucursal $sucursal
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Precio[] $precios
 * @property-read \App\Producto $producto
 * @method static \Illuminate\Database\Query\Builder|\App\ProductoSucursal whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductoSucursal whereProductoId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProductoSucursal whereSucursalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LGGModel last()
 * @property \Carbon\Carbon $deleted_at
 * @property-read \App\Precio $precio
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductoMovimiento[] $movimientos
 * @method static \Illuminate\Database\Query\Builder|\App\ProductoSucursal whereDeletedAt($value)
 */
class ProductoSucursal extends LGGModel {


    protected $table = "productos_sucursales";
    public $timestamps = false;
    protected $fillable = ['producto_id', 'sucursal_id'];

    public static $rules = [];

    /**
     * Define the model hooks
     * @codeCoverageIgnore
     */
    public static function boot() {
        parent::boot();
        ProductoSucursal::creating(function ($ps) {
            if (!$ps->isValid()) {
                return false;
            }

            return true;
        });
    }

    /**
     * Obtiene la Existencia asociado al producto sucursal
     * @return \App\Existencia
     */
    public function existencia() {
        return $this->hasOne('App\Existencia', 'productos_sucursales_id');
    }

    /**
     * Obtiene la Sucursal asociada al producto sucursal
     * @return \App\Sucursal
     */
    public function sucursal() {
        return $this->belongsTo('App\Sucursal');
    }


    /**
     * Obtiene los precios asociados
     * @return \App\Precio
     */
    public function precio() {
        return $this->hasOne('App\Precio', 'producto_sucursal_id');
    }

    /**
     * Obtiene el producto asociado
     * @return \App\Producto
     */
    public function producto() {
        return $this->belongsTo('App\Producto', 'producto_id');
    }

    /**
     * Obtiene los Productos Movimientos asociados
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function movimientos() {
        return $this->hasMany('App\ProductoMovimiento', 'producto_sucursal_id');
    }
}
