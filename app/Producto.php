<?php

namespace App;


use App\Events\Pretransferir;
use App\Events\ProductoActualizado;
use App\Events\ProductoCreado;
use DB;
use Event;
use Illuminate\Support\MessageBag;
use Sagd\SafeTransactions;

/**
 * App\Producto
 *
 * @property integer $id
 * @property boolean $activo
 * @property string $clave
 * @property string $descripcion
 * @property string $descripcion_corta
 * @property string $fecha_entrada
 * @property string $numero_parte
 * @property boolean $remate
 * @property float $spiff
 * @property string $subclave
 * @property string $upc
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $tipo_garantia_id
 * @property integer $marca_id
 * @property integer $margen_id
 * @property integer $unidad_id
 * @property integer $subfamilia_id
 * @property-read \App\TipoGarantia $tipoGarantia
 * @property-read \App\Marca $marca
 * @property-read \App\Margen $margen
 * @property-read \App\Unidad $unidad
 * @property-read \App\Subfamilia $subfamilia
 * @property-read Dimension $dimension
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductoMovimiento[] $productoMovimientos
 * @property-read \Illuminate\Database\Eloquent\Collection|ProductoSucursal[] $productosSucursales
 * @property-read \Illuminate\Database\Eloquent\Collection|Sucursal[] $sucursales
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Proveedor[] $proveedores
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\EntradaDetalle[] $entradasDetalles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SalidaDetalle[] $salidasDetalles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TransferenciaDetalle[] $transferenciasDetalles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ApartadoDetalle[] $apartadosDetalles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Reposicion[] $reposiciones
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereActivo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereClave($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereDescripcion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereDescripcionCorta($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereFechaEntrada($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereNumeroParte($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereRemate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereSpiff($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereSubclave($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereUpc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereTipoGarantiaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereMarcaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereMargenId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereUnidadId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereSubfamiliaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LGGModel last()
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\Producto whereDeletedAt($value)
 * @property-read \App\Ficha $ficha
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Pretransferencia[] $pretransferencias
 */
class Producto extends LGGModel {

    use SafeTransactions;

    protected $table = "productos";
    public $timestamps = true;
    protected $fillable = ['activo', 'clave', 'descripcion', 'descripcion_corta',
        'fecha_entrada', 'numero_parte', 'remate', 'spiff', 'subclave', 'upc',
        'tipo_garantia_id', 'marca_id', 'margen_id', 'unidad_id', 'subfamilia_id'];

    public static $rules = [
        'activo'            => 'required|boolean',
        'clave'             => 'required|max:60|unique:productos',
        'descripcion'       => 'required|max:300',
        'descripcion_corta' => 'max:50',
        'fecha_entrada'     => 'date',
        'numero_parte'      => ['required', 'max:30', 'regex:`^([\w\-_#\.\(\)\/\+]+\s?)+$`'],
        'remate'            => 'required|boolean',
        'spiff'             => 'required|numeric|min:0.0',
        'subclave'          => 'required|string|max:45',
        'upc'               => 'string|max:20|unique:productos',
        'tipo_garantia_id'  => 'required|integer',
        'marca_id'          => 'required|integer',
        'margen_id'         => 'integer',
        'unidad_id'         => 'required|integer',
        'subfamilia_id'     => 'required|integer',
    ];

    public $updateRules = [];

    /**
     * Define the model hooks
     * @codeCoverageIgnore
     */
    public static function boot() {
        parent::boot();
        Producto::creating(function ($producto) {
            $producto->subclave || $producto->subclave = $producto->numero_parte;
            if (!$producto->isValid()) {
                return false;
            }

            return true;
        });
        Producto::created(function (Producto $producto) {
            $producto->upc || $producto->upc = $producto->id;
            return $producto->save();
        });
        Producto::updating(function ($producto) {
            $producto->updateRules = self::$rules;
            $producto->updateRules['clave'] .= ',clave,' . $producto->id;
            $producto->updateRules['upc'] .= ',upc,' . $producto->id;

            return $producto->isValid('update');
        });
        Producto::updated(function ($producto) {
            event(new ProductoActualizado($producto));
        });
        Producto::created(function ($producto) {
            Event::fire(new ProductoCreado($producto));
        });
    }

    /**
     * Hace las operaciones correspondientes para guardar los datos del producto, inicializar sus existencias,
     * guardar sus precios por sucursal considerando que son iguales por proveedor, así como también guarda
     * los datos asociados en sus dimensiones.
     * @param array $parameters
     * @return bool
     */
    public function guardarNuevo($parameters) {
        if (!empty($parameters['producto'])) {
            $this->fill($parameters['producto']);
        }
        $dimension = new Dimension($parameters['dimension']);
        $precio = new Precio($parameters['precio']);
        $dimension->producto_id = 0;
        $precio->producto_sucursal_id = 0;

        if ($this->isValid() && $dimension->isValid() && $precio->isValid()) {
            $this->save();
            $this->attachDimension($dimension);
            $this->guardarPrecios($precio);

            return true;
        } else {
            $this->errors || $this->errors = new MessageBag();
            if ($dimension->errors) {
                $this->errors->merge($dimension->errors);
            }
            if ($precio->errors) {
                $this->errors->merge($precio->errors);
            }

            return false;
        }
    }

