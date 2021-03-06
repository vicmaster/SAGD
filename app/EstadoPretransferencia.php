<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\EstadoPretransferencia
 *
 * @property integer $id
 * @property string $nombre
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\EstadoPretransferencia whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EstadoPretransferencia whereNombre($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EstadoPretransferencia whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LGGModel last()
 */
class EstadoPretransferencia extends LGGModel
{
    protected $table = "estados_pretransferencias";
    public $timestamps = false;
    protected $fillable = ['nombre'];

    public static $rules = [
        'nombre' => 'required|max:45'
    ];
    public $updateRules = [];

    /**
     * Define the model hooks
     * @codeCoverageIgnore
     */
    public static function boot() {
        parent::boot();
        EstadoPretransferencia::creating(function ($model) {
            return $model->isValid();
        });
        EstadoPretransferencia::updating(function ($model) {
            $model->updateRules = self::$rules;

            return $model->isValid('update');
        });
    }

    /**
     * Obtiene el id del estado que es Sin Transferir
     */
    public static function sinTransferir()
    {
        return EstadoPretransferencia::where('nombre', 'Sin Transferir')->first()->id;
    }

    /**
     * Obtiene el id del estado que es Transferido
     */
    public static function transferido()
    {
        return EstadoPretransferencia::where('nombre', 'Transferido')->first()->id;
    }
}
