<?php

namespace App;

use App\EstadoSalida;
use App\Producto;
use App\ProductoMovimiento;
use App\Sucursal;
use Sagd\SafeTransactions;

/**
 * App\Salida
 *
 * @property integer $id
 * @property string $fecha_salida
 * @property string $motivo
 * @property integer $empleado_id
 * @property integer $sucursal_id
 * @property integer $estado_salida_id
 * @property-read \App\Empleado $empleado
 * @property-read \App\Sucursal $sucursal
 * @property-read \App\EstadoSalida $estado
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SalidaDetalle[] $detalles
 * @method static \Illuminate\Database\Query\Builder|\App\Salida whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Salida whereFechaSalida($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Salida whereMotivo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Salida whereEmpleadoId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Salida whereSucursalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Salida whereEstadoSalidaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LGGModel last()
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\Salida whereDeletedAt($value)
 */
class Salida extends LGGModel {

    use SafeTransactions;

    //
    protected $table = "salidas";
    public $timestamps = false;
    protected $fillable = ['fecha_salida', 'motivo',
        'empleado_id', 'sucursal_id', 'estado_salida_id'];

    public static $rules = [
        'fecha_salida'     => 'date',
        'motivo'           => 'required|max:255',
        'empleado_id'      => 'required|integer',
        'sucursal_id'      => 'required|integer',
        'estado_salida_id' => 'required|integer',
    ];
    public $updateRules = [];

    /**
     * Define the model hooks
     * @codeCoverageIgnore
     */
    public static function boot() {
        parent::boot();
        Salida::creating(function ($model) {
            return $model->isValid();
        });
        Salida::updating(function ($model) {
            $model->updateRules = self::$rules;

            return $model->isValid('update');
        });
    }

    /**
     * Crear un detalle asociado a la salida
     * @param array $detalle
     * @return SalidaDetalle | false
     */
    public function crearDetalle($detalle)
    {
        if (! is_null($detalle['upc'])) { unset($detalle['upc']); }

        $salidaDetalle = new SalidaDetalle();
        $salidaDetalle->fill($detalle);
        if ($this->detalles->contains('producto_id', $salidaDetalle->producto_id)) {
            $salidaDetalleOriginal = $this->detalles()->where('producto_id', $salidaDetalle->producto_id)->first();
            $salidaDetalleOriginal->cantidad += $salidaDetalle->cantidad;
            return $salidaDetalleOriginal->save() ? $salidaDetalleOriginal : false;
        }
        return $this->detalles()->save($salidaDetalle);
    }

    /**
     * Quita un detalle asociado a la salida
     * @param int $detalle_id
     * @return bool
     */
    public function quitarDetalle($detalle_id)
    {
        return (SalidaDetalle::destroy($detalle_id) > 0);
    }

    /**
     * Carga los detalles para actualizar existencias
     * @return bool
     */
    public function cargar()
    {
        $lambda = function() {
            if($this->noCargado()) {
                foreach ($this->detalles()->get() as $detalle) {
                    if (! $detalle->cargar()) {
                        return false;
                    }
                }
                $this->finalizarCarga();
                return true;
            } else {
                return false;
            }
        };
        return $this->safe_transaction($lambda);
    }

    /**
     * Verifica que el estado no se encuentre como Cargado
     * @return bool
     */
    public function noCargado()
    {
        return $this->estado->nombre != 'Cargado';
    }

    /**
     * Establece el Estado de la Salida a Cargado
     * @return bool
     */
    public function finalizarCarga()
    {
        $this->estado()->associate(EstadoSalida::cargado());
        $this->save();
    }

    /**
     * Verifica que ningun detalle tenga cantidad mayor a sus existencias
     * @return bool
     */
    public function sobrepasaExistencias()
    {
        foreach ($this->detalles()->get() as $detalle) {
            $existencia = $detalle->producto->existencias($this->sucursal)->cantidad;
            if ($detalle->cantidad > $existencia) {
                return true;
            }
        }
        return false;
    }

    /**
     * Obtiene el Empleado asociado con la Salida
     * @return App\Empleado
     */
    public function empleado() {
        return $this->belongsTo('App\Empleado', 'empleado_id');
    }


    /**
     * Obtiene la Sucursal asociada con la Salida
     * @return App\Sucursal
     */
    public function sucursal() {
        return $this->belongsTo('App\Sucursal', 'sucursal_id');
    }


    /**
     * Obtiene el Estado asociado con la Salida
     * @return App\EstadoSalida
     */
    public function estado() {
        return $this->belongsTo('App\EstadoSalida', 'estado_salida_id');
    }


    /**
     * Obtiene las Salidas Detalles asociadas con la Salida
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function detalles() {
        return $this->hasMany('App\SalidaDetalle', 'salida_id');
    }
}