    /**
     * Función que hace las operaciones necesarias para la actualización de datos del producto
     * @param array $parameters
     * @return bool
     */
    public function actualizar($parameters) {
        DB::beginTransaction();
        if ($this->update($parameters)
            && $this->dimension->update($parameters['dimension'])
            && (empty($precios_errores = $this->actualizarPreciosPorProveedor($parameters)))
        ) {
            DB::commit();

            return true;
        } else {
            $this->errors || $this->errors = new MessageBag();
            if ($this->dimension->errors) {
                $this->errors->merge($this->dimension->errors);
            }
            if ($precios_errores) {
                $this->errors->merge(['Precios' => $precios_errores]);
            }
            DB::rollback();

            return false;
        }
    }

    /**
     * Invocara la modificacion de existencias para mandarlas a pretransferencia
     *
     * @param array $data
     * @return bool
     */
    public function pretransferir($data) {
        $lambda = function () use ($data) {
            if (empty($data)) {
                return false;
            }
            $sucursalOrigen = $this->originPretransferencias($data);
            $dataPretransferencia = $this->purgePretransferencias($data);
            $empleado = $this->creadorPretransferencia($data);

            foreach ($dataPretransferencia as $pretransferencia) {
                $result = Event::fire(new Pretransferir($this, $pretransferencia, $sucursalOrigen, $empleado))[0][0];
                if (!$result) {
                    return false;
                }
            }

            return true;
        };

        return $this->safe_transaction($lambda);
    }

    /**
     * Agrega una sucursal para un producto
     * @param App\Sucursal
     * @return void
     */
    public function addSucursal($sucursal) {
        $this->sucursales()->attach($sucursal->id);
    }

    /**
     * Gets the Tipo Garantia associated with Producto
     * @return \App\TipoGarantia
     */
    public function tipoGarantia() {
        return $this->belongsTo('App\TipoGarantia', 'tipo_garantia_id');
    }

    /**
     * Gets the Marca associated with Producto
     * @return \App\Marca
     */
    public function marca() {
        return $this->belongsTo('App\Marca', 'marca_id');
    }

    /**
     * Gets the Marge associated with Producto
     * @return \App\Margen
     */
    public function margen() {
        return $this->belongsTo('App\Margen', 'margen_id');
    }

    /**
     * Get the Unidad associated with Producto
     * @return \App\Unidad
     */
    public function unidad() {
        return $this->belongsTo('App\Unidad');
    }

    /**
     * Get the Subfamilia associated with Producto
     * @return \App\Subfamilia
     */
    public function subfamilia() {
        return $this->belongsTo('App\Subfamilia');
    }

    /**
     * Obtiene la Dimension de Producto
     * @return \App\Dimension
     */
    public function dimension() {
        return $this->hasOne('App\Dimension');
    }

    /**
     * Obtiene los productos_movimientos de todas las sucursales relacionados con el Producto
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function movimientos(Sucursal $sucursal = null) {
        if (is_null($sucursal)) {
            return $this->hasManyThrough('App\ProductoMovimiento', 'App\ProductoSucursal',
                'producto_id', 'producto_sucursal_id');
        } else {
            return $this->productosSucursales()->where('sucursal_id', $sucursal->id)->first()->movimientos;
        }
    }

    /**
     * Obtiene los productos_sucursales relacionados con el Producto
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function productosSucursales() {
        return $this->hasMany('App\ProductoSucursal');
    }

    /**
     * Obtiene las sucursales relacionadas con el Producto
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sucursales() {
        return $this->belongsToMany('App\Sucursal', 'productos_sucursales',
            'producto_id', 'sucursal_id')->withPivot('id');
    }

    /**
     * Obtiene los proveedores relacionados con el Producto
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function proveedores() {
        return $this->sucursales()->with('proveedor')->get()->pluck('proveedor')->unique();
    }

    /**
     * Obtiene las existencias relacionadas con el Producto
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function existencias(Sucursal $sucursal = null) {
        if (is_null($sucursal)) {
            return $this->hasManyThrough('App\Existencia', 'App\ProductoSucursal',
                'producto_id', 'productos_sucursales_id');
        } else {
            return $this->productosSucursales()->where('sucursal_id', $sucursal->id)->first()->existencia;
        }
    }

    public function precios() {
        return $this->hasManyThrough('App\Precio', 'App\ProductoSucursal',
            'producto_id', 'producto_sucursal_id');
    }

    /**
     * Obtiene la ficha asociada a este producto
     * @return \App\Ficha
     */
    public function ficha() {
        return $this->hasOne('App\Ficha');
    }

    /**
     * Obtiene las características de la ficha asociada a este producto
     * alias a $producto->ficha->caracteristicas
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function fichaCaracteristicas() {
        return $this->ficha->caracteristicas();
    }

    /**
     * Obtiene las Entradas Detalles asociadas con el Producto
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function entradasDetalles() {
        return $this->hasMany('App\EntradaDetalle', 'producto_id');
    }


    /**
     * Obtiene las Salidas Detalles asociadas con el Producto
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function salidasDetalles() {
        return $this->hasMany('App\SalidaDetalle', 'producto_id');
    }


    /**
     * Obtiene las Transferencias Detalles asociadas con el Producto
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function transferenciasDetalles() {
        return $this->hasMany('App\TransferenciaDetalle', 'producto_id');
    }


    /**
     * Obtiene los Apartados Detalles asociados con el Producto
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function apartadosDetalles() {
        return $this->hasMany('App\ApartadoDetalle', 'producto_id');
    }

    /**
     * Obtiene las reposiciones del producto
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function reposiciones() {
        return $this->hasMany('App\Reposicion');
    }


    /**
     * Obtiene las Pretransferencias asociadas con el Producto
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function pretransferencias() {
        return $this->hasMany('App\Pretransferencia', 'producto_id');
    }

    /**
     * Obtienes los precios agrupados por proveedor
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function preciosProveedor() {
        return $this->productosSucursales()
            ->join('precios', 'precios.producto_sucursal_id', '=', 'productos_sucursales.id')
            ->join('sucursales', 'productos_sucursales.sucursal_id', '=', 'sucursales.id')
            ->join('proveedores', 'sucursales.proveedor_id', '=', 'proveedores.id')
            ->select('proveedores.id AS proveedor_id', 'proveedores.clave', 'proveedores.externo', 'precios.costo', 'precios.precio_1',
                'precios.precio_2', 'precios.precio_3', 'precios.precio_4', 'precios.precio_5', 'precios.precio_6',
                'precios.precio_7', 'precios.precio_8', 'precios.precio_9', 'precios.precio_10', 'precios.descuento', 'precios.revisado')
            ->groupBy('proveedores.id')
            ->get();
    }

    /**
     * @param Precio $precio_interno
     */
    private function guardarPrecios($precio_interno) {
        $precio_externo = $precio_interno->calcularPrecios($precio_interno->precio_1, $precio_interno->costo, true);
        $precio_externo = new Precio($precio_externo['precios']);
        $precio_externo->revisado = $precio_interno->revisado;
        $precio_externo->descuento = $precio_interno->descuento;
        foreach ($this->productosSucursales as $producto_sucursal) {
            if ($producto_sucursal->sucursal->proveedor->externo) {
                $producto_sucursal->precio()->save(clone $precio_externo);
            } else {
                $producto_sucursal->precio()->save(clone $precio_interno);
            }
        }
    }

    private function actualizarPreciosPorProveedor($parameters) {
        $precios_proveedor = $parameters['precios'];
        $errors = [];
        foreach ($precios_proveedor as $precio_proveedor) {
            $precio_proveedor['revisado'] = boolval($parameters['revisado']);
            $sucursales_id = Sucursal::whereProveedorId($precio_proveedor['proveedor_id'])->get()->pluck('id');
            $productos_sucursales = $this->productosSucursales()->with('precio')->whereIn('sucursal_id', $sucursales_id)->get();

            foreach ($productos_sucursales as $producto_sucursal) {
                if (!$producto_sucursal->precio->update($precio_proveedor)) {
                    foreach ($producto_sucursal->precio->errors->toArray() as $err) {
                        if (!in_array($err, $errors)) {
                            array_push($errors, $err);
                        }
                    }
                }
            }
        }

        return $errors;
    }

    private function originPretransferencias($data) {
        $arr = array_values(array_filter($data, function ($element) {
            return !empty($element['sucursal_origen']);
        }))[0];

        return Sucursal::findOrFail($arr['sucursal_origen']);
    }

    /**
     * Remueve del array los objetos que tengan una pretransferencia menor o
     * igual a cero
     */
    private function purgePretransferencias($data) {
        return array_filter($data, function ($element) {
            return !empty($element['pretransferencia']);
        });
    }

    private function creadorPretransferencia($data) {
        $arr = array_values(array_filter($data, function ($element) {
            return !empty($element['empleado_id']);
        }))[0];

        return Empleado::findOrFail($arr['empleado_id']);
    }

    private function attachDimension($dimension) {
        $this->dimension()->save($dimension);
    }

}
